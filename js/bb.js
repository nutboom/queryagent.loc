function content_insert(objectid, start, end, extra, tag) {
    var replace = '';
    element = document.getElementById(objectid);

    if (document.selection) {
        element.focus();
        selText = document.selection.createRange();

        if (start == end && start == "") {
            replace =   start + extra + end;
        }
        else {
            replace =   start + selText.text.replace( /^\s+/g, '') + end;
        }

        selText.text = replace;
    }
    else if (element.selectionStart || element.selectionStart == '0') {
        element.focus();
        var startPos = element.selectionStart;
        var endPos = element.selectionEnd;
        var selText = element.value.substring(startPos, endPos);

        if (start == end && start == "") {
            replace =   element.value.substring(0, startPos) + extra + element.value.substring(endPos, element.value.length);
        }
        else {
            replace =   element.value.substring(0, startPos) + start + selText.replace( /^\s+/g, '') + end + element.value.substring(endPos, element.value.length);
        }

        element.value = replace;
    }
    else {
        element.value += start + end;
    }
}

$(document).ready(function() {
    $('.content-buttons a').click(function() {
        var tag = $(this).data('tag');
        var textarea = $(this).parent().data('textarea');
        console.log($(this).parent().data('textarea'));

        if (tag == "link") {
            if ((url = prompt('Введите URL:', 'http://')) != null) {
                var start   = '[link=' + url + ']';
                var end     = '[/link]';

                content_insert(textarea, start, end, "", "");
            }
        }
        else {
            var start   = '[' + tag + ']';
            var end     = '[/' + tag + ']';

            content_insert(textarea, start, end, "", "");
        }

        return false;
    })
});