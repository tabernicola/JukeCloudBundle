$(document).ready(function() {
    $('#ytAdd').click(function(){
        $('#ytForm').addClass('hide');
        $('#ytMessage').addClass('hide');
        $.post('plugin/youtube/info', {'url': $('#ytUrl').val()}, function(video){
            try{
                $('#ytImg').attr('src', video.snippet.thumbnails.medium.url);
                $('#ytTitle').html(video.snippet.title);
                $('#ytDesc').html(video.snippet.description.substring(0,500));
                $('#ytId').val(video.id);
                $('#ytForm').removeClass('hide');
                
            } catch(e){
                $('#ytMessage').removeClass().addClass("alert alert-success");
                if (video.hasOwnProperty('error')){
                    $('#ytMessageText').html(video.error);
                } else {
                    $('#ytMessageText').html('No se ha podido obtener el video para '+$('#ytUrl').val());
                }
            }
            
       }, 'json');
    });
    
    $('#ytSave').click(function(){
        var data={
            'ytId': $('#ytId').val(),
            'artist': $('#ytBand').val(),
            'disk': $('#ytDisk').val(),
            'number': $('#ytSongNumber').val(),
            'title': $('#ytSongTitle').val()
        }
        $.post('plugin/youtube/add', data, function(resp){
            if (resp.hasOwnProperty('msg')){
                $('#ytMessageText').html(resp.msg);
                $('#ytMessage').removeClass().addClass("alert alert-success");
                $('#ytForm').addClass('hide');
            }
            else if (resp.hasOwnProperty('error')){
                $('#ytMessageText').html(resp.error);
                $('#ytMessage').removeClass().addClass("alert alert-danger")
            } else {
                $('#ytMessageText').html("Se ha producido  un error inesperado, vuelve a intentarlo");
                $('#ytMessage').removeClass().addClass("alert alert-danger")
            }
        });
    });
    
    $('#ytBand').autocomplete({source: "/list/artists/"});
    $('#ytDisk').autocomplete({source: "/list/disks/"});
    $('#ytSongTitle').autocomplete({source: "/list/songs/"});
    $('#ytMessage .close').click(function(){$('#ytMessage').addClass('hide')})
});

