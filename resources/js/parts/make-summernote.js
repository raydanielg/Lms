(function ($) {
    "use strict";

    var lfm = function (options, cb) {
        var route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';
        window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=900,height=600');
        window.SetUrl = cb;
    };

    var LFMButton = function (context) {
        var ui = $.summernote.ui;
        var button = ui.button({
            contents: '<i class="note-icon-picture"></i> ',
            tooltip: 'Insert image with filemanager',
            click: function () {

                lfm({type: 'file', prefix: '/laravel-filemanager'}, function (lfmItems, path) {
                    lfmItems.forEach(function (lfmItem) {
                        context.invoke('insertImage', lfmItem.url);
                    });
                });

            }
        });
        return button.render();
    };

    window.makeSummernote = function ($content, cardHeight = null, onChange = undefined) {
        const height = cardHeight ? cardHeight : ($content.attr('data-height') ? $content.attr('data-height') : 300);

        $content.summernote({
            dialogsInBody: true,
            tabsize: 2,
            height: height,
            placeholder: $content.attr('placeholder'),
            fontNames: [],
            callbacks: {
                onChange: onChange
            },
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'video']],
                ['view', ['codeview', 'help']],
                ['popovers', ['lfm']],
                ['paperSize', ['paperSize']], // The Button
            ],
            buttons: {
                lfm: LFMButton
            }
        });
    }

})(jQuery);
