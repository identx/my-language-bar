$(document).ready(function() {

    var $videoSrc;
    $('.video-btn').click(function() {
        $videoSrc = $(this).data( "src" );
    });

    $('#teachers-modal').on('shown.bs.modal', function (e) {

        $("#video").attr('src',$videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0" );
    })

    $('#teachers-modal').on('hide.bs.modal', function (e) {
        // a poor man's stop video
        $("#video").attr('src',$videoSrc);
    })

});