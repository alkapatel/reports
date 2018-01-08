<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Models\User;


class SendDailyWorkReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-work-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation Process For sending daily work report to client.';

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
     * @return mixed
     */
    public function handle()
    { 
        
    }
}


