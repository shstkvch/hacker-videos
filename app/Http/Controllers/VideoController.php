<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Video;

class VideoController extends Controller
{
    function index() {
        $context = [];
        $context['videos'] = Video::orderBy( 'votes', 'DESC' )->get();

        return view( 'index', $context );
    }
}
