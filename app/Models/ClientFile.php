<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFile extends Model
{
    use HasFactory;
    protected $table = 'client_files';
    protected $fillable = ['path', 'name', 'status', 'task_id', 'user_id', 'user_check', 'production_check', 'message_id'];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
    
    public function get_extension(){
        $path = $this->path;
        $temp = explode('.',$path);
        $extension = end($temp);
        return $extension;
    }
    
}
