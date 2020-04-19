<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Item;
use App\Video;
use App\Jobs\CheckItemForVideo;

class EnqueueVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:enqueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find videos in collected items to enqueue for processing';

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
        $potential_videos = Item::where( 'imported', '=', 0 )
            ->where( function( $query ) {
                return $query->where( 'url', 'LIKE', '%youtube.com/watch?%' )
                    ->orWhere( 'text', 'LIKE', '%youtube.com/watch?%' );
            } )
            ->get();

        $this->info( 'Found ' . count( $potential_videos ) . ' videos to enqueue' );


        $potential_videos->each( function( $potential_video ) {
            $url  = $potential_video->url ?: '';
            $text = $potential_video->text ?: '';

            $preg_pattern = "/youtube\.com\/watch\?v=([%&=#\w-]+)/m";

            $subject = $url . ' ' . $text;

            // see if we have a YouTube URL in either field
            preg_match_all( $preg_pattern, $subject, $m);

            if ( isset( $m[0] ) ) {
                $new_video = new Video();

                $parsed = parse_url( current( $m[0] ) );

                if ( isset( $parsed['query'] ) ) {
                    parse_str( $parsed['query'], $query );

                    $new_video->youtube_guid = $query['v'];
                    $new_video->item_id = $potential_video->id;
                    $new_video->votes = $potential_video->score;

                    $new_video->save();    
                }
            }

            $potential_video->imported = true;
            $potential_video->save();
        } );
    }
}
