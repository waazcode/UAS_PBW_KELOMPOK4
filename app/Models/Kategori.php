<?php

namespace App\Models;

use App\Support\DisplayText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $fillable = ['nama'];

    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function getNamaTampilanAttribute(): string
    {
        return DisplayText::format($this->nama);
    }
}
