<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRecord extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'code',
        'borrowing_id',
        'received_by',
        'return_date',
        'status',
        'late_days',
        'fine_amount',
        'fine_paid',
        'notes',
        'verified_at',
    ];

    protected $casts = [
        'return_date' => 'date',
        'verified_at' => 'datetime',
        'late_days' => 'integer',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReturnDetail::class, 'return_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'diverifikasi' ? 'Diverifikasi' : 'Menunggu Verifikasi';
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status === 'diverifikasi' ? 'badge-success' : 'badge-warning';
    }
}