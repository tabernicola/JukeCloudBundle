(function ($) {
    // static function, making them public
    $.extend($.jPlayer, {
        getStatus: function () { return $.fn.jPlayer.data('jPlayer').status.media;},
        setSong:function (song){ console.log(song);return $.fn.jPlayer("setMedia",{mp3: "/"+song });},
        canPlay: function (type){ return (type == 'local')? true: false;}
    });
})(jQuery);

playerManager.registerPlayer('local', "jPlayer", {
    ended: function () {
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
} );
