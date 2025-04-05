(function ($) {
    "use strict";

    var $imageCropperContainer = $('#imageCropperContainer');
    var $avatarCropModalContainer = $('#avatarCropModalContainer');
    var $refImage = '';
    var $refInput = '';

    // initial avatar crop object
    $imageCropperContainer.cropit({
        width: 350,
        height: 350,
        'onFileChange': function () {
            $avatarCropModalContainer.modal('show');
        },
        //smallImage: 'stretch',
        //minZoom: 'fit',
        onImageError: function (err) {
            $.toast({
                heading: err.message,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 5000,
                position: 'bottom-right',
                icon: 'error'
            });
        }
    });

    $('body').on('click', '.select-image-cropit', function () {
        $refImage = $(this).attr('data-ref-image');
        $refInput = $(this).attr('data-ref-input');
        $imageCropperContainer.find('.cropit-image-input').trigger('click');
    });

    // rotation buttons click handler
    $avatarCropModalContainer.find('button.rotate-cw').on('click', function () {
        $imageCropperContainer.cropit('rotateCW');
    });
    $avatarCropModalContainer.find('button.rotate-ccw').on('click', function () {
        $imageCropperContainer.cropit('rotateCCW');
    });

    // Cancel Avatar Crop
    $('#cancelAvatarCrop').on('click', function (e) {
        e.preventDefault();

        $avatarCropModalContainer.modal('hide');
    });

    $('body').on('click', '#storeAvatar', function (e) {
        e.preventDefault();

        var avatar = $imageCropperContainer.cropit('export');

        $('#' + $refImage).attr('src', avatar);
        $('#' + $refInput).val(avatar);
        $avatarCropModalContainer.modal('hide');
    })
})(jQuery);
