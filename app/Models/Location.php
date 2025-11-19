<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'address',
        'city',
        'latitude',
        'longitude',
        'opening_hours',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
