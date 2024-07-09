<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'qa_feedback_id',
        'path',
        'name'
    ];

    public function qa_feedback ()
    {
        return $this->belongsTo(QaFeedback::class, 'qa_feedback_id');
    }
}
