<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\ExamGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'title',
        'start_time',
        'end_time',
        'token',
    ];

    public function exam_groups()
    {
        return $this->hasMany(ExamGroup::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function getStartTimeAttribute($value)
    {
        // Mengembalikan start_time dalam format 'Y-m-d H:i' dengan timezone Asia/Jakarta
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d H:i');
    }

    /**
     * Set the start time before saving in Asia/Jakarta
     *
     * @param  string  $value
     * @return void
     */
    public function setStartTimeAttribute($value)
    {
        // Mengatur start_time ke format database dengan timezone Asia/Jakarta
        $this->attributes['start_time'] = Carbon::parse($value, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->toDateTimeString();
    }

    /**
     * Get the end time formatted for Asia/Jakarta
     *
     * @return string
     */
    public function getEndTimeAttribute($value)
    {
        // Mengembalikan end_time dalam format 'Y-m-d H:i' dengan timezone Asia/Jakarta
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d H:i');
    }

    /**
     * Set the end time before saving in Asia/Jakarta
     *
     * @param  string  $value
     * @return void
     */
    public function setEndTimeAttribute($value)
    {
        // Mengatur end_time ke format database dengan timezone Asia/Jakarta
        $this->attributes['end_time'] = Carbon::parse($value, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->toDateTimeString();
    }
}
