<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Queue;


use App\Jobs\FetchItem;

class ItemController extends Controller
{
    function queue( Request $request ) {
        dispatch( new FetchItem( $request->get( 'id' ) ) );
    }
}
