/* global define, window, document */
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'load-image',
            './jquery.fileupload-process'
        ], factory);
    } else {
        // Browser globals:
        factory(
            window.jQuery,
            window.loadImage
        );
    }
}(function ($, loadImage) {
    'use strict';

    // Prepend to the default processQueue:
    $.blueimp.fileupload.prototype.options.processQueue.unshift(
        {
            action: 'loadAudio',
            // Use the action as prefix for the "@" options:
            prefix: true,
            fileTypes: '@',
            maxFileSize: '@',
            disabled: '@disableAudioPreview'
        },
        {
            action: 'setId',
            name: '@audioPreviewName',
            disabled: '@disableAudioPreview'
        }
    );

    // The File Upload Audio Preview plugin extends the fileupload widget
    // with audio preview functionality:
    $.widget('blueimp.fileupload', $.blueimp.fileupload, {

        options: {
            // The regular expression for the types of audio files to load,
            // matched against the file type:
            loadAudioFileTypes: /^audio\/.*$/
        },

        _audioElement: document.createElement('audio'),

        processActions: {

            // Loads the audio file given via data.files and data.index
            // as audio element if the browser supports playing it.
            // Accepts the options fileTypes (regular expression)
            // and maxFileSize (integer) to limit the files to load:
            loadAudio: function (data, options) {
                if (options.disabled) {
                    return data;
                }
                var file = data.files[data.index];
                if (this._audioElement.canPlayType && this._audioElement.canPlayType(file.type) &&
                    ($.type(options.maxFileSize) !== 'number' || file.size <= options.maxFileSize) &&
                    (!options.fileTypes || options.fileTypes.test(file.type))) {
                    ID3v2.parseFile(file,function(tags){
                        var id=file.name.replace(/ /g,'-').replace(/[^\w-]+/g,'');
                        console.log(tags);
                        $('#form-band-'+id).val(tags.Band).autocomplete({source: "/list/artists/"});
                        $('#form-disk-'+id).val(tags.Album).autocomplete({source: "/list/disks/"});
                        $('#form-song-'+id).val(tags.Title).autocomplete({source: "/list/songs/"});
                        $('#form-number-'+id).val(tags["Track number"]);
                    })
                }
                return data;
            },

            // Sets the audio element as a property of the file object:
            setId: function (data, options) {
                console.log('setid');
                if (data.id && !options.disabled) {
                                    console.log(data.id);

                    data.files[data.index]['id'] = data.id;
                }
                return data;
            }

        }

    });

}));
