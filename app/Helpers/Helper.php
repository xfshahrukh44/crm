<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

function mail_notification ($from, $to, $subject, $html, $for_admin = false) {
    try {
        $mails = $to;

        if ($for_admin) { $mails []= 'test-crm@mailinator.com'; }

        Mail::send([], [], function ($message) use ($from, $to, $subject, $html, $mails) {
            $message->to($mails)
            ->subject($subject)
            ->setBody($html, 'text/html');
        });

//        foreach ($mails as $mail) {
//            mail($mail, $subject, $html, "Content-Type: text/html; charset=UTF-8");
//        }

        return (boolean)count(Mail::failures());
    } catch (\Exception $e) {
        Log::error('function mail_notification failed. Error: ' . $e->getMessage());
        return false;
    }
}

function get_task_status_text ($num = 0) {
    if (!$num) {
        $num = 0;
    }

    $arr = [
        0 => 'Open',
        1 => 'Re Open',
        4 => 'In Progress',
        2 => 'On Hold',
        5 => 'Sent for Approval',
        6 => 'Incomplete Brief',
        3 => 'Completed',
    ];

    return ucfirst($arr[$num]) ?? 'Open';
}