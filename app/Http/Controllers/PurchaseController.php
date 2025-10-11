<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Purchase::withRelations();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('urgent')) {
            $query->where('urgent', $request->boolean('urgent'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purchase_order_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics for dashboard cards
        $stats = $this->getPurchaseStatistics();

        // Get filter options
        $suppliers = Supplier::select('id', 'company_name')->get();

        return view('purchases.index', compact('purchases', 'stats', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('status', 'active')
                          ->orderBy('name')
                          ->get();

        return view('purchases.create', compact('suppliers'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:purchase_date',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'currency' => 'required|string|size:3',
            'payment_terms' => 'required|string|max:255',
            'delivery_terms' => 'required|string|max:255',
            'delivery_address' => 'nullable|string',
            'delivery_city' => 'nullable|string|max:255',
            'delivery_state' => 'nullable|string|max:255',
            'delivery_pincode' => 'nullable|string|max:10',
            'delivery_country' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:100',
            'budget_code' => 'nullable|string|max:100',
            'urgent' => 'boolean',
            'requires_approval' => 'boolean',
            'approval_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.sku' => 'nullable|string|max:100',
            'items.*.unit' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string|max:500',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('purchases/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }
            $validated['attachments'] = $attachments;

            // Set created_by
            $validated['created_by'] = Auth::id();

            // Create purchase order
            $purchase = Purchase::create($validated);

            // Create purchase items
            foreach ($validated['items'] as $itemData) {
                $itemData['purchase_id'] = $purchase->id;
                PurchaseItem::create($itemData);
            }

            // Calculate totals
            $purchase->calculateTotals();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order created successfully',
                    'purchase' => $purchase->load('supplier', 'items'),
                    'redirect_url' => route('purchases.show', $purchase)
                ]);
            }

            return redirect()->route('purchases.show', $purchase)
                           ->with('success', 'Purchase order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create purchase order: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => 'Failed to create purchase order: ' . $e->getMessage()]);
        }
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'items',
            'approver',
            'creator',
            'updater',
            'canceller',
            'receiver',
            'qualityChecker'
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View|RedirectResponse
    {
        if (!$purchase->can_be_edited) {
            return redirect()->route('purchases.show', $purchase)
                           ->with('error', 'This purchase order cannot be edited in its current status');
        }

        $suppliers = Supplier::where('status', 'active')
                          ->orderBy('name')
                          ->get();

        $purchase->load('items');

        return view('purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase): JsonResponse|RedirectResponse
    {
        if (!$purchase->can_be_edited) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This purchase order cannot be edited in its current status'
                ], 403);
            }

            return redirect()->route('purchases.show', $purchase)
                           ->with('error', 'This purchase order cannot be edited in its current status');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:purchase_date',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'currency' => 'required|string|size:3',
            'payment_terms' => 'required|string|max:255',
            'delivery_terms' => 'required|string|max:255',
            'delivery_address' => 'nullable|string',
            'delivery_city' => 'nullable|string|max:255',
            'delivery_state' => 'nullable|string|max:255',
            'delivery_pincode' => 'nullable|string|max:10',
            'delivery_country' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:100',
            'budget_code' => 'nullable|string|max:100',
            'urgent' => 'boolean',
            'requires_approval' => 'boolean',
            'approval_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_items,id',
            'items.*.description' => 'required|string|max:500',
            'items.*.sku' => 'nullable|string|max:100',
            'items.*.unit' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string|max:500',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'new_attachments' => 'nullable|array',
            'new_attachments.*' => 'file|max:10240',
            'remove_attachments' => 'nullable|array',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $attachments = $purchase->attachments ?? [];

            // Remove attachments
            if ($request->filled('remove_attachments')) {
                foreach ($request->remove_attachments as $index) {
                    if (isset($attachments[$index])) {
                        Storage::disk('public')->delete($attachments[$index]['path']);
                        unset($attachments[$index]);
                    }
                }
                $attachments = array_values($attachments); // Re-index array
            }

            // Add new attachments
            if ($request->hasFile('new_attachments')) {
                foreach ($request->file('new_attachments') as $file) {
                    $path = $file->store('purchases/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }
            $validated['attachments'] = $attachments;

            // Set updated_by
            $validated['updated_by'] = Auth::id();

            // Increment version
            $validated['version'] = $purchase->version + 1;

            // Update purchase order
            $purchase->update($validated);

            // Update purchase items
            $existingItemIds = collect($validated['items'])
                              ->pluck('id')
                              ->filter()
                              ->toArray();

            // Delete items that are no longer present
            $purchase->items()->whereNotIn('id', $existingItemIds)->delete();

            // Update or create items
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Update existing item
                    PurchaseItem::where('id', $itemData['id'])
                               ->where('purchase_id', $purchase->id)
                               ->update($itemData);
                } else {
                    // Create new item
                    $itemData['purchase_id'] = $purchase->id;
                    PurchaseItem::create($itemData);
                }
            }

            // Recalculate totals
            $purchase->calculateTotals();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order updated successfully',
                    'purchase' => $purchase->load('supplier', 'items')
                ]);
            }

            return redirect()->route('purchases.show', $purchase)
                           ->with('success', 'Purchase order updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update purchase order: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update purchase order: ' . $e->getMessage()]);
        }
    }

    public function destroy(Purchase $purchase): JsonResponse|RedirectResponse
    {
        if (!$purchase->can_be_edited) {
            return response()->json([
                'success' => false,
                'message' => 'This purchase order cannot be deleted in its current status'
            ], 403);
        }

        try {
            // Delete associated files
            if ($purchase->attachments) {
                foreach ($purchase->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            $purchase->delete();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Purchase $purchase): JsonResponse
    {
        if (!$purchase->can_be_approved) {
            return response()->json([
                'success' => false,
                'message' => 'This purchase order cannot be approved'
            ], 403);
        }

        try {
            $purchase->approve(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Purchase order approved successfully',
                'purchase' => $purchase->fresh(['supplier', 'approver'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, Purchase $purchase): JsonResponse
    {
        if (!$purchase->can_be_cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'This purchase order cannot be cancelled'
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $purchase->cancel(Auth::id(), $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Purchase order cancelled successfully',
                'purchase' => $purchase->fresh(['supplier', 'canceller'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receive(Request $request, Purchase $purchase): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_items,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'quality_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Update received quantities for items
            foreach ($request->items as $itemData) {
                PurchaseItem::where('id', $itemData['id'])
                           ->where('purchase_id', $purchase->id)
                           ->update(['received_quantity' => $itemData['received_quantity']]);
            }

            // Check if all items are fully received
            $purchase->refresh();
            $totalItems = $purchase->total_items;
            $receivedItems = $purchase->received_items;

            if ($receivedItems >= $totalItems) {
                $purchase->markAsReceived(Auth::id());
            } else {
                $purchase->update([
                    'status' => Purchase::STATUS_PARTIAL_RECEIVED,
                    'received_by' => Auth::id(),
                    'received_at' => now()
                ]);
            }

            // Update quality notes if provided
            if ($request->filled('quality_notes')) {
                $purchase->update([
                    'quality_notes' => $request->quality_notes,
                    'quality_checked_by' => Auth::id(),
                    'quality_checked_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order receiving updated successfully',
                'purchase' => $purchase->fresh(['supplier', 'items', 'receiver'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update receiving: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'items']);
        return view('purchases.print', compact('purchase'));
    }

    public function duplicate(Purchase $purchase): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $newPurchase = $purchase->replicate();
            $newPurchase->purchase_order_number = null; // Will be auto-generated
            $newPurchase->status = Purchase::STATUS_DRAFT;
            $newPurchase->version = 1;
            $newPurchase->parent_id = null;
            $newPurchase->approved_by = null;
            $newPurchase->approved_at = null;
            $newPurchase->received_by = null;
            $newPurchase->received_at = null;
            $newPurchase->cancelled_by = null;
            $newPurchase->cancelled_at = null;
            $newPurchase->cancelled_reason = null;
            $newPurchase->created_by = Auth::id();
            $newPurchase->updated_by = null;
            $newPurchase->save();

            // Duplicate items
            foreach ($purchase->items as $item) {
                $newItem = $item->replicate();
                $newItem->purchase_id = $newPurchase->id;
                $newItem->received_quantity = 0;
                $newItem->save();
            }

            $newPurchase->calculateTotals();

            DB::commit();

            return redirect()->route('purchases.edit', $newPurchase)
                           ->with('success', 'Purchase order duplicated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to duplicate purchase order: ' . $e->getMessage());
        }
    }

    public function export()
    {
        // TODO: Implement export functionality (Excel, PDF, CSV)
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented soon'
        ]);
    }

    public function bulkActions(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,cancel,delete,export',
            'purchase_ids' => 'required|array',
            'purchase_ids.*' => 'exists:purchase_orders,id',
            'reason' => 'required_if:action,cancel|string|max:500'
        ]);

        try {
            $purchases = Purchase::whereIn('id', $request->purchase_ids)->get();
            $results = ['success' => 0, 'failed' => 0, 'messages' => []];

            foreach ($purchases as $purchase) {
                try {
                    switch ($request->action) {
                        case 'approve':
                            if ($purchase->can_be_approved) {
                                $purchase->approve(Auth::id());
                                $results['success']++;
                            } else {
                                $results['failed']++;
                                $results['messages'][] = "PO #{$purchase->purchase_order_number} cannot be approved";
                            }
                            break;

                        case 'cancel':
                            if ($purchase->can_be_cancelled) {
                                $purchase->cancel(Auth::id(), $request->reason);
                                $results['success']++;
                            } else {
                                $results['failed']++;
                                $results['messages'][] = "PO #{$purchase->purchase_order_number} cannot be cancelled";
                            }
                            break;

                        case 'delete':
                            if ($purchase->can_be_edited) {
                                if ($purchase->attachments) {
                                    foreach ($purchase->attachments as $attachment) {
                                        Storage::disk('public')->delete($attachment['path']);
                                    }
                                }
                                $purchase->delete();
                                $results['success']++;
                            } else {
                                $results['failed']++;
                                $results['messages'][] = "PO #{$purchase->purchase_order_number} cannot be deleted";
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['messages'][] = "PO #{$purchase->purchase_order_number}: " . $e->getMessage();
                }
            }

            $message = "Bulk action completed. {$results['success']} successful, {$results['failed']} failed.";
            if (!empty($results['messages'])) {
                $message .= " Errors: " . implode('; ', $results['messages']);
            }

            return response()->json([
                'success' => $results['failed'] === 0,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPurchaseStatistics(): array
    {
        return [
            'total' => Purchase::count(),
            'draft' => Purchase::byStatus(Purchase::STATUS_DRAFT)->count(),
            'pending' => Purchase::byStatus(Purchase::STATUS_PENDING)->count(),
            'approved' => Purchase::byStatus(Purchase::STATUS_APPROVED)->count(),
            'ordered' => Purchase::byStatus(Purchase::STATUS_ORDERED)->count(),
            'received' => Purchase::byStatus(Purchase::STATUS_RECEIVED)->count(),
            'overdue' => Purchase::overdue()->count(),
            'total_value' => Purchase::sum('total_amount'),
            'this_month' => Purchase::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
        ];
    }
}