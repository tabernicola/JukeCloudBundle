$(document).ready(function() {
    $('#ytAdd').click(function(){
        $('#ytForm').addClass('hide');
        $('#ytMessage').addClass('hide');
        $.post('plugin/youtube/info', {'url': $('#ytUrl').val()}, function(video){
            try{
                $('#ytImg').attr('src', video.snippet.thumbnails.medium.url);
                $('#ytTitle').html(video.snippet.title);
                $('#ytDesc').html(video.snippet.description);
                $('#ytId').val(video.id);
                $('#ytForm').removeClass('hide');
                
            } catch(e){
                $('#ytMessage').html('No se ha podido obtener el video para '+$('#ytUrl').val());
                $('#ytMessage').removeClass('hide');
            }
            
       }, 'json');
    });
});

