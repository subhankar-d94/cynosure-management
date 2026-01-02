<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }


        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // For AJAX requests, return JSON data
        if ($request->ajax()) {
            $suppliers = $query->paginate(10);
            return response()->json([
                'suppliers' => $suppliers->items(),
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
            ]);
        }

        $suppliers = $query->paginate(15);

        // Calculate statistics
        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('status', 'active')->count(),
            'inactive_suppliers' => Supplier::where('status', 'inactive')->count(),
            'pending_suppliers' => Supplier::where('status', 'pending')->count(),
            'total_purchases' => 0, // Placeholder - will be calculated from purchases table if needed
            'monthly_purchases' => 0, // Placeholder
            'top_rated_suppliers' => 0, // Removed rating system
            'total_materials' => 0, // Removed materials tracking
            'avg_rating' => 0, // Removed rating system
            'overdue_suppliers' => 0, // Removed
            'by_category' => Supplier::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
        ];

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255|unique:suppliers',
            'contact_person' => 'required|string|max:255',
            'category' => 'required|string|in:raw_materials,manufacturing,technology,services,logistics,packaging,maintenance,consulting',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'status' => 'nullable|string|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $supplierData = $request->only([
                'company_name',
                'contact_person',
                'category',
                'email',
                'phone',
                'gst_number',
                'address',
                'website',
                'status',
            ]);

            // Set default status if not provided
            $supplierData['status'] = $supplierData['status'] ?? 'active';

            $supplier = Supplier::create($supplierData);

            DB::commit();

            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Supplier created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error creating supplier: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        // Load related purchases
        $supplier->load(['purchases' => function($query) {
            $query->latest()->limit(5);
        }]);

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255|unique:suppliers,company_name,' . $supplier->id,
            'contact_person' => 'required|string|max:255',
            'category' => 'required|string|in:raw_materials,manufacturing,technology,services,logistics,packaging,maintenance,consulting',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'status' => 'required|string|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $supplierData = $request->only([
                'company_name',
                'contact_person',
                'category',
                'email',
                'phone',
                'gst_number',
                'address',
                'website',
                'status',
            ]);

            $supplier->update($supplierData);

            DB::commit();

            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Supplier updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error updating supplier: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            $supplier->delete();

            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error deleting supplier: ' . $e->getMessage());
        }
    }

    /**
     * Display supplier purchases.
     */
    public function purchases(Request $request, Supplier $supplier = null)
    {
        // For demo purposes, we'll use mock data until purchase_orders table exists
        $purchases = collect([]);

        // Calculate statistics using safe queries
        $stats = [
            'totalPurchases' => 156,
            'totalValue' => 890450,
            'pendingOrders' => 12,
            'avgDelivery' => 14,
            'monthlyValue' => 125680,
            'monthlyOrders' => 28,
            'paidAmount' => 745230,
            'pendingAmount' => 95420,
            'overdueAmount' => 49800,
        ];

        return view('suppliers.purchases', compact('supplier', 'purchases', 'stats'));
    }

    /**
     * Display supplier materials.
     */
    public function materials(Request $request, Supplier $supplier = null)
    {
        // For demo purposes, we'll use mock data
        $materials = collect([]);

        // Calculate statistics
        $stats = [
            'totalMaterials' => 127,
            'availableMaterials' => 98,
            'categoriesCount' => 8,
            'avgPrice' => 125.50,
            'newMaterials' => 12,
        ];

        return view('suppliers.materials', compact('supplier', 'materials', 'stats'));
    }

    /**
     * Display supplier performance metrics.
     */
    public function performance(Request $request, Supplier $supplier = null)
    {
        // For demo purposes, we'll use mock data
        $performanceData = [
            'overallScore' => 87,
            'qualityScore' => 92,
            'deliveryScore' => 85,
            'responseTime' => 12,
            'costSavings' => 24500,
            'complianceScore' => 94,
            'onTimeDelivery' => 85,
            'qualityRating' => 92,
            'costCompetitiveness' => 78,
            'communicationScore' => 88,
            'innovationScore' => 72,
            'sustainabilityScore' => 65,
        ];

        return view('suppliers.performance', compact('supplier', 'performanceData'));
    }

    /**
     * Bulk operations on suppliers.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:activate,deactivate,delete',
            'supplier_ids' => 'required|array',
            'supplier_ids.*' => 'exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid request data'], 400);
        }

        try {
            DB::beginTransaction();

            $supplierIds = $request->supplier_ids;
            $action = $request->action;

            switch ($action) {
                case 'activate':
                    Supplier::whereIn('id', $supplierIds)->update(['status' => 'active']);
                    $message = 'Suppliers activated successfully';
                    break;

                case 'deactivate':
                    Supplier::whereIn('id', $supplierIds)->update(['status' => 'inactive']);
                    $message = 'Suppliers deactivated successfully';
                    break;

                case 'delete':
                    Supplier::whereIn('id', $supplierIds)->delete();
                    $message = 'Suppliers deleted successfully';
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Error performing bulk action'], 500);
        }
    }

    /**
     * Export suppliers data.
     */
    public function export(Request $request)
    {
        try {
            $suppliers = Supplier::select([
                'company_name',
                'contact_person',
                'category',
                'email',
                'phone',
                'gst_number',
                'address',
                'website',
                'status',
                'created_at'
            ])->get();

            $filename = 'suppliers_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($suppliers) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'Company Name',
                    'Contact Person',
                    'Category',
                    'Email',
                    'Phone',
                    'GST Number',
                    'Address',
                    'Website',
                    'Status',
                    'Created Date'
                ]);

                // CSV data
                foreach ($suppliers as $supplier) {
                    fputcsv($file, [
                        $supplier->company_name,
                        $supplier->contact_person,
                        $supplier->category,
                        $supplier->email,
                        $supplier->phone,
                        $supplier->gst_number,
                        $supplier->address,
                        $supplier->website,
                        $supplier->status,
                        $supplier->created_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error exporting suppliers: ' . $e->getMessage());
        }
    }

    /**
     * Get supplier statistics for dashboard.
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_suppliers' => Supplier::count(),
                'active_suppliers' => Supplier::where('status', 'active')->count(),
                'inactive_suppliers' => Supplier::where('status', 'inactive')->count(),
                'pending_suppliers' => Supplier::where('status', 'pending')->count(),
                'new_this_month' => Supplier::whereMonth('created_at', now()->month)->count(),
                'total_purchases' => 0, // Removed - no longer tracked in supplier table
                'monthly_purchases' => 0, // Removed - no longer tracked
                'top_rated_suppliers' => 0, // Removed rating system
                'total_materials' => 0, // Removed materials tracking
                'avg_rating' => 0, // Removed rating system
                'overdue_suppliers' => 0, // Removed
                'top_categories' => Supplier::select('category', DB::raw('count(*) as count'))
                    ->groupBy('category')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->get(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching statistics'], 500);
        }
    }
}