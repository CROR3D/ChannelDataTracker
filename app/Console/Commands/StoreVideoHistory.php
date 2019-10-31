<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;

class StoreVideoHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:storevideohistory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store video data of the previous month';

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
        $videos = Video::all();

        foreach ($videos as $video) {

        }
    }
}
