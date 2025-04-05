(function ($) {
    "use strict";

    $('body').on('click', '.contact-info', function (e) {
        e.preventDefault();
        const $this = $(this);
        const user_id = $this.attr('data-user-id');
        const user_type = $this.attr('data-user-type');
        const itemId = $this.attr('data-item-id');

        loadingSwl();

        const data = {
            user_id: user_id,
            user_type: user_type,
            item_id: itemId
        };
        $.post('/panel/users/contact-info', data, function (result) {
            if (result && result.code === 200) {
                const modal_title = (user_type === 'instructor') ? instructor_contact_information_lang : student_contact_information_lang;
                const html = `
                    <div class="contact-info-modal">
                        <h2 class="section-title after-line">${modal_title}</h2>
                        <div class="mt-25 d-flex flex-column align-items-center justify-content-center">
                            <div class="contact-avatar">
                                <img src="${result.avatar}" class="img-cover" alt="">
                            </div>
                            <div class="mt-15 w-75 text-center">
                                <h3 class="font-16 font-weight-bold text-dark-blue">${result.name}</h3>
                                <div class="d-flex align-items-center justify-content-between mt-15">
                                    <span class="text-left mr-15 flex-grow-1">${email_lang} :</span>
                                    <span class="text-right">${result.email}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-15">
                                    <span class="text-left mr-15 flex-grow-1">${phone_lang} :</span>
                                    <span class="text-right">${result.phone}</span>
                                </div>

                                ${
                                    (result.location && result.location !== 'null') ?
                                        `<div class="d-flex align-items-center justify-content-between mt-15">
                                            <span class="text-left mr-15 flex-grow-1">${location_lang} :</span>
                                            <span class="text-right">${result.location}</span>
                                        </div>`
                                        : ''
                                 }

                            </div>
                        </div>

                        ${
                            (result.description && result.description !== 'null') ?
                                `
                                    <div class="mt-25 rounded-sm border p-10 text-gray font-16">${result.description}</div>
                                `
                                : ''
                        }

                       <div class="mt-30 d-flex align-items-center justify-content-end">
                            <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">${close_lang}</button>
                        </div>
                    </div>
                `;

                Swal.fire({
                    html: html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '40rem',
                });
            }
        })
    });
})(jQuery);
