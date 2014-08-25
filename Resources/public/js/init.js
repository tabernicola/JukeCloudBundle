var thePlayer=$("#player").jPlayer({
        ended: function() {
            thePlaylist.playNext();
        },
        error: function () {
            thePlaylist.playNext();
        },
        cssSelectorAncestor: "#controls",
        cssSelector: {
            videoPlay: ".video-play",
            play: "#play",
            pause: "#pause",
            stop: "#stop",
            seekBar: "#seek-bar",
            playBar: "#play-bar",
            mute: "#mute",
            unmute: "#unmute",
            volumeBar: "#volume-bar",
            volumeBarValue: "#volume-bar-value",
            volumeMax: "#volume-max",
            playbackRateBar: "#playback-rate-bar",
            playbackRateBarValue: "#playback-rate-bar-value",
            currentTime: "#current-time",
            duration: "#duration",
            title: "#title",
            fullScreen: "#full-screen",
            restoreScreen: "#restore-screen",
            repeat: "#repeat",
            repeatOff: "#repeat-off",
            gui: "#gui",
            noSolution: "#no-solution"
        },
        swfPath: "/extern/jplayer/jquery.jplayer/",
        supplied: "mp3"
    });
var thePlayerData = thePlayer.data('jPlayer');
$(document).ready(function() {
    $(document).on('jc.playlist.new-element', function(e, data){
        if(Object.keys(thePlayerData.status.media).length==0){
            thePlaylist.playNext();
        };
    });
    
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '/upload',
        done: function (e, data) {
            if (data.context){
                data.context.html(data.result.files[0].response);
            }
        }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option','redirect',
        window.location.href.replace(/\/[^\/]*$/,'/cors/result.html?%s')
    );

    // Load existing files:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });

    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        var inputs = data.context.find(':input');
        if (inputs.filter(function () {
                return !this.value && $(this).prop('required');
            }).first().focus().length) {
            data.context.find('button').prop('disabled', false);
            return false;
        }
        data.formData = inputs.serializeArray();
    });
});

$('#upload-menu a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})



/*
* 
$('.slider').slider({range: "min", max: 100, value: 50, slide: updateSlider, change: updateSlider});

$('#left-panel').resizable({
 handles: 'e',
 maxWidth: 600,
 minWidth: 150,
 resize: resizeCenter,
 });
 //$('#left-panel').draggable();
 $('#right-panel').resizable({}
 handles: 'w',
 maxWidth: 600,
 minWidth: 150,
 resize: resizeRCenter
 });
$('#dial').knob({
    fgColor: '#000000',
    thickness: .2,
    displayInput: false,
    height: 60,
    angleArc: 350,
    angleOffset: -175
});

**/

/*function resizeCenter(e,ui){
$('#center-panel').css('margin-left','5px')
$('#center-panel').animate({left: ui.size.width,width: $('#right-panel').offset().left - ui.size.width -5},0) ;
}

function resizeRCenter(e,ui){
var newSize=$('#right-panel').offset().left -$('#left-panel').width()-30;
console.log($('#left-panel').width());
$('#center-panel').animate({width: newSize},0) ;
//$('#center-panel').css('margin-left','5px');
}

function updateSlider() {
console.log(1)
}

**/