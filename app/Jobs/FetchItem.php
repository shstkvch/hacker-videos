<?php

namespace App\Jobs;

use App\Item;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $hn_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $hn_id )
    {
        $this->hn_id = $hn_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( Item::find( $this->hn_id ) ) {
            throw new Exception( 'Already added this item' );
        }
        
        // grab the item json from HN
        $response = Http::get( 'https://hacker-news.firebaseio.com/v0/item/' . $this->hn_id . '.json?print=pretty' );  

        if ( ! $response->ok() ) {
            throw new Exception( 'Could not grab HN item JSON' );
        }

        $new_item = new Item();

        $json = $response->json();

        $new_item->fill( $json );
        $new_item->id = $json['id'];

        $new_item->save();
    }
}
