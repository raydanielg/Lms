(function ($) {
    $('body').on('click', '.panel-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'});
    });

    const learningPageContent = $('#learningPageContent');

    // disable right click
    document.addEventListener('contextmenu', event => event.preventDefault(), false);

    $(document).ready(function () {
        const allItems = $('.tab-item');

        if (allItems && allItems.length && defaultItemType && defaultItemType !== '' && defaultItemId && defaultItemId !== '') {
            for (const item of allItems) {
                const $item = $(item);
                const type = $item.attr('data-type');
                const id = $item.attr('data-id');

                if (type === defaultItemType && id === defaultItemId) {
                    $item.trigger('click');

                    const collapse = $item.closest('.collapse');

                    if (collapse) {
                        collapse.collapse('show');
                    }
                }
            }
        } else if (allItems && loadFirstContent && loadFirstContent !== 'false') {
            if (allItems.length) {
                const item = allItems[0];

                const $item = $(item);

                $item.trigger('click');

                const collapse = $item.closest('.collapse');

                if (collapse) {
                    collapse.collapse('show');
                }
            } else {
                contentEmptyStateHtml();
            }
        }
    });


    $('body').on('click', '#collapseBtn', function () {
        const $tabs = $('.learning-page-tabs');

        $tabs.toggleClass('show');
    });

    if ($(window).width() < 992) {
        $('.learning-page-tabs').removeClass('show')
    }

    $('body').on('click', '.nav-item a', function () {
        const $tabs = $('.learning-page-tabs');

        if (!$tabs.hasClass('show')) {
            $tabs.addClass('show');
        }
    });

    $('body').on('click', '.tab-item', function () {
        const $this = $(this);

        if (!$this.hasClass('active')) {
            const type = $this.attr('data-type');
            const id = $this.attr('data-id');

            //
            $('.tab-item-info').slideUp();
            $('.tab-item').removeClass('active');
            $('.certificate-item').removeClass('active');

            $this.addClass('active');
            $this.find('.tab-item-info').slideDown();

            if ($(window).width() < 992) {
                $('.learning-page-tabs').removeClass('show')
            }

            if (type !== 'assignment') {
                addContentLoading();

                handleContent(id, type);
            }
        }
    });

    $('body').on('click', '#checkAgainSession', function () {
        const $this = $(this);

        const type = $this.attr('data-type');
        const id = $this.attr('data-id');

        addContentLoading();

        handleContent(id, type);
    });

    $('body').on('click', '.certificate-item', function () {
        const $this = $(this);

        $('.course-certificate-item').removeClass('active');
        $('.certificate-item').removeClass('active');
        $('.tab-item').removeClass('active');
        $this.addClass('active');

        const result = $this.attr('data-result');

        handleDownloadCertificateHtml(result);
    });

    $('body').on('click', '.course-certificate-item', function () {
        const $this = $(this);

        $('.course-certificate-item').removeClass('active');
        $('.certificate-item').removeClass('active');
        $('.tab-item').removeClass('active');
        $this.addClass('active');

        const id = $this.attr('data-course-certificate');

        handleCourseCertificateHtml(id);
    });


    function handleContent(itemId, itemType) {

        const data = {
            type: itemType,
            id: itemId,
        };

        $.post('/course/learning/itemInfo', data, function (result) {
            if (itemType === 'session') {
                const {session} = result;

                if (session) {
                    if (session.is_finished) {
                        handleLiveSessionFinishedHtml(session);
                    } else if (session.is_started) {
                        handleLiveSessionHtml(session);
                    } else {
                        handleLiveSessionNotStartedHtml(session);
                    }
                }
            } else if (itemType === 'file') {
                handleFileHtml(result.file); // if file is downloadable
            } else if (itemType === 'text_lesson') {
                handleTextLessonHtml(result.textLesson);
            } else if (itemType === 'quiz') {
                handleQuizHtml(result.quiz);
            }
        });
    }


    function handlePersonalNoteHtml(item) {
        if (courseNotesStatus === '1') {
            return `<div class="js-personal-notes-form learning-page-personal-note bg-white mt-15 p-10 rounded-sm" data-item-id="${item.id}" data-item-type="${item.modelName}" data-course-id="${item.webinar_id}">
                <h4 class="font-14 font-weight-bold text-secondary">${personalNoteLang}</h4>
                <p class="mt-5 font-12 text-gray">${personalNoteHintLang}</p>

                <textarea name="notes" rows="5" class="form-control mt-5">${(item.personalNote && item.personalNote.note) ? item.personalNote.note : ''}</textarea>

                ${
                    (courseNotesShowAttachment) ?
                        `<div class="form-group mt-15">
                            <label class="input-label">${attachmentLang}</label>

                            <div class="input-group mr-10">
                                <div class="input-group-prepend">
                                    <button type="button" class="input-group-text panel-file-manager" data-input="personalNotesAttachment" data-preview="holder">
                                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                    </button>
                                </div>
                                <input type="text" name="attach" id="personalNotesAttachment" value="${(item.personalNote && item.personalNote.attachment) ? item.personalNote.attachment : ''}" class="form-control" placeholder=""/>

                                ${
                                    (item.personalNote && item.personalNote.attachment) ?
                                        `<div class="input-group-append">
                                            <a href="/course/personal-notes/${item.personalNote.id}/download-attachment" target="_blank" class="input-group-text">
                                                <i data-feather="download" width="18" height="18" class="text-white"></i>
                                            </a>
                                        </div>` : ''
                                }

                            </div>
                        </div>`
                        : ''
                }

                <div class="d-flex align-items-center mt-15">
                    <button type="button" class="js-save-personal-note btn btn-sm btn-primary">${saveNoteLang}</button>
                    ${
                (item.personalNote && item.personalNote.note) ?
                    '<button type="button" class="js-clear-personal-note btn btn-sm btn-danger ml-2">' + clearNoteLang + '</button>'
                    : ''
            }
                </div>
            </div>`;
        }

        return '';
    }

    function handleDownloadCertificateHtml(result) {

        const title = downloadCertificateLang;
        const hint = enjoySharingYourCertificateWithOthersLang;
        const img = 'quiz.svg';

        let otherHtml = '';

        if (result && result !== '') {
            otherHtml = `
                <a href="/panel/quizzes/results/${result}/showCertificate" target="_blank" class="btn btn-primary btn-sm mt-15">${downloadLang}</a>
            `;
        } else {
            otherHtml = `
                <button type="button" disabled class="btn btn-primary btn-sm mt-15">${downloadLang}</button>
            `;
        }

        const html = handleContentBoxHtml(title, hint, img, otherHtml);

        learningPageContent.html(html);
    }

    function handleCourseCertificateHtml(id) {

        const title = downloadCertificateLang;
        const hint = enjoySharingYourCertificateWithOthersLang;
        const img = 'quiz.svg';

        let otherHtml = '';

        if (id && id !== '') {
            otherHtml = `
                <a href="/panel/certificates/webinars/${id}/show" target="_blank" class="btn btn-primary btn-sm mt-15">${showLang}</a>
            `;
        } else {
            otherHtml = `
                <button type="button" disabled class="btn btn-primary btn-sm mt-15">${showLang}</button>
            `;
        }

        const html = handleContentBoxHtml(title, hint, img, otherHtml);

        learningPageContent.html(html);
    }

    function handleQuizHtml(quiz) {

        let title = quiz.title;
        let hint = goToTheQuizPageForMoreInformationLang;
        const img = 'quiz.svg';

        let otherHtml = '';

        if (quiz.has_expired) {
            title = expiredQuizLang;
            hint = quiz.expired_message;
        } else {
            if (quiz.expire_time_message && quiz.expire_time_message !== "null") {
                otherHtml += `<div class="mt-10 font-14 text-gray">${quiz.expire_time_message}</div>`;
            }

            if (quiz.can_try) {
                otherHtml += `
                <a href="/panel/quizzes/${quiz.id}/start" target="_blank" class="btn btn-primary btn-sm mt-15">${quizPageLang}</a>
            `;
            } else {
                otherHtml += `
                <button type="button" class="js-cant-start-quiz-toast btn btn-primary btn-sm mt-15 disabled">${quizPageLang}</button>
            `;
            }
        }

        let html = handleContentBoxHtml(title, hint, img, otherHtml);

        html += handlePersonalNoteHtml(quiz);

        learningPageContent.html(html);

        feather.replace();
    }

    function handleLiveSessionFinishedHtml(session) {

        const title = sessionIsFinishedLang;
        const hint = sessionIsFinishedHintLang;
        const img = 'live_session.svg';
        const otherHtml = `
                <a href="${courseUrl}" class="btn btn-white btn-sm mt-15">${coursePageLang}</a>
        `;

        let html = handleContentBoxHtml(title, hint, img, otherHtml, 'mt-10');

        html += handlePersonalNoteHtml(session);

        learningPageContent.html(html);

        feather.replace();
    }

    function handleLiveSessionNotStartedHtml(session) {

        const title = sessionIsNotStartedYetLang;
        const hint = thisSessionWillBeStartedOnLang + ' ' + session.start_data;
        const img = 'live_session.svg';
        const otherHtml = `
            <div class="d-flex align-items-center mt-15">
                <button type="button" id="checkAgainSession" data-type="session" data-id="${session.id}" class="btn btn-primary btn-sm ">${checkAgainLang}</button>
                <a href="${courseUrl}" class="btn btn-white btn-sm ml-10">${coursePageLang}</a>
            </div>
        `;

        let html = handleContentBoxHtml(title, hint, img, otherHtml, 'mt-10');

        html += handlePersonalNoteHtml(session);

        learningPageContent.html(html);

        feather.replace();
    }

    function handleLiveSessionHtml(session) {

        const title = sessionIsLiveLang;
        const hint = youCanJoinTheLiveNowLang;
        const img = 'live_session.svg';

        let otherHtml = "";

        if (session.password) {
            otherHtml += `<div class="font-14 font-weight-500 text-gray mt-5">${passwordLang}: ${session.password}</div>`
        }


        otherHtml += `
            <div class="d-flex align-items-center mt-15">
                <a href="${session.join_url}" target="_blank" class="btn btn-primary btn-sm ">${joinTheClassLang}</a>
                <a href="${courseUrl}" class="btn btn-white btn-sm ml-10">${coursePageLang}</a>
            </div>
        `;

        let html = handleContentBoxHtml(title, hint, img, otherHtml, 'mt-10');

        html += handlePersonalNoteHtml(session);

        learningPageContent.html(html);

        feather.replace();
    }

    function handleFileHtml(file) {

        if ((file.online_viewer && file.online_viewer !== '0') || (file.downloadable && file.downloadable !== '0')) {

            if ((file.online_viewer && file.online_viewer !== '0')) {
                let html = `<div class="d-flex flex-column p-10 h-100">`;

                html += `<iframe src="/ViewerJS/index.html#${file.file_path}" class="file-online-viewer rounded-sm ${(file.downloadable && file.downloadable !== '0') ? 'has-download-card' : ''}" frameborder="0" allowfullscreen></iframe>`;

                if ((file.downloadable && file.downloadable !== '0')) {
                    html += `<div class="d-flex align-items-center justify-content-between rounded-sm mt-15 p-15 border-dashed-gray300">
                                <span class="font-weight-bold text-dark">${downloadTheFileLang}</span>
                                <a href="${courseUrl}/file/${file.id}/download" class="btn btn-primary btn-sm" target="_blank">${downloadLang}</a>
                            </div>`;
                }

                html += `</div>`;

                html += handlePersonalNoteHtml(file);

                learningPageContent.html(html);
            } else if ((file.downloadable && file.downloadable !== '0')) {
                if (file.is_video) {
                    let $html = `<div class="js-video-and-download d-flex flex-column p-10 h-100">`;

                    $html += '<div class="learning-content-video-player w-100"></div>';

                    if ((file.downloadable && file.downloadable !== '0')) {
                        $html += `<div class="d-flex align-items-center justify-content-between rounded-sm mt-15 p-15 border-dashed-gray300">
                                <span class="font-weight-bold text-dark">${downloadTheFileLang}</span>
                                <a href="${courseUrl}/file/${file.id}/download" class="btn btn-primary btn-sm" target="_blank">${downloadLang}</a>
                            </div>`;
                    }

                    $html += `</div>`;

                    $html += handlePersonalNoteHtml(file);

                    learningPageContent.html($html);

                    const $videoCard = $('.js-video-and-download .learning-content-video-player');

                    handleVideoByFileId(file.id, $videoCard, function () {

                    });
                } else {
                    const title = downloadTheFileLang;
                    const hint = file.title;
                    const img = 'download.svg';
                    const otherHtml = `<a href="${courseUrl}/file/${file.id}/download" class="btn btn-primary btn-sm mt-15" target="_blank">${downloadLang}</a>`;

                    let html = handleContentBoxHtml(title, hint, img, otherHtml);

                    html += handlePersonalNoteHtml(file);

                    learningPageContent.html(html);
                }
            }
        } else {
            switch (file.storage) {
                case 'upload':
                case 'youtube':
                case 'vimeo':
                case 'external_link':
                case 's3':

                    let $html = '<div class="js-other-content-video-player learning-content-video-player w-100"></div>';

                    $html += handlePersonalNoteHtml(file);

                    learningPageContent.html($html);

                    const $videoCard1 = $('.js-other-content-video-player');

                    handleVideoByFileId(file.id, $videoCard1, function () {

                    });

                    break;

                case 'secure_host':

                    let $secureHosthtml = '<div class="js-secure-host-video-player learning-content-video-player w-100"></div>';

                    $secureHosthtml += handlePersonalNoteHtml(file);

                    learningPageContent.html($secureHosthtml);

                    const $videoCard2 = $('.js-secure-host-video-player');

                    handleVideoByFileId(file.id, $videoCard2, function () {

                    });

                    break;

                case 'google_drive':
                case 'iframe':
                    handleFileIframe(file);
                    break;
                case 'upload_archive':
                    const title = showHtmlFileLang;
                    const hint = file.title;
                    const img = 'download.svg';
                    const otherHtml = `<a href="${courseUrl}/file/${file.id}/showHtml" target="_blank" class="btn btn-primary btn-sm mt-15">${showLang}</a>`;

                    let html = handleContentBoxHtml(title, hint, img, otherHtml);

                    html += handlePersonalNoteHtml(file);

                    learningPageContent.html(html);
                    break;
            }

        }

        feather.replace();
    }

    function handleTextLessonHtml(textLesson) {
        let html = `<div class="text-lesson-content p-15 p-lg-30">
                    <h4 class="font-16 font-weight-bold text-dark">${textLesson.title}</h4>

                    ${
            (textLesson.image) ?
                `<div class="pb-5 mt-15 main-image rounded-lg w-100">
                                <img src="${textLesson.image}" class="bg-gray200" alt="${textLesson.title}"/>
                            </div>`
                : ''
        }

                    ${textLesson.content}
                </div>`;

        if (textLesson.attachments && Object.keys(textLesson.attachments).length) {
            html += `<div class="shadow-sm rounded-lg bg-white px-15 px-md-25 py-20 mt-20">
                    <h3 class=" font-16 font-weight-bold text-dark-blue">${attachmentsLang}</h3>

                    <div class="row mt-10">
                    `;

            Object.keys(textLesson.attachments).forEach(key => {
                const attachment = textLesson.attachments[key];

                html += `<div class="col-12 col-lg-3 mt-10 mt-lg-0">
                            <a href="${courseUrl}/file/${attachment.file.id}/download" class="d-flex align-items-center p-10 border border-gray200 rounded-sm">
                                <span class="chapter-icon bg-gray300 mr-10">
                                    <i data-feather="download-cloud" class="text-gray" width="16" height="16"></i>
                                </span>

                                <div class="">
                                    <span class="font-weight-500 font-14 text-dark-blue d-block">${attachment.file.title}</span>
                                    <span class="font-12 text-gray d-block">${attachment.file.file_type} | ${attachment.file.volume}</span>
                                </div>
                            </a>
                    </div>`;
            });

            html += `</div>
                </div>`;

        }

        html += handlePersonalNoteHtml(textLesson);

        learningPageContent.html(html);

        feather.replace();
    }

    function handleContentBoxHtml(title, hint, img, html = null, titleClassName = null) {
        return `<div class="d-flex align-items-center justify-content-center w-100 h-100">
                    <div class="learning-content-box d-flex align-items-center justify-content-center flex-column p-15 p-lg-30 rounded-lg">
                        <div class="learning-content-box-icon">
                            <img src="/assets/default/img/learning/${img}" alt="downloadable icon">
                        </div>

                        <h4 class="font-16 font-weight-bold text-dark ${titleClassName ?? ''}">${title}</h4>

                        <span class="font-14 font-weight-500 text-gray mt-5">${hint}</span>

                        ${html ?? ''}
                    </div>
                </div>`
            ;
    }

    function addContentLoading() {
        const html = `<div class="learning-content-loading d-flex align-items-center justify-content-center flex-column w-100 h-100">
            <img src="/assets/default/img/loading.gif" alt="">
            <p class="mt-10">${pleaseWaitForTheContentLang}</p>
        </div>`;

        learningPageContent.html(html);
    }

    function contentEmptyStateHtml() {
        const html = `<div class="learning-page-forum-empty d-flex align-items-center justify-content-center flex-column">
            <div class="learning-page-forum-empty-icon d-flex align-items-center justify-content-center">
                <img src="/assets/default/img/learning/content-empty.svg" class="img-fluid" alt="">
            </div>

            <div class="d-flex align-items-center flex-column mt-10 text-center">
                <h3 class="font-20 font-weight-bold text-dark-blue text-center">${learningPageEmptyContentTitleLang}</h3>
                <p class="font-14 font-weight-500 text-gray mt-5 text-center">${learningPageEmptyContentHintLang}</p>
            </div>
        </div>`;

        learningPageContent.html(html);
    }

    function handleFileIframe(file) {
        $.post('/course/getFilePath', {file_id: file.id}, function (result) {

            if (result && result.code === 200) {
                const {storage, path} = result;

                let $iframeHtml = `<div class="learning-content-iframe">
                            ${path}
                        </div>`;

                $iframeHtml += handlePersonalNoteHtml(file);

                learningPageContent.html($iframeHtml);

                feather.replace();
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
    }

    $('body').on('change', '.js-passed-lesson-toggle', function (e) {
        const $this = $(this);
        const course_id = $this.val();
        const item = $this.attr('data-item');
        const item_id = $this.attr('data-item-id');
        const status = this.checked;

        const data = {
            item: item,
            item_id: item_id,
            status: status
        };

        $.post('/course/' + course_id + '/learningStatus', data, function (result) {
            $.toast({
                heading: '',
                text: learningToggleLangSuccess,
                bgColor: '#43d477',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'success'
            });

            /*setTimeout(() => {
                window.location.reload();
            }, 500);*/
        }).fail(err => {
            $this.prop('checked', !status);

            $.toast({
                heading: '',
                text: learningToggleLangError,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });
        });
    });

    $('body').on('click', '.js-sequence-content-error-modal', function (e) {
        e.preventDefault();

        const passedError = $(this).attr('data-passed-error');
        const accessDaysError = $(this).attr('data-access-days-error');

        let html = '<ul class="list-group-custom">\n';
        if (passedError) {
            html += '<li class="font-14 mb-10">' + passedError + '</li>\n';
        }

        if (accessDaysError) {
            html += '<li class="font-14">' + accessDaysError + '</li>\n';
        }

        html += '</ul>';

        Swal.fire({
            icon: 'error',
            title: sequenceContentErrorModalTitle,
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
        });
    });

    $('body').on('click', '.js-save-history-message', function () {
        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $form.find('.invalid-feedback').text('');
        $form.find('.is-invalid').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + sendAssignmentSuccessLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else if (result && result.code === 401) {
                $.toast({
                    heading: result.errors.title,
                    text: result.errors.msg,
                    bgColor: '#f63c3c',
                    textColor: 'white',
                    hideAfter: 10000,
                    position: 'bottom-right',
                    icon: 'error'
                });

                $this.removeClass('loadingbar primary').prop('disabled', false);
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    })

    $('body').on('click', '.js-save-history-rate', function () {
        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $form.find('.invalid-feedback').text('');
        $form.find('.is-invalid').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + saveAssignmentRateSuccessLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else if (result && result.code === 401) {
                $.toast({
                    heading: result.errors.title,
                    text: result.errors.msg,
                    bgColor: '#f63c3c',
                    textColor: 'white',
                    hideAfter: 10000,
                    position: 'bottom-right',
                    icon: 'error'
                });

                $this.removeClass('loadingbar primary').prop('disabled', false);
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });

    function errorToast(heading, text) {
        $.toast({
            heading: heading,
            text: text,
            bgColor: '#f63c3c',
            textColor: 'white',
            hideAfter: 10000,
            position: 'bottom-right',
            icon: 'error'
        });
    }

    $('body').on('click', '.js-not-access-toast', function (e) {
        e.preventDefault();

        if (notAccessToastTitleLang && notAccessToastMsgLang) {
            errorToast(notAccessToastTitleLang, notAccessToastMsgLang);
        }
    });

    $('body').on('click', '.js-cant-start-quiz-toast', function (e) {
        e.preventDefault();

        if (cantStartQuizToastTitleLang && cantStartQuizToastMsgLang) {
            errorToast(cantStartQuizToastTitleLang, cantStartQuizToastMsgLang);
        }
    });

    $('body').on('click', '.js-save-personal-note', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('.js-personal-notes-form');
        const courseId = $form.attr('data-course-id');
        const itemId = $form.attr('data-item-id');
        const itemType = $form.attr('data-item-type');

        const data = {
            course_id: courseId,
            item_id: itemId,
            item_type: itemType,
            note: $form.find('textarea[name="notes"]').val(),
            attachment: $form.find('input[name="attach"]').val(),
        }

        $this.addClass('loadingbar primary').prop('disabled', true);


        $.post('/course/learning/personalNotes', data, function (result) {
            if (result && result.code === 200) {
                $.toast({
                    heading: personalNoteLang,
                    text: personalNoteStoredSuccessfullyLang,
                    bgColor: '#43d477',
                    textColor: 'white',
                    hideAfter: 10000,
                    position: 'bottom-right',
                    icon: 'success'
                });
            }

            $this.removeClass('loadingbar primary').prop('disabled', false);
        }).fail(function () {
            $.toast({
                heading: oopsLang,
                text: somethingWentWrongLang,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });

            $this.removeClass('loadingbar primary').prop('disabled', false);
        })

    });

    $('body').on('click', '.js-clear-personal-note', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('.js-personal-notes-form');

        $form.find('textarea[name="notes"]').val('')
        $form.find('input[name="attach"]').val('')
    });

})(jQuery);
