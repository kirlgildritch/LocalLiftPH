<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAction extends Model
{
    protected $fillable = [
        'report_id',
        'handled_by',
        'action',
        'admin_notes',
        'handled_at',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'warn_seller' => 'Warn Seller',
            'delist_product' => 'Hide / Delist Product',
            'ban_product' => 'Ban / Remove Product',
            'suspend_seller' => 'Suspend Seller Account',
            'mark_resolved' => 'Mark as Resolved',
            'dismiss_report' => 'Dismiss Report',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
