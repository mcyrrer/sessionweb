$(document).ready(function () {
    $('#cntJqueryUiPopup').dialog({
        autoOpen: false,
        width: 600,
        height: 450,
        modal: true,
        resizable: true,
        draggable: true,
        title: 'Test helper'
    });

    $('.counterstring').click(function () {
        $('#cntJqueryUiPopup').load('testhelper.php').dialog('open');
    });



});

/**
 * Escape an id and adds a # in the beginning.
 * @param myid id to escape without #
 * @returns {string} jQuery Id with a # in the beginning.
 */
function escapeJqueryIdName( myid ) {

    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );

}