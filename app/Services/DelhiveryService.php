<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DelhiveryService
{
    private string $baseUrl;
    private ?string $apiKey;
    private array $headers;

    public function __construct()
    {
        $this->baseUrl = config('services.delhivery.base_url', 'https://track.delhivery.com/api');
        $this->apiKey = config('services.delhivery.api_key');
        $this->headers = [
            'Authorization' => 'Token ' . ($this->apiKey ?? ''),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function createWaybill(array $orderData): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'Delhivery API key not configured'];
        }

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/cmu/create.json', [
                    'format' => 'json',
                    'data' => json_encode([
                        'shipments' => [$this->formatOrderData($orderData)]
                    ])
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'waybill' => $data['packages'][0]['waybill'] ?? null,
                    'response' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create waybill',
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Delhivery create waybill error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'API request failed: ' . $e->getMessage()
            ];
        }
    }

    public function trackShipment(string $waybill): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'Delhivery API key not configured'];
        }

        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/v1/packages/json/', [
                    'waybill' => $waybill
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'tracking_data' => $data['ShipmentData'][0] ?? [],
                    'status' => $this->parseTrackingStatus($data)
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to track shipment',
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Delhivery tracking error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Tracking request failed: ' . $e->getMessage()
            ];
        }
    }

    public function cancelShipment(string $waybill): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'Delhivery API key not configured'];
        }

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/cmu/cancel.json', [
                    'waybills' => $waybill
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Shipment cancelled successfully',
                    'response' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to cancel shipment',
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Delhivery cancel shipment error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Cancel request failed: ' . $e->getMessage()
            ];
        }
    }

    public function getServiceabilityCheck(string $pincode): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'Delhivery API key not configured'];
        }

        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/c/api/pin-codes/json/', [
                    'filter_codes' => $pincode
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'serviceable' => count($data['delivery_codes']) > 0,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check serviceability'
            ];

        } catch (\Exception $e) {
            Log::error('Delhivery serviceability check error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Serviceability check failed: ' . $e->getMessage()
            ];
        }
    }

    public function calculateShippingRate(array $rateData): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'Delhivery API key not configured'];
        }

        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/kinko/v1/invoice/charges/.json', [
                    'md' => $rateData['mode'] ?? 'S', // S for Surface, E for Express
                    'ss' => $rateData['service_type'] ?? 'Delivered',
                    'cgm' => $rateData['weight'] ?? 500, // weight in grams
                    'd_pin' => $rateData['destination_pincode'],
                    'o_pin' => $rateData['origin_pincode'] ?? config('services.delhivery.origin_pincode'),
                    'cod' => $rateData['cod_amount'] ?? 0
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'charges' => $data[0] ?? [],
                    'total_amount' => $data[0]['total_amount'] ?? 0
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to calculate shipping rate'
            ];

        } catch (\Exception $e) {
            Log::error('Delhivery rate calculation error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Rate calculation failed: ' . $e->getMessage()
            ];
        }
    }

    private function formatOrderData(array $orderData): array
    {
        return [
            'name' => $orderData['customer_name'],
            'add' => $orderData['address'],
            'pin' => $orderData['pincode'],
            'city' => $orderData['city'],
            'state' => $orderData['state'],
            'country' => $orderData['country'] ?? 'India',
            'phone' => $orderData['phone'],
            'order' => $orderData['order_id'],
            'payment_mode' => $orderData['payment_mode'] ?? 'Prepaid',
            'return_pin' => config('services.delhivery.return_pincode'),
            'return_city' => config('services.delhivery.return_city'),
            'return_name' => config('services.delhivery.return_name'),
            'return_add' => config('services.delhivery.return_address'),
            'return_state' => config('services.delhivery.return_state'),
            'return_country' => 'India',
            'products_desc' => $orderData['products_description'] ?? 'Handicraft Items',
            'hsn_code' => $orderData['hsn_code'] ?? '9701',
            'cod_amount' => $orderData['cod_amount'] ?? 0,
            'order_date' => $orderData['order_date'] ?? now()->format('Y-m-d H:i:s'),
            'total_amount' => $orderData['total_amount'],
            'seller_add' => config('services.delhivery.seller_address'),
            'seller_name' => config('services.delhivery.seller_name'),
            'seller_inv' => $orderData['invoice_number'] ?? '',
            'quantity' => $orderData['quantity'] ?? 1,
            'waybill' => '',
            'shipment_width' => $orderData['width'] ?? 10,
            'shipment_height' => $orderData['height'] ?? 10,
            'weight' => $orderData['weight'] ?? 500,
            'seller_gst_tin' => config('services.delhivery.gst_number'),
            'shipping_mode' => $orderData['shipping_mode'] ?? 'Surface',
            'address_type' => 'home'
        ];
    }

    private function parseTrackingStatus(array $data): string
    {
        if (!isset($data['ShipmentData'][0]['Shipment']['Status'])) {
            return 'unknown';
        }

        $status = $data['ShipmentData'][0]['Shipment']['Status']['Status'];

        return match(strtolower($status)) {
            'shipped' => 'shipped',
            'in transit' => 'in_transit',
            'out for delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'returned' => 'returned',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
    }
}