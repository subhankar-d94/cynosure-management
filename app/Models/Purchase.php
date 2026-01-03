<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'supplier_id',
        'purchase_order_number',
        'reference_number',
        'purchase_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'status',
        'priority',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'currency',
        'payment_terms',
        'payment_status',
        'delivery_terms',
        'delivery_address',
        'delivery_city',
        'delivery_state',
        'delivery_pincode',
        'delivery_country',
        'notes',
        'internal_notes',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
        'terms_conditions',
        'purchase_type',
        'department',
        'project_code',
        'budget_code',
        'urgent',
        'requires_approval',
        'approval_limit',
        'version',
        'parent_id',
        'cancelled_reason',
        'cancelled_by',
        'cancelled_at',
        'received_by',
        'received_at',
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'payment_due_date',
        'paid_amount',
        'paid_at',
        'payment_method',
        'bank_reference',
        'quality_check_status',
        'quality_checked_by',
        'quality_checked_at',
        'quality_notes',
        'attachments',
        'tags'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'received_at' => 'datetime',
        'invoice_date' => 'date',
        'payment_due_date' => 'date',
        'paid_at' => 'datetime',
        'quality_checked_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoice_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'approval_limit' => 'decimal:2',
        'urgent' => 'boolean',
        'requires_approval' => 'boolean',
        'version' => 'integer',
        'attachments' => 'array',
        'tags' => 'array'
    ];

    protected $attributes = [
        'status' => 'approved',
        'priority' => 'medium',
        'currency' => 'INR',
        'payment_terms' => 'Net 30',
        'payment_status' => 'pending',
        'delivery_terms' => 'Ex-Works',
        'delivery_country' => 'India',
        'purchase_type' => 'standard',
        'urgent' => false,
        'requires_approval' => false,
        'version' => 1,
        'quality_check_status' => 'pending'
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ORDERED = 'ordered';
    const STATUS_PARTIAL_RECEIVED = 'partial_received';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_OVERDUE = 'overdue';

    const QUALITY_STATUS_PENDING = 'pending';
    const QUALITY_STATUS_PASSED = 'passed';
    const QUALITY_STATUS_FAILED = 'failed';
    const QUALITY_STATUS_PARTIAL = 'partial';

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function qualityChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'quality_checked_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeBySupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('urgent', true);
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('requires_approval', true);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('expected_delivery_date', '<', now())
                    ->whereIn('status', [self::STATUS_APPROVED, self::STATUS_ORDERED]);
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('purchase_date', [$startDate, $endDate]);
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['supplier', 'items', 'approver', 'creator']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ORDERED => 'Ordered',
            self::STATUS_PARTIAL_RECEIVED => 'Partially Received',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
            default => ucfirst($this->status)
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            default => ucfirst($this->priority)
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_ORDERED => 'primary',
            self::STATUS_PARTIAL_RECEIVED => 'info',
            self::STATUS_RECEIVED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_COMPLETED => 'success',
            default => 'secondary'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary'
        };
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->expected_delivery_date || $this->status === self::STATUS_RECEIVED) {
            return 0;
        }

        return max(0, now()->diffInDays($this->expected_delivery_date, false));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->days_overdue > 0;
    }

    public function getProgressPercentageAttribute(): int
    {
        return match($this->status) {
            self::STATUS_DRAFT => 10,
            self::STATUS_PENDING => 25,
            self::STATUS_APPROVED => 50,
            self::STATUS_ORDERED => 75,
            self::STATUS_PARTIAL_RECEIVED => 85,
            self::STATUS_RECEIVED => 95,
            self::STATUS_COMPLETED => 100,
            self::STATUS_CANCELLED => 0,
            default => 0
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_amount, 2);
    }

    public function getCanBeEditedAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING]);
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED, self::STATUS_RECEIVED]);
    }

    public function getCanBeApprovedAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->requires_approval;
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getReceivedItemsAttribute(): int
    {
        return $this->items->sum('received_quantity');
    }

    public function getReceiveProgressAttribute(): int
    {
        $total = $this->total_items;
        $received = $this->received_items;

        return $total > 0 ? (int) round(($received / $total) * 100) : 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->purchase_order_number)) {
                $purchase->purchase_order_number = static::generatePurchaseOrderNumber();
            }
        });
    }

    public static function generatePurchaseOrderNumber(): string
    {
        $prefix = 'PO';
        $year = date('Y');
        $month = date('m');

        $lastPurchase = static::whereYear('created_at', $year)
                             ->whereMonth('created_at', $month)
                             ->orderBy('id', 'desc')
                             ->first();

        $sequence = $lastPurchase ?
            (int) substr($lastPurchase->purchase_order_number, -4) + 1 : 1;

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }

    public function approve(int $approvedBy): bool
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'approved_at' => now()
        ]);

        return true;
    }

    public function cancel(int $cancelledBy, ?string $reason = null): bool
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_by' => $cancelledBy,
            'cancelled_at' => now(),
            'cancelled_reason' => $reason
        ]);

        return true;
    }

    public function markAsReceived(int $receivedBy): bool
    {
        $this->update([
            'status' => self::STATUS_RECEIVED,
            'received_by' => $receivedBy,
            'received_at' => now(),
            'actual_delivery_date' => now()
        ]);

        return true;
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->tax_amount = $this->items->sum(function ($item) {
            $itemTotal = $item->quantity * $item->unit_price;
            return $itemTotal * ($item->tax_rate / 100);
        });

        $this->total_amount = $this->subtotal + $this->tax_amount +
                             ($this->shipping_cost ?? 0) - ($this->discount_amount ?? 0);

        $this->save();
    }
}
