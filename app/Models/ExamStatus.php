<?php

namespace App\Models;

use App\Models\Student;
use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_session_id',
        'token',
        'status_token'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}
