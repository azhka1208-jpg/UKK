<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'description',
        'stock_total',
        'stock_available',
        'condition',
        'location',
        'photo',
        'status',
    ];

    protected $casts = [
        'stock_total' => 'integer',
        'stock_available' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function returnDetails(): HasMany
    {
        return $this->hasMany(ReturnDetail::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'tersedia')->where('stock_available', '>', 0);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'tersedia' && $this->stock_available > 0;
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => 'Baik',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'perbaikan' => 'Dalam Perbaikan',
            'tidak_tersedia' => 'Tidak Tersedia',
            default => 'Tersedia',
        };
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::disk('public')->url($this->photo);
        }

        return asset('images/tool-placeholder.svg');
    }
}