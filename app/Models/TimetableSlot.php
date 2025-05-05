<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableSlot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'timetable_id',
        'day',
        'time_start',
        'time_end',
        'subject',
        'type',
        'notes'
    ];

    /**
     * Get the timetable that owns the slot.
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }
}
