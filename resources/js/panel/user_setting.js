(function ($) {
    "use strict";

    $('body').on('click', '#saveData', function (e) {
        e.preventDefault();

        $('#userSettingForm input[name="next_step"]').val(0);
        $(this).addClass('loadingbar primary').prop('disabled', true);

        $('#userSettingForm').trigger('submit');
    });

    $('body').on('click', '#getNextStep', function (e) {
        e.preventDefault();

        $('#userSettingForm input[name="next_step"]').val(1);
        $(this).addClass('loadingbar primary').prop('disabled', true);

        $('#userSettingForm').trigger('submit');
    });

    $('body').on('click', '#userAddEducations', function (e) {
        e.preventDefault();

        var html = '<div id="newEducationSwlModal">';
        html += $('#newEducationModal').html();
        html += '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',

        });
    });

    $('body').on('click', '#userAddExperiences', function (e) {
        e.preventDefault();

        var html = '<div id="newExperienceSwlModal">';
        html += $('#newExperienceModal').html();
        html += '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',

        });
    })

    $('body').on('click', '.close-swl', function (e) {
        e.preventDefault();
        Swal.close();
    });

    $('body').on('click', '#newEducationSwlModal #saveEducation', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);
        var $input = $('#newEducationSwlModal #new_education_val');

        submitMetas($this, $input, 'education')
    });

    $('body').on('click', '#newExperienceSwlModal #saveExperience', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);
        var $input = $('#newExperienceSwlModal #new_experience_val');

        submitMetas($this, $input, 'experience')
    });

    $('body').on('click', '#editEducationSwlModal #editEducation', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);
        var $input = $('#editEducationSwlModal #new_education_val');
        var user_id = $(this).attr('data-user-id');
        var education_id = $(this).attr('data-education-id');
        var val = $input.val();

        if (val !== '' && val !== null) {
            var data = {
                user_id: user_id,
                value: val,
                name: 'education',
            };

            $.post('/panel/setting/metas/' + education_id + '/update', data, function (result) {
                if (result && result.code == 200) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                        showConfirmButton: false,
                        width: '25rem',
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else if (result.code == 403) {
                    Swal.fire({
                        icon: 'error',
                        html: '<h3 class="font-20 text-center text-dark-blue py-25">' + notAccessToLang + '</h3>',
                        showConfirmButton: false,
                        width: '25rem',
                    });

                    $this.removeClass('loadingbar primary').prop('disabled', false);
                }
            }).fail(err => {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveErrorLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                $this.removeClass('loadingbar primary').prop('disabled', false);
            });
        }
    });

    $('body').on('click', '#editExperienceSwlModal #editExperience', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);
        var $input = $('#editExperienceSwlModal #new_experience_val');
        var user_id = $(this).attr('data-user-id');
        var experience_id = $(this).attr('data-experience-id');
        var val = $input.val();

        if (val !== '' && val !== null) {
            var data = {
                user_id: user_id,
                value: val,
                name: 'experience',
            };

            $.post('/panel/setting/metas/' + experience_id + '/update', data, function (result) {
                if (result && result.code == 200) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                        showConfirmButton: false,
                        width: '25rem',
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else if (result.code == 403) {
                    Swal.fire({
                        icon: 'error',
                        html: '<h3 class="font-20 text-center text-dark-blue py-25">' + notAccessToLang + '</h3>',
                        showConfirmButton: false,
                        width: '25rem',
                    });

                    $this.removeClass('loadingbar primary').prop('disabled', false);
                }
            }).fail(err => {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveErrorLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                $this.removeClass('loadingbar primary').prop('disabled', false);
            });
        }
    });

    $('body').on('click', '.edit-education', function (e) {
        e.preventDefault();
        var user_id = $(this).attr('data-user-id');
        var education_id = $(this).attr('data-education-id');
        var education_value = $(this).closest('.education-card').find('.education-value').text();

        var html = '<div id="editEducationSwlModal">';
        html += $('#newEducationModal').html();
        html += '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',
            onOpen: () => {
                var editEducationSwlModal = $('#editEducationSwlModal');
                editEducationSwlModal.find('#new_education_val').val(education_value);
                editEducationSwlModal.find('.section-title').text(editEducationLang);
                var saveBtn = editEducationSwlModal.find('#saveEducation');
                saveBtn.attr('data-user-id', user_id);
                saveBtn.attr('data-education-id', education_id);
                saveBtn.attr('id', 'editEducation');
            }
        });
    });

    $('body').on('click', '.edit-experience', function (e) {
        e.preventDefault();
        var user_id = $(this).attr('data-user-id');
        var experience_id = $(this).attr('data-experience-id');
        var experience_value = $(this).closest('.experience-card').find('.experience-value').text();

        var html = '<div id="editExperienceSwlModal">';
        html += $('#newExperienceModal').html();
        html += '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',
            onOpen: () => {
                var editExperienceSwlModal = $('#editExperienceSwlModal');
                editExperienceSwlModal.find('#new_experience_val').val(experience_value);
                editExperienceSwlModal.find('.section-title').text(editExperienceLang);
                var saveBtn = editExperienceSwlModal.find('#saveExperience');
                saveBtn.attr('data-user-id', user_id);
                saveBtn.attr('data-experience-id', experience_id);
                saveBtn.attr('id', 'editExperience');
            }
        });
    });

    function submitMetas($this, $input, name) {
        var val = $input.val();
        $input.removeClass('is-invalid');
        var user_id = null;
        if ($('input#userId').length) {
            user_id = $('input#userId').val();
        }

        if (val !== '' && val !== null) {
            var data = {
                name: name,
                value: val,
                user_id: user_id
            };

            $.post('/panel/setting/metas', data, function (result) {
                if (result && result.code == 200) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                        showConfirmButton: false,
                        width: '25rem',
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 500)
                }
            }).fail(err => {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveErrorLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                $this.removeClass('loadingbar primary').prop('disabled', false);
            });
        } else {
            $input.addClass('is-invalid');
            $this.removeClass('loadingbar primary').prop('disabled', false);
        }
    }

    $('body').on('change', '.js-user-bank-input', function (e) {
        e.preventDefault();

        const $optionSelected = $(this).find("option:selected");
        const specifications = $optionSelected.attr('data-specifications')

        const $card = $('.js-bank-specifications-card');
        let html = '';

        if (specifications) {
            Object.entries(JSON.parse(specifications)).forEach(([index, item], key) => {

                html += '<div class="form-group">\n' +
                    '         <label class="font-weight-500 text-dark-blue">' + item + '</label>\n' +
                    '         <input type="text" name="bank_specifications['+ index +']" value="" class="form-control"/>\n' +
                    ' </div>'
            })
        }

        $card.html(html);
    });

})(jQuery)
