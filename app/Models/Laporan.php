<?php

namespace App\Models;

use App\Support\DisplayText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    public const STATUS = [
        'menunggu' => 'Menunggu',
        'proses' => 'Proses',
        'selesai' => 'Selesai',
    ];

    protected $fillable = [
        'user_id',
        'kategori_id',
        'judul',
        'deskripsi',
        'foto',
        'latitude',
        'longitude',
        'alamat',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function komentars(): HasMany
    {
        return $this->hasMany(Komentar::class)->orderBy('created_at');
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    public function getAlamatTampilanAttribute(): ?string
    {
        return $this->alamat ? DisplayText::format($this->alamat) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
