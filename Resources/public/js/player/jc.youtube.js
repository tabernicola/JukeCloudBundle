/*
 * jQuery Jukecloud Youtube plugin
 *
 * Requires: jQuery v1.8+
 * MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

;
(function ($, undefined) {

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
                var instance = $(this).data(name);
                if (instance) {
                    // instance.option( options || {} )._init(); // Orig jquery.ui.widget.js code: Not recommend for jPlayer. ie., Applying new options to an existing instance (via the jPlayer constructor) and performing the _init(). The _init() is what concerns me. It would leave a lot of event handlers acting on jPlayer instance and the interface.
                    instance.option(options || {}); // The new constructor only changes the options. Changing options only has basic support atm.
                } else {
                    $(this).data(name, new $.jukeCloudYoutubePlayer(options, this));
                }
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
            this._init();
        }
    };

    $.jukeCloudYoutubePlayer.prototype = {
        setMedia: function(){
            // Generate the required media elements
            var tag = document.createElement('script');
            tag.src = "http://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            var yt_player_1;
        },
        
        play: function() {
            
        },
        
        _init: function () {
            var self = this;
            this.element.empty();

            this.css = {};
            this.css.cs = {}; // Holds the css selector strings
            this.css.jq = {}; // Holds jQuery selectors. ie., $(css.cs.method)

            this.options.volume = this._limitValue(this.options.volume, 0, 1); // Limit volume value's bounds.



            // Register listeners defined in the constructor
            $.each($.jPlayer.event, function (eventName, eventType) {
                if (self.options[eventName] !== undefined) {
                    self.element.bind(eventType + ".jPlayer", self.options[eventName]); // With .jPlayer namespace.
                    self.options[eventName] = undefined; // Destroy the handler pointer copy on the options. Reason, events can be added/removed in other ways so this could be obsolete and misleading.
                }
            });

            this._setSize(); // update status and jPlayer element size

            // Create event handlers if native fullscreen is supported
            if ($.jPlayer.nativeFeatures.fullscreen.api.fullscreenEnabled) {
                this._fullscreenAddEventListeners();
            }

            




            // The HTML Audio handlers
            if (this.html.audio.available) {
                this._addHtmlEventListeners(this.htmlElement.audio, this.html.audio);
                this.element.append(this.htmlElement.audio);
                this.internal.audio.jq = $("#" + this.internal.audio.id);
            }

            // The HTML Video handlers
            if (this.html.video.available) {
                this._addHtmlEventListeners(this.htmlElement.video, this.html.video);
                this.element.append(this.htmlElement.video);
                this.internal.video.jq = $("#" + this.internal.video.id);
                if (this.status.nativeVideoControls) {
                    this.internal.video.jq.css({'width': this.status.width, 'height': this.status.height});
                } else {
                    this.internal.video.jq.css({'width': '0px', 'height': '0px'}); // Using size 0x0 since a .hide() causes issues in iOS
                }
                this.internal.video.jq.bind("click.jPlayer", function () {
                    self._trigger($.jPlayer.event.click);
                });
            }



            if (this.html.used && !this.flash.used) { // If only HTML, then emulate flash ready() call after 100ms.
                setTimeout(function () {
                    self.internal.ready = true;
                    self.version.flash = "n/a";
                    self._trigger($.jPlayer.event.repeat); // Trigger the repeat event so its handler can initialize itself with the loop option.
                    self._trigger($.jPlayer.event.ready);
                }, 100);
            }

            // Initialize the interface components with the options.
            this._updateNativeVideoControls();

        },
        
        onYouTubeIframeAPIReady: function () {
            yt_player_1 = new YT.Player('yt_player_1', {
                height: '540',
                width: '960',
                videoId: 'iABUHt-Z9Gc',
                playerVars: {
                    'autohide': 1,
                    'autoplay': 0,
                    'controls': 0,
                    'fs': 1,
                    'disablekb': 0,
                    'modestbranding': 1,
                    // 'cc_load_policy': 1, // forces closed captions on
                    'iv_load_policy': 3, // annotations, 1=on, 3=off
                    // 'playlist': videoID, videoID, videoID, etc,
                    'rel': 0,
                    'showinfo': 0,
                    'theme': 'dark', // dark, light
                    'color': 'red'	// red, white
                },
                events: {
                    'onReady': onPlayerReady,
                    'onPlaybackQualityChange': onPlayerPlaybackQualityChange,
                    'onPlaybackRateChange': onPlaybackRateChange,
                    'onStateChange': onPlayerStateChange,
                    'onError': onPlayerError
                }
            });
        },

        onPlayerReady: function (data) {
            initializeJplayerControls();
            yt_player_1.setVolume(80);
            $('#player .jp-volume-bar .jp-volume-bar-value').width('80%');
        },

        onPlayerStateChange: function (state) {
            switch (state.data) {
                case -1: //unstarted
                    /* do something */
                    break;
                case 0: // ended
                    $('#player .jp-pause').show();
                    $('#player .jp-play').hide();
                    break;
                case 1: // playing
                    $('#player .jp-pause').show();
                    $('#player .jp-play').hide();
                    startYoutubeTime();
                    break;
                case 2: // paused
                    $('#player .jp-pause').hide();
                    $('#player .jp-play').show();
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
            console.log(error);
        },

        youtubeFeedCallback: function (data) {
            jQuery(document).ready(function () {
                $('#player .jp-duration').text(
                Math.floor(data.entry['media$group']['yt$duration'].seconds / 60) + ':' + (data.entry['media$group']['yt$duration'].seconds % 60));
            });
        } ,

        youTubeFrequency: 100,
        youTubeInterval: 0,
        startYoutubeTime:function () {
            if (youTubeInterval > 0)
                clearInterval(youTubeInterval); // stop
            youTubeInterval = setInterval("updateYoutubeTime()", youTubeFrequency); // run
        },
        
        updateYoutubeTime:function () {
            if (yt_player_1.getCurrentTime() >= 60) {
                $('#player .jp-current-time').text(Math.floor(yt_player_1.getCurrentTime() / 60) + ':' + (yt_player_1.getCurrentTime() % 60));
            } else {
                $('#player .jp-current-time').text('0:' + FormatNumberLength(Math.round(yt_player_1.getCurrentTime()), 2));
                $('#player .jp-progress .jp-play-bar').width(Math.round((yt_player_1.getCurrentTime() / yt_player_1.getDuration()) * 100) + '%');
            }
        },
        
        FormatNumberLength: function (num, length) {
            var r = "" + num;
            while (r.length < length) {
                r = "0" + r;
            }
            return r;
        },

        initializeJplayerControls: function () {
            $('#player .jp-pause').hide();
            $('#player .jp-unmute').hide();
            $('#player .jp-restore-screen').hide();
            $('#player .jp-play').live('click', function () {
                $(this).hide();
                $('#player .jp-pause').show();
                yt_player_1.playVideo();
            });
            $('#player .jp-pause').live('click', function () {
                $(this).hide();
                $('#player .jp-play').show();
                yt_player_1.pauseVideo();
            });
            $('#player .jp-full-screen').live('click', function () {
                $(this).hide();
                $('#player .jp-restore-screen').show();
                $('#player').addClass('jp-video-full');
                $('#player .jp-jplayer, #player #yt_player_1').css({'width': '100%', 'height': '100%'});
            });
            $('#player .jp-restore-screen').live('click', function () {
                $(this).hide();
                $('#player .jp-full-screen').show();
                $('#player').removeClass('jp-video-full');
                $('#player .jp-jplayer, #player #yt_player_1').removeAttr('style');
                $('#player .jp-gui').show();
                clearTimeout(fullScreenHoverTime);
            });
            $('#player .jp-mute').live('click', function () {
                $(this).hide();
                $('#player .jp-unmute').show();
                $('#player .jp-volume-bar .jp-volume-bar-value').hide();
                yt_player_1.mute();
            });
            $('#player .jp-unmute').live('click', function () {
                $(this).hide();
                $('#player .jp-mute').show();
                $('#player .jp-volume-bar .jp-volume-bar-value').show();
                yt_player_1.unMute();
            });
            $('#player .jp-volume-bar').click(function (e) {
                var posX = $(this).offset().left, posWidth = $(this).width();
                posX = (e.pageX - posX) / posWidth;
                $('#player .jp-volume-bar .jp-volume-bar-value').width((posX * 100) + '%').show();
                yt_player_1.setVolume(posX * 100);
                $('#player .jp-unmute').hide();
                $('#player .jp-mute').show();
            });
            $('#player .jp-seek-bar').click(function (e) {
                var posX = $(this).offset().left, posWidth = $(this).width();
                posX = (e.pageX - posX) / posWidth;
                $('#player .jp-progress .jp-play-bar').width((posX * 100) + '%');
                posX = Math.round((posX) * yt_player_1.getDuration());
                yt_player_1.seekTo(posX, true);
            });
            $("#player.jp-video-full .jp-gui").live('click', function (e) {
                if (e.target != this)
                    return;
                if ($('#player .jp-play').is(':visible')) {
                    $('#player .jp-play').click();
                } else {
                    $('#player .jp-pause').click();
                }
            });
            var fullScreenHoverTime;
            $("#player.jp-video-full").live('mouseover', function () {
                $('.jp-gui', this).show();
                clearTimeout(fullScreenHoverTime);
                fullScreenTimeout();
            });
            
            function fullScreenTimeout() {
                fullScreenHoverTime = setTimeout(function () {
                    $('#player .jp-gui').hide();
                }, 5000);
            }
       }
    }
});
abstractPlayerFactory.registerPlayer('jukeCloudYoutubePlayer', $.fn.jPlayer);

