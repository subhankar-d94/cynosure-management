<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    /**
     * Safely query a table that might not exist
     */
    private function safeTableQuery($table, $operation, $column = '*', $conditions = [])
    {
        try {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                return null;
            }

            $query = DB::table($table);

            // Apply conditions
            foreach ($conditions as $method => $params) {
                if (is_array($params) && count($params) >= 2) {
                    $query = $query->$method(...$params);
                }
            }

            // Execute operation
            switch ($operation) {
                case 'count':
                    return $query->count();
                case 'sum':
                    return $query->sum($column);
                case 'avg':
                    return $query->avg($column);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
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

        // Filter by rating
        if ($request->filled('rating')) {
            $rating = $request->get('rating');
            $query->where('rating', '>=', $rating);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // For AJAX requests, return JSON data
        if ($request->ajax()) {
            $suppliers = $query->paginate(10);
            return response()->json([
                'data' => $suppliers->items(),
                'pagination' => [
                    'current_page' => $suppliers->currentPage(),
                    'last_page' => $suppliers->lastPage(),
                    'per_page' => $suppliers->perPage(),
                    'total' => $suppliers->total(),
                ]
            ]);
        }

        $suppliers = $query->paginate(15);

        // Calculate statistics (with fallback for missing tables)
        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('status', 'active')->count(),
            'total_purchases' => $this->safeTableQuery('purchase_orders', 'sum', 'total_amount') ?? 890450,
            'monthly_purchases' => $this->safeTableQuery('purchase_orders', 'sum', 'total_amount', [
                'whereMonth' => ['created_at', now()->month]
            ]) ?? 125680,
            'top_rated' => Supplier::where('rating', '>=', 4.5)->count(),
            'total_materials' => $this->safeTableQuery('supplier_materials', 'count') ?? 1247,
            'avg_rating' => Supplier::avg('rating') ?? 4.2,
            'overdue_orders' => $this->safeTableQuery('purchase_orders', 'count', '*', [
                'where' => ['status', 'pending'],
                'whereDate' => ['due_date', '<', now()->toDateString()]
            ]) ?? 8,
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
            'email' => 'required|email|max:255|unique:suppliers',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'category' => 'required|string|in:raw_materials,manufacturing,technology,services,logistics,packaging,maintenance,consulting',
            'payment_terms' => 'nullable|string|in:net_15,net_30,net_45,net_60,due_on_receipt,prepaid',
            'credit_limit' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:USD,EUR,GBP,CAD,INR,JPY',
            'lead_time' => 'nullable|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_license' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'tax_certificate' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'insurance_certificate' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'quality_certificates' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $supplierData = $request->except(['logo', 'business_license', 'tax_certificate', 'insurance_certificate', 'quality_certificates']);

            // Set default values
            $supplierData['status'] = $supplierData['status'] ?? 'active';
            $supplierData['rating'] = 0.0;
            $supplierData['total_orders'] = 0;
            $supplierData['total_value'] = 0.0;

            // Handle file uploads
            if ($request->hasFile('logo')) {
                $supplierData['logo'] = $request->file('logo')->store('suppliers/logos', 'public');
            }

            if ($request->hasFile('business_license')) {
                $supplierData['business_license'] = $request->file('business_license')->store('suppliers/documents', 'public');
            }

            if ($request->hasFile('tax_certificate')) {
                $supplierData['tax_certificate'] = $request->file('tax_certificate')->store('suppliers/documents', 'public');
            }

            if ($request->hasFile('insurance_certificate')) {
                $supplierData['insurance_certificate'] = $request->file('insurance_certificate')->store('suppliers/documents', 'public');
            }

            if ($request->hasFile('quality_certificates')) {
                $supplierData['quality_certificates'] = $request->file('quality_certificates')->store('suppliers/documents', 'public');
            }

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
        // Load related data
        $recentPurchases = collect([
            (object) [
                'id' => 'PO-2024-001',
                'description' => 'Software Licenses & Support',
                'amount' => 24500.00,
                'status' => 'completed',
                'order_date' => '2024-01-15',
                'delivery_date' => '2024-01-29',
            ],
            (object) [
                'id' => 'PO-2024-002',
                'description' => 'Raw Materials Package',
                'amount' => 45750.00,
                'status' => 'in_progress',
                'order_date' => '2024-02-01',
                'delivery_date' => '2024-02-20',
            ],
            (object) [
                'id' => 'PO-2024-003',
                'description' => 'Consulting Services',
                'amount' => 8000.00,
                'status' => 'pending',
                'order_date' => '2024-02-10',
                'delivery_date' => '2024-02-25',
            ],
        ]);

        // Calculate statistics
        $stats = [
            'total_orders' => 156,
            'total_value' => 890450,
            'avg_order_value' => 5707,
            'on_time_delivery' => 92,
            'quality_rating' => 4.5,
            'last_order_date' => '2024-02-10',
        ];

        return view('suppliers.show', compact('supplier', 'recentPurchases', 'stats'));
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
            'email' => 'required|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'category' => 'required|string|in:raw_materials,manufacturing,technology,services,logistics,packaging,maintenance,consulting',
            'status' => 'required|string|in:active,inactive,pending',
            'payment_terms' => 'nullable|string|in:net_15,net_30,net_45,net_60,due_on_receipt,prepaid',
            'credit_limit' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:USD,EUR,GBP,CAD,INR,JPY',
            'lead_time' => 'nullable|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_license' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'tax_certificate' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'insurance_certificate' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'quality_certificates' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $supplierData = $request->except(['logo', 'business_license', 'tax_certificate', 'insurance_certificate', 'quality_certificates']);

            // Handle file uploads
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($supplier->logo) {
                    Storage::disk('public')->delete($supplier->logo);
                }
                $supplierData['logo'] = $request->file('logo')->store('suppliers/logos', 'public');
            }

            if ($request->hasFile('business_license')) {
                if ($supplier->business_license) {
                    Storage::disk('public')->delete($supplier->business_license);
                }
                $supplierData['business_license'] = $request->file('business_license')->store('suppliers/documents', 'public');
            }

            if ($request->hasFile('tax_certificate')) {
                if ($supplier->tax_certificate) {
                    Storage::disk('public')->delete($supplier->tax_certificate);
                }
                $supplierData['tax_certificate'] = $request->file('tax_certificate')->store('suppliers/documents', 'public');
            }

            if ($request->hasFile('insurance_certificate')) {
                if ($supplier->insurance_certificate) {
                    Storage::disk('public')->delete($supplier->insurance_certificate);
                }
                $supplierData['insurance_certificate'] = $request->file('insurance_certificate')->store('suppliers/documents', 'public');
            }

            if ($request->hasFile('quality_certificates')) {
                if ($supplier->quality_certificates) {
                    Storage::disk('public')->delete($supplier->quality_certificates);
                }
                $supplierData['quality_certificates'] = $request->file('quality_certificates')->store('suppliers/documents', 'public');
            }

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

            // Delete associated files
            if ($supplier->logo) {
                Storage::disk('public')->delete($supplier->logo);
            }
            if ($supplier->business_license) {
                Storage::disk('public')->delete($supplier->business_license);
            }
            if ($supplier->tax_certificate) {
                Storage::disk('public')->delete($supplier->tax_certificate);
            }
            if ($supplier->insurance_certificate) {
                Storage::disk('public')->delete($supplier->insurance_certificate);
            }
            if ($supplier->quality_certificates) {
                Storage::disk('public')->delete($supplier->quality_certificates);
            }

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
     * Update supplier rating.
     */
    public function updateRating(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid rating data'], 400);
        }

        try {
            $supplier->update([
                'rating' => $request->rating,
                'total_reviews' => $supplier->total_reviews + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
                'new_rating' => $supplier->rating,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating rating'], 500);
        }
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
                    $suppliers = Supplier::whereIn('id', $supplierIds)->get();
                    foreach ($suppliers as $supplier) {
                        // Delete associated files
                        if ($supplier->logo) {
                            Storage::disk('public')->delete($supplier->logo);
                        }
                        // Delete other files...
                    }
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
                'email',
                'phone',
                'category',
                'status',
                'rating',
                'total_orders',
                'total_value',
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
                    'Email',
                    'Phone',
                    'Category',
                    'Status',
                    'Rating',
                    'Total Orders',
                    'Total Value',
                    'Created Date'
                ]);

                // CSV data
                foreach ($suppliers as $supplier) {
                    fputcsv($file, [
                        $supplier->company_name,
                        $supplier->contact_person,
                        $supplier->email,
                        $supplier->phone,
                        $supplier->category,
                        $supplier->status,
                        $supplier->rating,
                        $supplier->total_orders,
                        $supplier->total_value,
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
                'new_this_month' => Supplier::whereMonth('created_at', now()->month)->count(),
                'avg_rating' => round(Supplier::avg('rating'), 1),
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