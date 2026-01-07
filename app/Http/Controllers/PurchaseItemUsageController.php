<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\PurchaseItemUsageHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseItemUsageController extends Controller
{
    /**
     * Record usage for a single purchase item
     */
    public function recordUsage(Request $request, PurchaseItem $purchaseItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity_used' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate not exceeding remaining quantity
        if ($request->quantity_used > $purchaseItem->remaining_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Usage quantity cannot exceed remaining quantity (' . $purchaseItem->remaining_quantity . ')'
            ], 422);
        }

        $success = $purchaseItem->recordUsage(
            $request->quantity_used,
            $request->notes,
            auth()->id()
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Usage recorded successfully',
                'data' => $purchaseItem->fresh(['purchase', 'usageHistory'])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to record usage'
        ], 500);
    }

    /**
     * Get usage history for a purchase item
     */
    public function getUsageHistory(PurchaseItem $purchaseItem): JsonResponse
    {
        $history = $purchaseItem->usageHistory()
            ->with('recordedBy:id,name')
            ->orderBy('usage_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'purchase_item' => $purchaseItem->load('purchase'),
                'history' => $history
            ]
        ]);
    }

    /**
     * Bulk record usage for multiple purchase items (end-of-day updates)
     */
    public function bulkRecordUsage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'usage_records' => 'required|array|min:1',
            'usage_records.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'usage_records.*.quantity_used' => 'required|numeric|min:0',
            'usage_records.*.notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $results = [];
        $errors = [];

        foreach ($request->usage_records as $record) {
            try {
                $purchaseItem = PurchaseItem::find($record['purchase_item_id']);

                if ($record['quantity_used'] > $purchaseItem->remaining_quantity) {
                    $errors[] = [
                        'purchase_item_id' => $purchaseItem->id,
                        'description' => $purchaseItem->description,
                        'message' => "Usage ({$record['quantity_used']}) exceeds remaining quantity ({$purchaseItem->remaining_quantity})"
                    ];
                    continue;
                }

                $success = $purchaseItem->recordUsage(
                    $record['quantity_used'],
                    $record['notes'] ?? null,
                    auth()->id()
                );

                if ($success) {
                    $results[] = [
                        'purchase_item_id' => $purchaseItem->id,
                        'description' => $purchaseItem->description,
                        'quantity_used' => $record['quantity_used'],
                        'remaining_quantity' => $purchaseItem->remaining_quantity
                    ];
                } else {
                    $errors[] = [
                        'purchase_item_id' => $purchaseItem->id,
                        'description' => $purchaseItem->description,
                        'message' => 'Failed to record usage'
                    ];
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'purchase_item_id' => $record['purchase_item_id'],
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => count($errors) === 0,
            'message' => count($results) . ' usage record(s) created' .
                        (count($errors) > 0 ? ', ' . count($errors) . ' error(s)' : ''),
            'data' => [
                'successful' => $results,
                'failed' => $errors
            ]
        ], count($errors) > 0 ? 422 : 200);
    }

    /**
     * Get purchase items with remaining quantity for usage tracking
     */
    public function getItemsForUsage(Request $request): JsonResponse
    {
        $query = PurchaseItem::with(['purchase.supplier'])
            ->whereHas('purchase', function ($q) {
                $q->whereIn('status', ['received', 'partial_received', 'completed']);
            })
            ->where('remaining_quantity', '>', 0);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('purchase_id', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }
}
