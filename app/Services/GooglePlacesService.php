<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    private ?string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.google.places_api_key');
        $this->baseUrl = 'https://maps.googleapis.com/maps/api';
    }

    public function searchPlaces(string $query, ?array $location = null, int $radius = 5000): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $params = [
                'query' => $query,
                'key' => $this->apiKey,
                'type' => 'establishment'
            ];

            if ($location) {
                $params['location'] = $location['lat'] . ',' . $location['lng'];
                $params['radius'] = $radius;
            }

            $response = Http::get($this->baseUrl . '/place/textsearch/json', $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'places' => $data['results'] ?? [],
                    'next_page_token' => $data['next_page_token'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to search places',
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Google Places search error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Search request failed: ' . $e->getMessage()
            ];
        }
    }

    public function getPlaceDetails(string $placeId, array $fields = []): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $defaultFields = [
                'place_id',
                'formatted_address',
                'geometry',
                'name',
                'address_components',
                'international_phone_number',
                'website',
                'rating'
            ];

            $fields = !empty($fields) ? $fields : $defaultFields;

            $response = Http::get($this->baseUrl . '/place/details/json', [
                'place_id' => $placeId,
                'fields' => implode(',', $fields),
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK') {
                    return [
                        'success' => true,
                        'place' => $data['result'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Place not found or invalid place ID'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get place details'
            ];

        } catch (\Exception $e) {
            Log::error('Google Places details error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Place details request failed: ' . $e->getMessage()
            ];
        }
    }

    public function autocompleteAddress(string $input, ?array $location = null, int $radius = 50000): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $params = [
                'input' => $input,
                'key' => $this->apiKey,
                'types' => 'address'
            ];

            if ($location) {
                $params['location'] = $location['lat'] . ',' . $location['lng'];
                $params['radius'] = $radius;
            }

            $response = Http::get($this->baseUrl . '/place/autocomplete/json', $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'predictions' => $data['predictions'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get address suggestions'
            ];

        } catch (\Exception $e) {
            Log::error('Google Places autocomplete error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Autocomplete request failed: ' . $e->getMessage()
            ];
        }
    }

    public function geocodeAddress(string $address): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $response = Http::get($this->baseUrl . '/geocode/json', [
                'address' => $address,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $result = $data['results'][0];

                    return [
                        'success' => true,
                        'location' => $result['geometry']['location'],
                        'formatted_address' => $result['formatted_address'],
                        'address_components' => $result['address_components'] ?? [],
                        'place_id' => $result['place_id'] ?? null
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Address not found'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to geocode address'
            ];

        } catch (\Exception $e) {
            Log::error('Google Geocoding error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Geocoding request failed: ' . $e->getMessage()
            ];
        }
    }

    public function reverseGeocode(float $lat, float $lng): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $response = Http::get($this->baseUrl . '/geocode/json', [
                'latlng' => $lat . ',' . $lng,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    return [
                        'success' => true,
                        'results' => $data['results']
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Location not found'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to reverse geocode'
            ];

        } catch (\Exception $e) {
            Log::error('Google Reverse Geocoding error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Reverse geocoding request failed: ' . $e->getMessage()
            ];
        }
    }

    public function validateAddress(array $addressData): array
    {
        if (!$this->apiKey) {
            return ['is_valid' => false, 'message' => 'Google Places API key not configured'];
        }

        try {
            $addressString = $this->buildAddressString($addressData);
            $geocodeResult = $this->geocodeAddress($addressString);

            if (!$geocodeResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Address could not be validated'
                ];
            }

            $parsedAddress = $this->parseAddressComponents($geocodeResult['address_components']);

            return [
                'success' => true,
                'validated_address' => [
                    'formatted_address' => $geocodeResult['formatted_address'],
                    'location' => $geocodeResult['location'],
                    'place_id' => $geocodeResult['place_id'],
                    'components' => $parsedAddress
                ],
                'is_valid' => true
            ];

        } catch (\Exception $e) {
            Log::error('Address validation error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Address validation failed: ' . $e->getMessage()
            ];
        }
    }

    public function findNearbyPincodes(float $lat, float $lng, int $radius = 10000): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $response = Http::get($this->baseUrl . '/place/nearbysearch/json', [
                'location' => $lat . ',' . $lng,
                'radius' => $radius,
                'type' => 'postal_code',
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'pincodes' => $data['results'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to find nearby pincodes'
            ];

        } catch (\Exception $e) {
            Log::error('Nearby pincodes search error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Nearby search failed: ' . $e->getMessage()
            ];
        }
    }

    private function buildAddressString(array $addressData): string
    {
        $parts = [];

        if (!empty($addressData['address_line_1'])) {
            $parts[] = $addressData['address_line_1'];
        }

        if (!empty($addressData['address_line_2'])) {
            $parts[] = $addressData['address_line_2'];
        }

        if (!empty($addressData['city'])) {
            $parts[] = $addressData['city'];
        }

        if (!empty($addressData['state'])) {
            $parts[] = $addressData['state'];
        }

        if (!empty($addressData['pincode'])) {
            $parts[] = $addressData['pincode'];
        }

        if (!empty($addressData['country'])) {
            $parts[] = $addressData['country'];
        }

        return implode(', ', $parts);
    }

    private function parseAddressComponents(array $components): array
    {
        $parsed = [
            'street_number' => '',
            'route' => '',
            'locality' => '',
            'sublocality' => '',
            'administrative_area_level_1' => '',
            'administrative_area_level_2' => '',
            'country' => '',
            'postal_code' => ''
        ];

        foreach ($components as $component) {
            $type = $component['types'][0] ?? '';

            if (isset($parsed[$type])) {
                $parsed[$type] = $component['long_name'];
            }
        }

        return $parsed;
    }
}