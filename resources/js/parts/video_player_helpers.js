var fileVideoPlayer;

window.makeVideoPlayerHtml = function (path, storage, height, tagId) {
    let html = '';
    let options = {
        autoplay: false,
        preload: 'auto',
    };

    if (storage === 'youtube' || storage === 'vimeo') {
        html = '<video id="' + tagId + '" class="video-js" width="100%" height="' + height + '"></video>';

        options = {
            controls: (storage !== 'vimeo'),
            ytControls: true,
            autoplay: false,
            preload: 'auto',
            techOrder: ['html5', storage],
            sources: [{
                src: path,
                type: "video/" + storage
            }]
        };
    } else if (storage === "secure_host") {
        html = '<iframe src="' + path + '" class="img-cover bg-gray200" frameborder="0" allowfullscreen="true" loading="lazy" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"></iframe>';
    } else {
        html = '<video id="' + tagId + '" oncontextmenu="return false;" controlsList="nodownload" class="video-js" controls preload="auto" width="100%" height="' + height + '"><source src="' + path + '" type="video/mp4"/></video>';
    }

    return {
        html: html,
        options: options,
    };
};

window.handleVideoByFileId = function (fileId, $contentEl, callback) {

    closeVideoPlayer();

    const height = $(window).width() > 991 ? 426 : 264;

    $.post('/course/getFilePath', {file_id: fileId}, function (result) {

        if (result && result.code === 200) {
            const storage = result.storage;

            const videoTagId = 'videoPlayer' + fileId;

            const {html, options} = makeVideoPlayerHtml(result.path, storage, height, videoTagId);

            if ($contentEl) {
                $contentEl.html(html);
            }

            if (storage !== "secure_host") {
                fileVideoPlayer = videojs(videoTagId, options);
            }

            callback();
        } else {
            $.toast({
                heading: notAccessToastTitleLang,
                text: notAccessToastMsgLang,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });
        }
    }).fail(err => {
        $.toast({
            heading: notAccessToastTitleLang,
            text: notAccessToastMsgLang,
            bgColor: '#f63c3c',
            textColor: 'white',
            hideAfter: 10000,
            position: 'bottom-right',
            icon: 'error'
        });
    });
};

window.closeVideoPlayer = function () {
    if (fileVideoPlayer !== undefined) {
        fileVideoPlayer.dispose();
    }
};

window.pauseVideoPlayer = function () {
    if (fileVideoPlayer !== undefined) {
        fileVideoPlayer.pause();
    }
};


