<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DateTime;

class ProductionMemberAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'subtask_id',
        'assigned_by',
        'assigned_to',
        'comments',
        'duadate',
        'status',
    ];

    public function assigned_by_user(){
        return $this->hasOne(User::class, 'id', 'assigned_by');
    }


    public function assigned_to_user(){
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    public function get_status(){
        $status = $this->status;
        if($status == 0){
            return "<button class='btn btn-danger btn-sm'>Open</button>";
        }else if($status == 1){
            return "<button class='btn btn-primary btn-sm'>Re Open</button>";
        }else if($status == 2){
            return "<button class='btn btn-info btn-sm'>Hold</button>";
        }else if($status == 3){
            return "<button class='btn btn-success btn-sm'>Completed</button>";
        }else if($status == 4){
            return "<button class='btn btn-warning btn-sm'>In Progress</button>";
        }else if($status == 5){
            return "<button class='btn btn-".(auth()->user()->is_employee == 5 ? 'warning' : 'info')." btn-sm'>Sent for ".auth()->user()->is_employee == 5 ? 'Review' : 'Approval'."</button>";
        }else if($status == 6){
            return "<button class='btn btn-warning btn-sm'>Incomplete Brief</button>";
        }
    }

    public function get_status_badge(){
        // 0 for open
        // 1 for re_open
        // 2 for hold
        // 3 for completed
        // 4 for in_progress
        $status = $this->status;
        if($status == 0){
            return "<span class='badge badge-danger badge-sm text-white'>Open</span>";
        }else if($status == 1){
            return "<span class='badge badge-primary badge-sm text-white'>Re Open</span>";
        }else if($status == 2){
            return "<span class='badge badge-info badge-sm text-white'>Hold</span>";
        }else if($status == 3){
            return "<span class='badge badge-success badge-sm text-white'>Completed</span>";
        }else if($status == 4){
            return "<span class='badge badge-warning badge-sm text-white'>In Progress</span>";
        }else if($status == 5){
            return "<span class='badge badge-info badge-sm text-white'>Sent for Approval</span>";
        }else if($status == 6){
            return "<span class='badge badge-warning badge-sm text-white'>Incomplete Brief</span>";
        }
//        else if($status == 7){
//            return "<span class='badge badge-info badge-sm text-white'>Sent for QA</span>";
//        }
    }

    public function get_status_badge_v2(){
        // 0 for open
        // 1 for re_open
        // 2 for hold
        // 3 for completed
        // 4 for in_progress
        $status = $this->status;
        if($status == 0){
            return "<span class='badge bg-danger badge-sm text-white'>Open</span>";
        }else if($status == 1){
            return "<span class='badge bg-primary badge-sm text-white'>Re Open</span>";
        }else if($status == 2){
            return "<span class='badge bg-info badge-sm text-white'>Hold</span>";
        }else if($status == 3){
            return "<span class='badge bg-success badge-sm text-white'>Completed</span>";
        }else if($status == 4){
            return "<span class='badge bg-warning badge-sm text-white'>In Progress</span>";
        }else if($status == 5){
            return "<span class='badge bg-info badge-sm text-white'>Sent for Approval</span>";
        }else if($status == 6){
            return "<span class='badge bg-warning badge-sm text-white'>Incomplete Brief</span>";
        }
//        else if($status == 7){
//            return "<span class='badge badge-info badge-sm text-white'>Sent for QA</span>";
//        }
    }


    // public function getDueDate(){
    //     $date_string = '';
    //     $date_now = new DateTime();
    //     if(is_countable($this->sub_tasks) == 0){
    //         $date2 = new DateTime(date('d-m-Y', strtotime($this->duedate)));
    //         $date_string = $this->duedate;
    //     }else{
    //         $date_string = "";
    //         $date2 = "";
    //         if($this->getSubtaskDueDate != null){
    //             $date2 = new DateTime(date('d-m-Y', strtotime($this->getSubtaskDueDate->duedate)));
    //             $date_string = $this->getSubtaskDueDate->duedate;
    //         }else{
    //             $date2 = new DateTime(date('d-m-Y', strtotime($this->duedate)));
    //             $date_string = $this->duedate;
    //         }

    //         // if($this->subtaskDueDate != null){
    //         //     if($this->subtaskDueDate->duedateChange != null){
    //         //         $date2 = new DateTime(date('d-m-Y', strtotime($this->subtaskDueDate->duedateChange->duadate)));
    //         //         $date_string = $this->subtaskDueDate->duedateChange->duadate;
    //         //     }else{
    //         //         $date2 = new DateTime(date('d-m-Y', strtotime($this->subtaskDueDate->duedate)));
    //         //         $date_string = $this->subtaskDueDate->duedate;
    //         //     }
    //         // }else{
    //         //     $date2 = new DateTime(date('d-m-Y', strtotime($this->duedate)));
    //         //     $date_string = $this->duedate;
    //         // }
    //     }

    //     $button = '';
    //     if ($date_now > $date2){
    //         $button.= '<button class="btn btn-danger btn-sm">';
    //     }else{
    //         $button.= '<button class="btn btn-success btn-sm">';
    //     }

    //     $button .= date('d-m-Y', strtotime($date_string)) . '</button>';
    //     return $button;
    // }


    public function task(){
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

    public function subtask(){
        return $this->hasOne(SubTask::class, 'id', 'subtask_id');
    }

    public function sub_tasks_message(){
        return $this->hasMany(ProductionMessage::class, 'production_member_assigns_id', 'id')->orderBy('id', 'desc');
    }


}
