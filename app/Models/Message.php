<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'contact_id',
        'wamid',
        'direction',
        'type',
        'body',
        'payload',
        'timestamp',
        'status',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'payload' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}