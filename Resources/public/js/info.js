$(document).ready(function(){
    $(document).on('jc.playlist.play-song', function(e, data){
        $.ajax({
                url: '/info/'+$(e.target).data('element'),
                dataType: "json"
            }).done(function(data){
                $('#browser-info-cover').attr('src',data.cover);
                $('#browser-info-song').html(data.song);
                $('#browser-info-disk').html(data.disk);
                $('#browser-info-artist').html(data.artist);
            });
    });
});