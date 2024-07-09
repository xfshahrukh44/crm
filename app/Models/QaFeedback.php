<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaFeedback extends Model
{
    use HasFactory;

    protected $table = 'qa_feedbacks';

    protected $fillable = [
        'user_id',
        'task_id',
        'message',
        'status'
    ];

    public function user ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task ()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function qa_files ()
    {
        return $this->hasMany(QaFile::class);
    }
}
