<?php

namespace App\Console\Commands;

use App\Jobs\PurgeNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use net\authorize\util\Log;

class PurgeNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now()->subMonth();
        \Illuminate\Support\Facades\Log::info('purging notifications');

//        foreach (DB::table('notifications')->whereDate('created_at', '<=', $date)->pluck('id') as $notification_id) {
//            dispatch(new PurgeNotification($notification_id));
//        }

        return Command::SUCCESS;
    }
}
