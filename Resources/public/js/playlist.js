var Playlist= function(selector){
    this.selector=selector
    this.nextId=0;
    this.tempId=0;
    this.shuffle=false;
    this.numElements=0;
    
    $(this.selector).sortable();
    $(this.selector).selectable();
    this.activeElement;
   
    //Capture clicks
    $(this.selector).bind( "click",function( event ) {
        var elem=$(event.target).closest('a');
        if (!event.ctrlKey){
            // add unselecting class to all elements in the styleboard canvas except the ones to select
            $(".ui-selected", $(this)).not(elem).removeClass("ui-selected").addClass("ui-unselecting");
        }
        // add ui-selecting class to the elements to select
        elem.not(".ui-selected").addClass("ui-selecting");
        // trigger the mouse stop event (this will select all .ui-selecting elements, and deselect all .ui-unselecting elements)
        $(this).data("ui-selectable")._mouseStop(null);
        $(elem).focus();
        return false;
    } );
    
    var playlist=this;
    $(document).keydown(function( event ) {
        if (event.keyCode==46){
            $(playlist.selector+' .ui-selected').each(function (index,elem){
                playlist.removeSong($(elem).parent().attr('id'));
            });
        }
    } );
    
    $(this.selector).dblclick(function( event ) {
        var t=$(event.target).closest('.droppable-element');
        if(t.length && t.data().element){
            playlist.playSong(t.attr('id'));
        };
    });
    
    $('#active-shuffle').bind("click",function( event ) {
        playlist.shuffle=true;
        $('#active-shuffle').hide();
        $('#disable-shuffle').show();
        playlist.refreshPlaylist();
    })
    
    $('#disable-shuffle').bind("click",function( event ) {
        playlist.shuffle=false;  
        $('#disable-shuffle').hide();
        $('#active-shuffle').show();
    })
}

$.extend(Playlist.prototype,{
    
    //Add element to the playlist
    addElement: function(value, where){
        var element;
        var newId='temp-'+(this.tempId++);
        var elemHtml='<div id="'+newId+'" class="list-group droppable-element">'+
            '<a href="#" class="list-group-item">'+
            '<h5 class="list-group-item-heading ">'+
            '<img src="/jstree/themes/default/throbber.gif"/> '+
            value+'</h5></a></div>';
        if(where.closest('.droppable-element').length) {
            element=$(elemHtml).insertAfter(where.closest('.droppable-element'));
        }
        else {
            element=$(elemHtml).appendTo($(this.selector));
        }
        this.numElements++;

        element.playlist=this;
        element.updateElementInfo = function(data){
            for(key in data){
                this.playlist.addSong(data[key],newId);
            }
            this.playlist.removeSong(newId);
        }
        
        
        $.ajax({
            url: '/playlist/'+value,
            dataType: "json"
        }).done(function(data){
            element.updateElementInfo(data);
        });
    },
    
    addSong: function(data, where){
        var newId='jc-pe-'+(this.nextId++);
        var elemHtml=
        '<div id="'+newId+'" class="list-group droppable-element" data-element="'+data.id+'">\n\
            <a href="#" class="list-group-item">\n\
                <div class="album-cover"><img src="'+data.icon+'"/></div>\n\
                <p class="list-group-item-text ">'+data.songTitle+'</p>\n\
                <p class="list-group-item-text ">'+data.diskTitle+'('+data.artistName+')</p>\n\
            </a>\n\
        </div>';
        
        $(elemHtml).insertAfter('#'+where);
        this.numElements++;
        $('#'+newId).trigger('jc.playlist.new-element');
    },
    
    removeSong: function(id){
        $('#'+id).remove();
        this.numElements--;
        $(this.selector).selectable( "refresh" );
    },
    
    playSong: function(id){
        t=$('#'+id);
        if (t){
            try{
                $("#player").jPlayer("setMedia",{
                    mp3: "/"+t.data().element
                }).jPlayer("play");
                if (this.activeElement){
                    $('#'+this.activeElement).removeClass('active')
                }
                this.activeElement=id;
                $('#'+this.activeElement).addClass('active')
                t.trigger('jc.playlist.play-song');
            }
            catch(e){
                $(document).trigger('jc.playlist.error.play-song');
            }
        }
    },
    
    playNext: function(){
        var current=this.activeElement;
        if (!current) {
            this.playSong($(this.selector).children().not('div[id^=temp]').first().attr('id'));
        }
        else{
            this.playSong($('#'+current).next().attr('id'));
        }
        this.refreshPlaylist();
    },
    
    getActivePosition: function(){
        try{
            return $('#'+this.activeElement).index();
        }
        catch(e){
            return 0;
        }
    },
    //Add element to the playlist
    refreshPlaylist: function(){
        if (this.shuffle){
            var pos=this.getActivePosition();
            var numSongsToAdd=10-(this.numElements-pos);
            for(var i=numSongsToAdd;i>0;i--){
                this.addElement('random?'+Math.random(),$(this.selector));
            }
            for(var i=0;i<pos-5;i++){
                this.removeSong($(this.selector+ ' div:first').attr('id'));
            }
        }
    }
});
    