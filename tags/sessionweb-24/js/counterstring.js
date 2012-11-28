function counterstring(length) {
    var position_marker_character = "*";
    var output = "";
    if (length <= 0) {
        return "";
    }
    else if (length == 1) {
        return "1";
    }
    else if (length == 2) {
        return "*2";
    }
    else {
        output = "*3" + position_marker_character;
        var last_position_number = 3;
        while (output.length < length) {
            position_number = output.toString().length + last_position_number.toString().length + 1;
            if (position_number.toString().length > last_position_number.toString().length) {
                position_number += 1;
            }
            var token = position_number + position_marker_character;
            var remaining_length = length - output.toString().length;
            if (remaining_length < token.toString().length + output.toString().length) {
                token = token.substr(0, remaining_length);
            }
            output = output + token;

        }
        return output;
    }
}

function createSelection(start, end, field) {

    if ( field.createTextRange ) {

        /*
        IE calculates the end of selection range based
        from the starting point.
        Other browsers will calculate end of selection from
        the beginning of given text node.
        */

        var newend = end - start;
        var selRange = field.createTextRange();
        selRange.collapse(true);
        selRange.moveStart("character", start);
        selRange.moveEnd("character", newend);
        selRange.select();
    }

    /* For the other browsers */

    else if( field.setSelectionRange ){

        field.setSelectionRange(start, end);
    }

    field.focus();
}

