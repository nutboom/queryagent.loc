$(document).ready(function(){
    $(document).on('click', '.tarif-block', function() {
        $('.tarif-block').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true);
    });
    $('.register-form .reenter_password,.register-form .password').on('blur', function() {
        var pass = $('.register-form .password'),
            repass = $('.register-form .reenter_password');
        if (pass.val() != repass.val()) {
            pass[0].setCustomValidity('Пароли должны совпадать.');
        } else {
            pass[0].setCustomValidity('');
        }
    });

    $(".checkbox-layer")
        .each(function() {
            var isChecked = $(this).prev().prop('checked');
            if (isChecked) {
                $(this).addClass('checkbox-layer__active');
            } else {
                $(this).removeClass('checkbox-layer__active');
            }
        })
        .on('click', function() {
            var checkbox = $(this).prev(),
                isChecked = checkbox.prop('checked');
            if (!isChecked) {
                $(this).addClass('checkbox-layer__active');
                checkbox.prop('checked', true);
            } else {
                $(this).removeClass('checkbox-layer__active');
                checkbox.prop('checked', false);
            }
        });
});