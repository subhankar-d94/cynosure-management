<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShipmentGlobalController extends Controller
{
    private $apiUrl = 'https://api.bhbazar.com/api/shipmentglobal/orders/all';
    private $sellerId = 'sfCXz8BoQ8ddamP2gCWLbvZO2Ti1';

    public function index(Request $request)
    {
        try {
            // Fetch data from external API
            $response = Http::timeout(30)->get($this->apiUrl, [
                'seller_id' => $this->sellerId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $orders = $data['data'] ?? $data ?? [];
                
                return view('shipment-global.index', [
                    'orders' => $orders,
                    'success' => true
                ]);
            } else {
                return view('shipment-global.index', [
                    'orders' => [],
                    'success' => false,
                    'error' => 'Failed to fetch data from API. Status: ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Shipment Global API Error: ' . $e->getMessage());
            
            return view('shipment-global.index', [
                'orders' => [],
                'success' => false,
                'error' => 'Error connecting to Shipment Global API: ' . $e->getMessage()
            ]);
        }
    }

    public function show(Request $request, $orderId)
    {
        try {
            // Fetch single order details
            $response = Http::timeout(30)->get($this->apiUrl, [
                'seller_id' => $this->sellerId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $orders = $data['data'] ?? $data ?? [];
                
                // Find the specific order
                $order = collect($orders)->firstWhere('id', $orderId);
                
                if ($order) {
                    return view('shipment-global.show', [
                        'order' => $order,
                        'success' => true
                    ]);
                } else {
                    return redirect()->route('shipment-global.index')
                        ->with('error', 'Order not found');
                }
            } else {
                return redirect()->route('shipment-global.index')
                    ->with('error', 'Failed to fetch order details');
            }
        } catch (\Exception $e) {
            Log::error('Shipment Global API Error: ' . $e->getMessage());
            
            return redirect()->route('shipment-global.index')
                ->with('error', 'Error connecting to Shipment Global API');
        }
    }

    public function refresh(Request $request)
    {
        return redirect()->route('shipment-global.index')
            ->with('success', 'Data refreshed successfully');
    }
}
