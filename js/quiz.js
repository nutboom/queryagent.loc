$(document).ready(function() {
    $('.Quiz_elements').live('change', function(){
        var modalContentEl = $("#show_client");
        modalContentEl.html('');
        var pEl = $('<p/>').appendTo(modalContentEl);
        pEl.html($(this).next().html());
    });

    $('#Quiz_isSendMessenge').parent().hide();
    $('#Quiz_state').live('change', function () {
        switch (/*this.selectedOptions.item().value*/$(this).val()) {
            case "work":
                $('#Quiz_isSendMessenge').parent().show();
                break;
            default:
                $('#Quiz_isSendMessenge').parent().hide();
                break;
        }

    });
    //$('#Quiz_state').change();
});