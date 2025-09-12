<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriberAction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'subscriber_id',
        'action',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the subscriber this action belongs to
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
