<?php

namespace App\Services;

use App\Models\Laporan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LaporanMapCoordinateResolver
{
    private const ACEH_BOUNDS = [
        'min_lat' => 2.0,
        'max_lat' => 6.3,
        'min_lng' => 95.0,
        'max_lng' => 98.3,
    ];

    /**
     * @return array{0: float, 1: float}|null
     */
    public function resolve(Laporan $laporan): ?array
    {
        if ($laporan->latitude !== null && $laporan->longitude !== null) {
            $lat = (float) $laporan->latitude;
            $lng = (float) $laporan->longitude;

            if ($this->isWithinAceh($lat, $lng)) {
                return [$lat, $lng];
            }
        }

        if (! $laporan->alamat) {
            return null;
        }

        $cacheKey = 'laporan_map_coords:'.$laporan->id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $coords = $this->geocodeAlamat($laporan->alamat);

        if ($coords === null) {
            return null;
        }

        $laporan->update([
            'latitude' => $coords[0],
            'longitude' => $coords[1],
        ]);

        Cache::put($cacheKey, $coords, now()->addDays(7));

        return $coords;
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    private function geocodeAlamat(string $alamat): ?array
    {
        $searchQuery = $alamat;
        if (! str_contains(strtolower($searchQuery), 'aceh')) {
            $searchQuery .= ', Aceh';
        }

        $bounds = self::ACEH_BOUNDS;

        $response = Http::withHeaders([
            'User-Agent' => config('app.name', 'SafeZone').'/1.0',
        ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
            'q' => $searchQuery,
            'format' => 'json',
            'addressdetails' => 0,
            'limit' => 1,
            'countrycodes' => 'id',
            'viewbox' => "{$bounds['min_lng']},{$bounds['max_lat']},{$bounds['max_lng']},{$bounds['min_lat']}",
            'bounded' => 1,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $result = $response->json()[0] ?? null;

        if ($result === null) {
            return null;
        }

        $lat = (float) $result['lat'];
        $lng = (float) $result['lon'];

        if (! $this->isWithinAceh($lat, $lng)) {
            return null;
        }

        return [$lat, $lng];
    }

    private function isWithinAceh(float $lat, float $lng): bool
    {
        $bounds = self::ACEH_BOUNDS;

        return $lat >= $bounds['min_lat']
            && $lat <= $bounds['max_lat']
            && $lng >= $bounds['min_lng']
            && $lng <= $bounds['max_lng'];
    }
}
