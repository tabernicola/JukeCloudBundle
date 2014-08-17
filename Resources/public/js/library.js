var thePlaylist= new Playlist('#playlist');
var defaultData={
    'url': function(node) {
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
    },
    "data": function(node) {
        return null
    }
}

$(document).ready(function(){
    $('#library').jstree({
        "core": {
            "check_callback": true,
            "themes": {"stripes": true},
            "data": defaultData,
            "li_height": 10
        },
        "types": {
            "root": {"icon": "glyphicon glyphicon-home"},
            "default": {"icon": "glyphicon glyphicon-music"},
            "artist": {"icon": "glyphicon glyphicon-headphones"},
            "disk": {"icon": "/bundles/tabernicolajukecloud/img/disc-case.png"}
        },
        "dnd":{"copy": false},
        "plugins": [ "types", "wholerow", "dnd"]
    }).on('changed.jstree', function(e, data) {
        //console.log(data);
        //console.log(e);
    });
    
    function filterLibrary(search){
            $.ajax({
                url: '/library/filter/'+encodeURIComponent(search),
                dataType: "json"
            }).done(function(data){
                $('#library').jstree(true).settings.core.data =data;
                $("#library").jstree(true).refresh();
            });
        
        
    }
    $('#clear-library-filter').click(function(){
        $('#library-filter').val('');
        $("#library").jstree("close_all");
        $('#library').jstree(true).settings.core.data =defaultData;
        $("#library").jstree(true).refresh();
    });
    
    var to = false;
    $('#library-filter').keyup(function () {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {filterLibrary($('#library-filter').val());}, 250);
    });
    
    $(document).on('dblclick','li[id^="song"] > a',function (e) {
        thePlaylist.addElement($(this).parent().attr('id'), $(thePlaylist.selector));
        //console.log($("#player").data().jPlayer.status.paused);
    });
            
    $(document).on('dnd_move.vakata', function (e, data) {
        var t = $(data.event.target);
        if(t.closest('.droppable').length) {
            data.helper.find('.jstree-icon').removeClass('jstree-er').addClass('jstree-ok');
        }
        else {
            data.helper.find('.jstree-icon').removeClass('jstree-ok').addClass('jstree-er');
        }
    })
    .on('dnd_stop.vakata', function (e, data) {
        var t = $(data.event.target);
        $.each(data.data.nodes, function(key, value){
            thePlaylist.addElement(value, t);
        });
    });
});