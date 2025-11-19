<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringBlockedTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'start_time',
        'end_time',
        'days_of_week',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    /**
     * Get the business that owns this recurring blocked time.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
