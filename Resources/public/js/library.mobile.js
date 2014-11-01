var thePlaylist= new Playlist('#playlist');
var dataUrlFunction=function(node){
    switch (node.type) {
        case 'root':
            return '/ajax/' + node.id +'/' ;
        case 'artist':
            return '/ajax/' + node.id +'/';
        case 'disk':
            return '/ajax/' + node.id +'/';
        default:
            return '/ajax/types/'
    }
}

$(document).ready(function(){
    $.get('/ajax/artists/',null,function(response){
        for (var i in response){
            $("#artist-list").append($('<option>', {'value': response[i].id, 'text':response[i].text}))
        }
    },'json');
    
    $("#artist-list").change(function(){
        var id=$("#artist-list option:selected").val();
        if (id!=0){
            $.get('/ajax/'+id,null,function(response){
                $('#disk-list option[value!="0"]').remove();
                for (var i in response){
                    $("#disk-list").append($('<option>', {'value': response[i].id, 'text':response[i].text}))
                }
                $('.disk-selector').removeClass('hide');
            },'json');
        } else{
            $('.disk-selector').addClass('hide');
        }
    });
    
    $("#disk-list").change(function(){
        var id=$("#disk-list option:selected").val();
        if (id!=0){
            $.get('/ajax/'+id,null,function(response){
                $('#song-list option[value!="0"]').remove();
                for (var i in response){
                    $("#song-list").append($('<option>', {'value': response[i].id, 'text':response[i].text}))
                }
                $('.song-selector').removeClass('hide');
            },'json');
        } else{
            $('.song-selector').addClass('hide');
        }
    });
    
    $('#play-artist').click(function(){
        var id=$("#artist-list option:selected").val();
        if (id!=0){
            thePlaylist.clear();
            thePlaylist.addElement(id, $(thePlaylist), true)
        }
    });
    
    $('#add-artist').click(function(){
        var id=$("#artist-list option:selected").val();
        if (id!=0){
            thePlaylist.addElement(id, $(thePlaylist), false)
        }
    });
    
    $('#play-disk').click(function(){
        var id=$("#disk-list option:selected").val();
        if (id!=0){
            thePlaylist.clear();
            thePlaylist.addElement(id, $(thePlaylist), true)
        }
    });
    
    $('#add-disk').click(function(){
        var id=$("#disk-list option:selected").val();
        if (id!=0){
            thePlaylist.addElement(id, $(thePlaylist), false)
        }
    });
    
    $('#library-play-song').click(function(){
        var id=$("#song-list option:selected").val();
        if (id!=0){
            thePlaylist.clear();
            thePlaylist.addElement(id, $(thePlaylist), true)
        }
    });
    
    $('#library-add-song').click(function(){
        var id=$("#song-list option:selected").val();
        if (id!=0){
            thePlaylist.addElement(id, $(thePlaylist), false)
        }
    });
    
    $("#library").swipe({
        swipeUp:function(event, direction, distance, duration, fingerCount) {
            currentMargin=$('#library').css('margin-top').replace("px","");
            currentHeight=$(this).height();
            return;
            $('#library').css('margin-top', (currentMargin + duration) + 'px');
            currentMargin=$('#library').css('margin-top').replace("px","");
        },
        swipeDown:function(event, direction, distance, duration, fingerCount) {
            currentMargin=$('#library').css('margin-top').replace("px","");
            currentHeight=$(this).height();
            return;
            $('#library').css('margin-top', (currentMargin - duration) + 'px');
            currentMargin=$('#library').css('margin-top').replace("px","");
        }

    });
});