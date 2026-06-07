<?php

use App\Models\Laporan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const ACEH_BOUNDS = [
        'min_lat' => 2.0,
        'max_lat' => 6.3,
        'min_lng' => 95.0,
        'max_lng' => 98.3,
    ];

    public function up(): void
    {
        $knownCoordinates = [
            'Lampu jalan mati' => ['latitude' => 5.5483, 'longitude' => 95.3238],
            'Jalan berlubang' => ['latitude' => 5.5521, 'longitude' => 95.3172],
            'Sampah menumpuk' => ['latitude' => 5.5412, 'longitude' => 95.3315],
        ];

        foreach ($knownCoordinates as $judul => $coordinates) {
            Laporan::where('judul', $judul)->update($coordinates);
        }

        Laporan::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->each(function (Laporan $laporan) {
                if (! $this->isWithinAceh((float) $laporan->latitude, (float) $laporan->longitude)) {
                    $laporan->update([
                        'latitude' => null,
                        'longitude' => null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Data correction migration; no rollback needed.
    }

    private function isWithinAceh(float $lat, float $lng): bool
    {
        $bounds = self::ACEH_BOUNDS;

        return $lat >= $bounds['min_lat']
            && $lat <= $bounds['max_lat']
            && $lng >= $bounds['min_lng']
            && $lng <= $bounds['max_lng'];
    }
};
