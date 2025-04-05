(function () {
    "use strict";


    if (jQuery().summernote) {
        makeSummernote($('#summernote'), 280)
    }

    $('body').on('click', '.panel-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'});
    });

    $('body').on('click', '.js-close-reply-post', function (e) {
        e.preventDefault();

        const $topicPostsReplyCard = $(this).closest('.topic-posts-reply-card');

        $topicPostsReplyCard.addClass('d-none');
        $topicPostsReplyCard.find('.js-reply-post-id').val('');
    });

    $('body').on('click', '.js-save-post', function (e) {
        e.preventDefault();
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
                    html: '<h3 class="font-20 text-center text-dark-blue">' + replyToTopicSuccessfullySubmittedLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }

            $this.removeClass('loadingbar primary').prop('disabled', false);
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

    $('body').on('click', '.js-reply-post-btn', function (e) {
        e.preventDefault();
        const $this = $(this);

        const postId = $this.attr('data-id');
        const $card = $this.closest('.topics-post-card');
        const userName = $card.find('.js-post-user-name').text();

        let description = '';
        $card.find('.topic-post-description').contents().filter(function () {
            description += this.innerText;
        });

        const shortText = jQuery.trim(description).substring(0, 125)
            .split(" ").slice(0, -1).join(" ") + "...";

        const $topicPostsReplyCard = $('.topic-posts-reply-card');
        $topicPostsReplyCard.removeClass('d-none');
        $topicPostsReplyCard.find('.js-reply-post-id').val(postId);
        $topicPostsReplyCard.find('.js-reply-post-title span').text(userName);
        $topicPostsReplyCard.find('.js-reply-post-description').text(shortText);


        $('html, body').animate({
            scrollTop: $topicPostsReplyCard.offset().top - 100
        }, 500);

    });


    /**
     * report modal
     * */
    $('body').on('click', '.js-topic-post-report', function (e) {
        e.preventDefault();
        const $this = $(this);
        const itemId = $this.attr('data-id');
        const type = $this.attr('data-type');

        const topicReportModal = $('#topicReportModal');
        topicReportModal.find('.js-item-id-input').val(itemId);
        topicReportModal.find('.js-item-type-input').val(type);

        let modal_html = topicReportModal.html();

        Swal.fire({
            html: modal_html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '.js-topic-report-submit', function (e) {
        e.preventDefault();
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
                    html: '<h3 class="font-20 text-center text-dark-blue">' + reportSuccessfullySubmittedLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }

            $this.removeClass('loadingbar primary').prop('disabled', false);
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

    $('body').on('click', '.js-topic-post-like', function (e) {
        e.preventDefault();

        const $this = $(this);
        const parent = $this.closest('.topic-post-like-btn');
        let likeCount = parent.find('.js-like-count').text();
        const action = $this.attr('data-action');

        const isLiked = $this.hasClass('liked');

        $this.toggleClass('liked');

        let newLikeCount = (isLiked) ? likeCount - 1 : Number(likeCount) + 1;

        parent.find('.js-like-count').text(newLikeCount);

        $.post(action, {}, function (result) {
            if (result && result.code === 200) {
                parent.find('.js-like-count').text(result.likes);

                if (result.status) {
                    $this.addClass('liked');
                } else {
                    $this.removeClass('liked');
                }
            }
        }).fail(err => {
            $this.toggleClass('liked');

            parent.find('.js-like-count').text(likeCount);
        });
    });

    $('body').on('click', '.js-btn-post-un-pin, .js-btn-post-pin', function (e) {
        e.preventDefault();
        const $this = $(this);

        const action = $this.attr('data-action');

        loadingSwl();

        $.post(action, {}, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + changesSavedSuccessfullyLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });

    $('body').on('click', '.js-topic-bookmark', function (e) {
        e.preventDefault();
        const $this = $(this);

        const action = $this.attr('data-action');

        $this.toggleClass('text-warning');

        const style = getComputedStyle(document.body);
        const primaryColor = style.getPropertyValue('--primary');
        const warningColor = style.getPropertyValue('--warning');

        $.post(action, {}, function (result) {
            if (result && result.code === 200) {

                if (topicBookmarkedSuccessfullyLang && topicUnBookmarkedSuccessfullyLang) {
                    $.toast({
                        text: result.add ? topicBookmarkedSuccessfullyLang : topicUnBookmarkedSuccessfullyLang,
                        bgColor: result.add ? primaryColor : warningColor,
                        textColor: 'white',
                        hideAfter: 10000,
                        position: 'bottom-right',
                        icon: 'success'
                    });
                }
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });

    function makeEditPostHtml(post, action, record) {

        return `<div>
        <h3 class="section-title after-line font-20 text-dark-blue">${editPostLang}</h3>

        <form action="${action}" method="post" class="mt-25">
            <div class="form-group text-left">
                <label class="input-label">${descriptionLang}</label>
                <textarea id="summernote_${record}" name="description" class="form-control">${post.description}</textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="row">
                <div class="col-12 col-md-7">
                    <div class="form-group text-left">
                        <label class="input-label">${editAttachmentLabelLang}</label>

                        <div class="d-flex align-items-center">
                            <div class="input-group mr-10">
                                <div class="input-group-prepend">
                                    <button type="button" class="input-group-text panel-file-manager" data-input="postAttachmentInput_${record}" data-preview="holder">
                                        ${(typeof feather !== "undefined") ? `<i data-feather="upload" width="18" height="18" class="text-white"></i>` : `<i class="fa fa-upload"></i>`}
                                    </button>
                                </div>
                                <input type="text" name="attach" id="postAttachmentInput_${record}" value="${post.attach ?? ''}" class="form-control"/>
                            </div>

                            <button type="button" class="js-save-post btn btn-primary btn-sm">${sendLang}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>`;
    }

    $('body').on('click', '.js-post-edit', function (e) {
        e.preventDefault();
        const $this = $(this);
        const action = $this.attr('data-action');

        loadingSwl();

        $.get(action, function (result) {
            if (result && result.code === 200) {
                const post = result.post;

                const random = randomString();
                const html = makeEditPostHtml(post, action, random);

                Swal.fire({
                    html: html,
                    showConfirmButton: false,
                    width: '60rem',
                    onOpen: function () {

                        if (jQuery().summernote) {
                            makeSummernote($('#summernote_' + random), 280)
                        }

                        if (typeof feather !== "undefined") {
                            feather.replace();
                        }
                    }
                });
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });

    $('body').on('click','.login-to-access',function (e) {
        e.preventDefault();

        if (notLoginToastTitleLang && notLoginToastMsgLang) {
            $.toast({
                heading: notLoginToastTitleLang,
                text: notLoginToastMsgLang,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });
        }
    });
})(jQuery);
