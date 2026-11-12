<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrowing extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DIPINJAM = 'dipinjam';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_TERLAMBAT = 'terlambat';

    public const ACTIVE_STATUSES = [
        self::STATUS_DISETUJUI,
        self::STATUS_DIPINJAM,
        self::STATUS_TERLAMBAT,
    ];

    protected $fillable = [
        'code',
        'user_id',
        'approved_by',
        'status',
        'borrow_date',
        'due_date',
        'purpose',
        'notes',
        'rejection_reason',
        'approved_at',
        'picked_up_at',
        'total_items',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'total_items' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(ReturnRecord::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_DIPINJAM => 'Sedang Dipinjam',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_TERLAMBAT => 'Terlambat',
            default => 'Menunggu Persetujuan',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI, self::STATUS_DIPINJAM => 'badge-info',
            self::STATUS_SELESAI => 'badge-success',
            self::STATUS_DITOLAK, self::STATUS_TERLAMBAT => 'badge-danger',
            default => 'badge-warning',
        };
    }

    public function getIsLateAttribute(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true)
            && $this->due_date
            && $this->due_date->isBefore(today());
    }

    public function getLateDaysAttribute(): int
    {
        if (! $this->due_date) {
            return 0;
        }

        $reference = in_array($this->status, self::ACTIVE_STATUSES, true)
            ? today()
            : ($this->returnRecord?->return_date ?? today());

        return $reference->greaterThan($this->due_date)
            ? (int) $this->due_date->diffInDays($reference)
            : 0;
    }
}