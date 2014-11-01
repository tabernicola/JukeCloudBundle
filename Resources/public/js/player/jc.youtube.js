/*
 * jQuery Jukecloud Youtube plugin
 *
 * Requires: jQuery v1.8+
 * MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

;
$.fn.jukeCloudYoutubePlayer = function (options) {
    var name = "jukeCloudYoutubePlayer";
    var isMethodCall = typeof options === "string",
            args = Array.prototype.slice.call(arguments, 1),
            returnValue = this;
    // allow multiple hashes to be passed on init
    options = !isMethodCall && args.length ? $.extend.apply(null, [true, options].concat(args)) : options;
    // prevent calls to internal methods
    if (isMethodCall && options.charAt(0) === "_") {
        return returnValue;
    }

    if (isMethodCall) {
        this.each(function () {
            var instance = $(this).data(name),
                    methodValue = instance && $.isFunction(instance[options]) ?
                    instance[ options ].apply(instance, args) :
                    instance;
            if (methodValue !== instance && methodValue !== undefined) {
                returnValue = methodValue;
                return false;
            }
        });
    } else {
        this.each(function () {
            $(this).data(name, new $.jukeCloudYoutubePlayer(options, this));
        });
    }

    return returnValue;
};
$.jukeCloudYoutubePlayer = function (options, element) {
    // allow instantiation without initializing for simple inheritance
    if (arguments.length) {
        this.element = $(element);
        this.options = $.extend(true, {},
                this.options,
                options
                );
        var self = this;
        this.element.bind("remove.jukeCloudYoutubePlayer", function () {
            self.destroy();
        });
        this.media = '';
        this.player = null;
        this.timer=null;
        this._init();
    }
};
$.jukeCloudYoutubePlayer.prototype = {
    mediaReady: false,
    setSong: function (mediaObj) {
        this.mediaReady = false;
        this.media = mediaObj;
    },
    play: function () {
        if (this.mediaReady) {
            $("#play").hide();
            $('#pause').show();
            this.player.playVideo();
        } else {
            var self = this;
            if (!this.media){
                return this.onPlayerError("No media");
            } 
            $.get('/' + this.media, function (data) {
                if (self.player){
                    clearInterval(self.timer);
                    self.player.destroy();
                }

                self.player = new YT.Player('browser-video', {
                    height: '90',
                    width: '160',
                    videoId: data,
                    playerVars: {
                        'autoplay': 0,
                        'controls': 0,
                        'enablejsapi': 1,
                        'modestbranding': 1,
                        // 'cc_load_policy': 1, // forces closed captions on
                        'iv_load_policy': 3, // annotations, 1=on, 3=off
                        'rel': 0,
                        'showinfo': 0,
                        'theme': 'dark', // dark, light
                        'color': 'red'	// red, white
                    },
                    events: {
                        'onReady': function () {
                            self.player.setVolume(Math.round($('#volume-bar-value').width()/$('#volume-bar').width())*100);
                            self.mediaReady = true;
                            $('#duration').text(self.getFormatedTime(self.player.getDuration()));
                            $('#seek-bar').width('100%');
                            self.play();
                            self.timer = window.setInterval(function () {
                                $('#current-time').text(self.getFormatedTime(self.player.getCurrentTime()));
                                $('#play-bar').width(Math.round((self.player.getCurrentTime() / self.player.getDuration()) * 100) + '%');
                            }, 1000);
                        },
                        'onStateChange': function (state) {
                            switch (state.data) {
                                case -1: //unstarted
                                    /* do something */
                                    break;
                                case 0: // ended
                                    $('#pause').show();
                                    $('#play').hide();
                                    clearInterval(self.timer);
                                    thePlaylist.playNext();
                                    break;
                                case 1: // playing
                                    $('#pause').show();
                                    $('#play').hide();
                                    break;
                                case 2: // paused
                                    $('#pause').hide();
                                    $('#play').show();
                                    break;
                                case 3: // buffering
                                    /* do something */
                                    break;
                                case 5: // video cued
                                    /* do something */
                                    break;
                                default:
                                    // do nothing
                            }
                        },
                        'onError': function(){
                            thePlaylist.playNext();
                        }
                    }
                });
            });
        }
    },
    pause: function () {
        if (this.mediaReady) {
            $("#play").show();
            $('#pause').hide();
            this.player.pauseVideo();
        }
    },
    mute: function () {
        $('#mute').hide();
        $('#unmute').show();
        $('#volume-bar .jp-volume-bar-value').hide();
        this.player.mute();
    },
    unmute: function () {
        $('#unmute').hide();
        $('#mute').show();
        $('#volume-bar .jp-volume-bar-value').show();
        this.player.unmute();
    },
    setVolume: function (vol) {
        $('#volume-bar-value').width(vol + '%').show();
        $('#unmute').hide();
        $('#mute').show();
        this.player.setVolume(vol);
    },
    seekTo: function (percent) {
        $('#play-bar').width((percent * 100) + '%');
        var second = Math.round((percent) * this.player.getDuration());
        this.player.seekTo(second, true);
    },
    getFormatedTime: function (seconds) {
        if (seconds >= 60) {
            return (
                    this.FormatNumberLength(Math.floor(seconds / 60), 2) + ':' +
                    this.FormatNumberLength(Math.round(seconds % 60), 2));
        } else {
            return '00:' + this.FormatNumberLength(Math.round(seconds), 2);
        }
    },
    _init: function () {
        // Initialize the interface components with the options.
        this.initializeJukeCloudYoutubePlayerControls(this);
    },
    onPlayerStateChange: function (state) {
        switch (state.data) {
            case -1: //unstarted
                /* do something */
                break;
            case 0: // ended
                $('#pause').show();
                $('#play').hide();
                break;
            case 1: // playing
                $('#pause').show();
                $('#play').hide();
                startYoutubeTime();
                break;
            case 2: // paused
                $('#pause').hide();
                $('#play').show();
                break;
            case 3: // buffering
                /* do something */
                break;
            case 5: // video cued
                /* do something */
                break;
            default:
                // do nothing
        }
    },
    onPlayerError: function (error) {
        thePlaylist.playNext();
    },
    youtubeFeedCallback: function (data) {
        jQuery(document).ready(function () {
            $('#duration').text(
                    Math.floor(data.entry['media$group']['yt$duration'].seconds / 60) + ':' + (data.entry['media$group']['yt$duration'].seconds % 60));
        });
    },
    youTubeFrequency: 100,
    youTubeInterval: 0,
    FormatNumberLength: function (num, length) {
        var r = "" + num;
        while (r.length < length) {
            r = "0" + r;
        }
        return r;
    },
    initializeJukeCloudYoutubePlayerControls: function (player) {
        $('#pause').hide();
        $('#unmute').hide();
        $('#play').bind('click', function () {
            player.play();
        });
        $('#pause').bind('click', function () {
            player.pause();
        });
        $('#mute').bind('click', function () {
            player.mute();
        });
        $('#unmute').bind('click', function () {
            player.unMute();
        });
        $('#volume-bar').click(function (e) {
            var posX = $(this).offset().left, posWidth = $(this).width();
            posX = (e.pageX - posX) / posWidth;
            player.setVolume(posX * 100);
        });
        $('#seek-bar').click(function (e) {
            var posX = $(this).offset().left, posWidth = $(this).width();
            posX = (e.pageX - posX) / posWidth;
            player.seekTo(posX, true);
        });
        $("#browser-video").bind('click', function (e) {
            if (e.target != this)
                return;
            if ($('#play').is(':visible')) {
                $('#play').click();
            } else {
                $('#pause').click();
            }
        });
    },
    destroy: function () {
        $('#play').unbind('click');
        $('#pause').unbind('click');
        $('#mute').unbind('click');
        $('#unmute').unbind('click');
        $('#volume-bar').unbind('click');
        $('#seek-bar').unbind('click');
        $("#browser-video").unbind('click');
        clearInterval(this.timer);
        //$("#browser-video").replaceWith('<div id="browser-video"></div>')
        this.player.destroy();

        delete this;
    }
}

playerManager.registerPlayer('youtube', 'jukeCloudYoutubePlayer');

