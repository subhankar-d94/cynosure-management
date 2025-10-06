<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GooglePlacesService;
use App\Services\DelhiveryService;

class AddressController extends Controller
{
    protected $googlePlacesService;
    protected $delhiveryService;

    public function __construct(GooglePlacesService $googlePlacesService, DelhiveryService $delhiveryService)
    {
        $this->googlePlacesService = $googlePlacesService;
        $this->delhiveryService = $delhiveryService;
    }

    public function validateAddress(Request $request)
    {
        $request->validate([
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|size:6',
            'country' => 'required|string|max:100'
        ]);

        try {
            $address = [
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => $request->country
            ];

            // Validate with Google Places if place_id is provided
            if ($request->has('google_place_id') && !empty($request->google_place_id)) {
                $placeDetails = $this->googlePlacesService->getPlaceDetails($request->google_place_id);

                if (!$placeDetails) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid Google Place ID'
                    ], 400);
                }
            }

            // Check serviceability with Delhivery
            $serviceability = $this->delhiveryService->checkServiceability($request->pincode);

            return response()->json([
                'success' => true,
                'message' => 'Address validated successfully',
                'data' => [
                    'address' => $address,
                    'serviceable' => $serviceability['serviceable'] ?? false,
                    'delivery_days' => $serviceability['delivery_days'] ?? null,
                    'cod_available' => $serviceability['cod_available'] ?? false
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Address validation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function autocomplete(Request $request)
    {
        $request->validate([
            'input' => 'required|string|min:3',
            'country' => 'string|size:2'
        ]);

        try {
            $country = $request->get('country', 'IN');
            $suggestions = $this->googlePlacesService->getAutocompleteSuggestions(
                $request->input,
                $country
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Autocomplete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function geocode(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'country' => 'string|size:2'
        ]);

        try {
            $country = $request->get('country', 'IN');
            $result = $this->googlePlacesService->geocodeAddress($request->address, $country);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geocoding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkServiceability($pincode)
    {
        try {
            $serviceability = $this->delhiveryService->checkServiceability($pincode);

            return response()->json([
                'success' => true,
                'data' => $serviceability
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Serviceability check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getShippingRate(Request $request)
    {
        $request->validate([
            'from_pincode' => 'required|string|size:6',
            'to_pincode' => 'required|string|size:6',
            'weight' => 'required|numeric|min:0.1',
            'cod' => 'boolean'
        ]);

        try {
            $rateParams = [
                'from_pincode' => $request->from_pincode,
                'to_pincode' => $request->to_pincode,
                'weight' => $request->weight,
                'cod' => $request->get('cod', false)
            ];

            $rates = $this->delhiveryService->getShippingRates($rateParams);

            return response()->json([
                'success' => true,
                'data' => $rates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping rate calculation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        try {
            $result = $this->googlePlacesService->reverseGeocode(
                $request->latitude,
                $request->longitude
            );

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reverse geocoding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getNearbyPlaces(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:1|max:50000',
            'type' => 'string|in:establishment,point_of_interest,store,bank,atm,hospital,school'
        ]);

        try {
            $params = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->get('radius', 1000),
                'type' => $request->get('type', 'establishment')
            ];

            $places = $this->googlePlacesService->getNearbyPlaces($params);

            return response()->json([
                'success' => true,
                'data' => $places
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nearby places search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPlaceDetails(Request $request)
    {
        $request->validate([
            'place_id' => 'required|string'
        ]);

        try {
            $details = $this->googlePlacesService->getPlaceDetails($request->place_id);

            if (!$details) {
                return response()->json([
                    'success' => false,
                    'message' => 'Place not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Place details fetch failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validatePincode(Request $request)
    {
        $request->validate([
            'pincode' => 'required|string|size:6'
        ]);

        try {
            // Basic pincode format validation
            if (!preg_match('/^[1-9][0-9]{5}$/', $request->pincode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pincode format'
                ], 400);
            }

            // Check with Delhivery for serviceability
            $serviceability = $this->delhiveryService->checkServiceability($request->pincode);

            // Try to get location details from Google
            $locationDetails = null;
            try {
                $geocodeResult = $this->googlePlacesService->geocodeAddress($request->pincode . ', India');
                if ($geocodeResult) {
                    $locationDetails = [
                        'city' => $geocodeResult['city'] ?? null,
                        'state' => $geocodeResult['state'] ?? null,
                        'country' => $geocodeResult['country'] ?? null,
                        'coordinates' => $geocodeResult['coordinates'] ?? null
                    ];
                }
            } catch (\Exception $e) {
                // Continue without location details if geocoding fails
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'pincode' => $request->pincode,
                    'valid' => true,
                    'serviceable' => $serviceability['serviceable'] ?? false,
                    'location_details' => $locationDetails,
                    'delivery_info' => $serviceability
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pincode validation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}