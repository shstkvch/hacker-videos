<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchItem;

class EnqueueItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:enqueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the maximum number of items and enqueue jobs to fetch them';

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
        $max = Http::get( 'https://hacker-news.firebaseio.com/v0/maxitem.json' );

        $max = $max->body();

        $items = range( 0, $max );

        foreach ( $items as $item ) {
            dispatch( new FetchItem( $item ) );
            $this->info( 'Enqueued ' . $item . ' of ' . $max . ' items' );
        }
    }
}
