(function ($) {
    // static function, making them public
    $.extend(true, $.jPlayer.prototype, {
        getStatus: function () { return this.data('jPlayer').status.media;},
        setSong:function (song){ return this.setMedia({mp3: "/"+song });},
        canPlay: function (type){ return (type == 'local')? true: false;},
        format: { // Static Object
			mp3: {
				codec: !(new Audio().canPlayType('audio/mpeg; codecs="mp3"')) ? 'audio/mp4; codecs="mp4a.40.2"' : 'audio/mpeg; codecs="mp3"',
                flashCanPlay: true,
                media: 'audio'
            },
			m4a: { // AAC / MP4
				codec: 'audio/mp4; codecs="mp4a.40.2"',
				flashCanPlay: true,
				media: 'audio'
			},
			m3u8a: { // AAC / MP4 / Apple HLS
				codec: 'application/vnd.apple.mpegurl; codecs="mp4a.40.2"',
				flashCanPlay: false,
				media: 'audio'
			},
			m3ua: { // M3U
				codec: 'audio/mpegurl',
				flashCanPlay: false,
				media: 'audio'
			},
			oga: { // OGG
				codec: 'audio/ogg; codecs="vorbis, opus"',
				flashCanPlay: false,
				media: 'audio'
			},
			flac: { // FLAC
				codec: 'audio/x-flac',
				flashCanPlay: false,
				media: 'audio'
			},
			wav: { // PCM
				codec: 'audio/wav; codecs="1"',
				flashCanPlay: false,
				media: 'audio'
			},
			webma: { // WEBM
				codec: 'audio/webm; codecs="vorbis"',
				flashCanPlay: false,
				media: 'audio'
			},
			fla: { // FLV / F4A
				codec: 'audio/x-flv',
				flashCanPlay: true,
				media: 'audio'
			},
			rtmpa: { // RTMP AUDIO
				codec: 'audio/rtmp; codecs="rtmp"',
				flashCanPlay: true,
				media: 'audio'
			},
			m4v: { // H.264 / MP4
				codec: 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
				flashCanPlay: true,
				media: 'video'
			},
			m3u8v: { // H.264 / AAC / MP4 / Apple HLS
				codec: 'application/vnd.apple.mpegurl; codecs="avc1.42E01E, mp4a.40.2"',
				flashCanPlay: false,
				media: 'video'
			},
			m3uv: { // M3U
				codec: 'audio/mpegurl',
				flashCanPlay: false,
				media: 'video'
			},
			ogv: { // OGG
				codec: 'video/ogg; codecs="theora, vorbis"',
				flashCanPlay: false,
				media: 'video'
			},
			webmv: { // WEBM
				codec: 'video/webm; codecs="vorbis, vp8"',
				flashCanPlay: false,
				media: 'video'
			},
			flv: { // FLV / F4V
				codec: 'video/x-flv',
				flashCanPlay: true,
				media: 'video'
			},
			rtmpv: { // RTMP VIDEO
				codec: 'video/rtmp; codecs="rtmp"',
				flashCanPlay: true,
				media: 'video'
			}
		},
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
