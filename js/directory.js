$(document).ready(function() {
    $('#answers_on_question').jqDynaForm();

    $('.delete').live('click',function(){
        var item = $(this).parents().filter(':first');
        var imgID = item.find('input[type=hidden]').val();
        if(imgID){
            var arrPath = window.location.pathname.split('/');
            var url = "http://" + window.location.hostname + "/" + arrPath[1] + "/" + arrPath[2] + "/deleteAnswer/";
            $.ajax({
                type: 'POST',
                url: url,
                data: "id="+imgID,
            });
        }
        item.slideUp(function(){
            item.remove();
        });
    });
});