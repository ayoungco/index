<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ReverseGeocoder
{
    /** @return array{address:?string,city:?string,country:?string,country_code:?string,building:?string} */
    public function lookup(float $latitude, float $longitude): array
    {
        $key = sprintf('geocode:%.4F:%.4F', $latitude, $longitude);

        return Cache::remember($key, now()->addDays(30), function () use ($latitude, $longitude): array {
            $response = Http::acceptJson()->timeout(4)
                ->withUserAgent((string) config('services.geocoding.user_agent'))
                ->get((string) config('services.geocoding.endpoint'), [
                    'format' => 'jsonv2', 'lat' => $latitude, 'lon' => $longitude, 'addressdetails' => 1,
                ]);

            if (! $response->ok()) {
                return $this->emptyResult();
            }

            $data = $response->json();
            $address = is_array($data['address'] ?? null) ? $data['address'] : [];

            return [
                'address' => is_string($data['display_name'] ?? null) ? $data['display_name'] : null,
                'city' => $this->firstString($address, ['city', 'town', 'village', 'municipality', 'county']),
                'country' => $this->firstString($address, ['country']),
                'country_code' => strtoupper((string) ($address['country_code'] ?? '')) ?: null,
                'building' => $this->firstString($address, ['building', 'amenity', 'shop', 'office', 'house_name']),
            ];
        });
    }

    /** @return array{address:null,city:null,country:null,country_code:null,building:null} */
    private function emptyResult(): array
    {
        return ['address' => null, 'city' => null, 'country' => null, 'country_code' => null, 'building' => null];
    }

    /** @param array<string, mixed> $values @param list<string> $keys */
    private function firstString(array $values, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (is_string($values[$key] ?? null) && $values[$key] !== '') {
                return $values[$key];
            }
        }

        return null;
    }
}
