<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function iconClass(): string
    {
        return match ($this->type) {
            'keluhan' => 'bi-exclamation-octagon text-danger',
            'checklist' => 'bi-card-checklist text-success',
            'pembayaran' => 'bi-cash-coin text-info',
            default => 'bi-bell',
        };
    }
}