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