<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'tool_id',
        'quantity',
        'condition',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function returnRecord(): BelongsTo
    {
        return $this->belongsTo(ReturnRecord::class, 'return_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => 'Baik',
        };
    }
}