<html>
    <head>
        <title>Hacker Videos</title>  
    </head>
    <body>
        @foreach( $videos as $video )
            <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $video->youtube_guid }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        @endforeach
    </body>
</html>
