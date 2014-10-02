function flexi_colorGridRows() {
    $("tr").each(function () {
        var type = $(this).find('td[abbr="status"]').text()
        if (type == "Not Executed") {
            $(this).attr("class", $(this).attr("class") == "erow" ? "brown" : "brown");
        }
        else if (type == "In progress") {
            $(this).attr("class", $(this).attr("class") == "erow" ? "lightblue" : "lightblue");
        }
        else if (type == "Executed") {
            $(this).attr("class", $(this).attr("class") == "erow" ? "yellow" : "yellow");
        }
        else if (type == "Closed") {
            $(this).attr("class", $(this).attr("class") == "erow" ? "darkred" : "lightred");
        }
        else if (type == "Debriefed") {
            $(this).attr("class", $(this).attr("class") == "erow" ? "green" : "green");
        }
        else {
            $(this).attr("class", $(this).attr("class") == "erow" ? "white" : "white");
        }
    });
}
;

//TODO: CHange to delete through API!!!!
function deleteSession() {
    //$(".counterstring").colorbox({iframe:true, width:"80%", height:"80%"});

    var id = $('.trSelected td:nth-child(1) div').text();
    var title = $('.trSelected td:nth-child(3) div').text();
    if (id != "") {
        if (confirm('Are you sure you want to delete this session: (' + title + ") ?")) {
            $.ajax({
                type: "GET",
                data: {
                    sessionid: id
                },
                url: 'api/session/delete/index.php',
                complete: function (data) {
                    if (data.status == '200') {
                        jQuery("#flexgrid1").flexReload();
                        $("#msgdiv").fadeIn("slow");
                        $("#msgdiv").text("Session deleted");
                        $('#msgdiv').fadeOut(3000, function () {
                        });
                    }
                    else if (data.status == '401') {
                        $("#msgdiv").fadeIn("slow");
                        $("#msgdiv").text("You are not allowed to delete this session");
                        $('#msgdiv').fadeOut(3000, function () {
                        });
                    } else {
                        $("#msgdiv").fadeIn("slow");
                        $("#msgdiv").text("On error occured, please check log files");
                        $('#msgdiv').fadeOut(3000, function () {
                        });
                    }
                }
            });
        }
    }
    else {
        displaySelectSessionMsg();
    }
}

function debirefSession() {
    var id = $('.trSelected td:nth-child(1) div').text();
    var status = $('.trSelected td:nth-child(2) div').text();
    var command = "view";

    if (status == "Executed") {
        if (id != "")
            window.open('view.php?sessionid=' + id + '&debrief=yes', '_blank');
    }
    else {
        if (status == "Closed" || status == "Debriefed") {
            var msg = "Session is already debriefed."
        }
        else {
            var msg = "Session is not executed and can therefore not be debriefed.";
        }
        $("#msgdiv").fadeIn("slow");
        $("#msgdiv").text(msg);
        $('#msgdiv').fadeOut(3000, function () {
            // Animation complete.
        });
    }
}

function editSession() {
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('edit.php?sessionid=' + id, '_self', false);
    else {
        displaySelectSessionMsg();
    }
}

function viewSession() {
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('view.php?sessionid=' + id + '', '_self', false);
    else {
        displaySelectSessionMsg();
    }
}

function viewSession_newtab() {
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('view.php?sessionid=' + id + '', '_blank');
    else {
        displaySelectSessionMsg();
    }
}


function editSession_newtab() {
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('edit.php?sessionid=' + id, '_blank');
    else {
        displaySelectSessionMsg();
    }
}

function displaySelectSessionMsg() {
    $("#msgdiv").fadeIn("slow");
    $("#msgdiv").text("You need to select a session first.");
    $('#msgdiv').fadeOut(3000, function () {
        // Animation complete.
    });
}

function copySession() {
    var id = $('.trSelected td:nth-child(1) div').text();

    $("#dialog").dialog({
        autoOpen: false,
        modal: true,
        width: 500,
        height: 500,
        title: "Copy session",
        open: function (ev, ui) {
            $('#dialogurl').attr('src', 'api/session/copy/?sessionid=' + id);
            $('#dialog').css('overflow', 'hidden');
        },
        close: function (event, ui) {
            jQuery("#flexgrid1").flexReload();
        }
    });

    if (id != "") {
        $('#dialog').dialog('open');
    }
    else {
        displaySelectSessionMsg();
    }
}


function reassignSession() {
    var id = $('.trSelected td:nth-child(1) div').text();
    var status = $('.trSelected td:nth-child(2) div').text();
    if (status == "Not Executed" || status == "In progress") {

        $("#dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 500,
            title: "Reassign session",
            open: function (ev, ui) {
                $('#dialogurl').attr('src', 'api/session/reassign/?sessionid=' + id);
                $('#dialog').css('overflow', 'hidden');
            },
            close: function (event, ui) {
                jQuery("#flexgrid1").flexReload();
            }
        });

        if (id != "") {
            $('#dialog').dialog('open');
        }
        else {
            displaySelectSessionMsg();
        }
    }
    else {
        $("#msgdiv").fadeIn("slow");
        $("#msgdiv").text("You can only reassign a session that is Not Executed or In progress.");
        $('#msgdiv').fadeOut(3000, function () {
            // Animation complete.
        });
    }

}

function shareSession() {
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "") {
        $('.share').colorbox({
            href: 'api/session/share/?sessionid=' + id,
            open: true,
            iframe: false,
            width: 500,
            height: 500
        });
    }
    else {
        displaySelectSessionMsg();
    }
}


function filterSession() {
    if ($("#filterbox").is(':hidden'))
        $("#filterbox").fadeIn("slow");
    else
        $("#filterbox").fadeOut("slow");
}

function reloadSession() {
    $('#flexgrid1').flexOptions({newp: 1}).flexReload();
}

function searchSession() {
    if ($("#searchbox").is(':hidden'))
        $("#searchbox").fadeIn("slow");
    else
        $("#searchbox").fadeOut("slow");
}


$(function () {
    $("#flexgrid1").flexigrid({
        url: 'api/list/',
        dataType: 'json',
        colModel: [
            {display: 'ID', name: 'id', width: 40, sortable: true, align: 'left'},
//            {display:'Notes', name:'notes', width:30, sortable:true, align:'left'},
            {display: 'Status', name: 'status', width: 65, sortable: true, align: 'left'},
            {display: 'Title', name: 'title', width: 400, sortable: false, align: 'left'},
            {display: 'User', name: 'user', width: 100, sortable: false, align: 'left'},
            {display: 'Sprint', name: 'sprint', width: 100, sortable: false, align: 'left'},
            {display: 'Team', name: 'team', width: 100, sortable: false, align: 'left'},
            {display: 'Area', name: 'area', width: 200, sortable: false, align: 'left', hide: false},
//            {display:'Environment', name:'env', width:100, sortable:false, align:'left', hide:false},
            {display: 'Updated', name: 'updated', width: 105, sortable: true, align: 'left'},
            {display: 'Executed', name: 'executed', width: 105, sortable: true, align: 'left', hide: true}
        ],
        buttons: [
            {name: 'View', bclass: 'view', onpress: viewSession},
            {name: 'View in new tab', bclass: 'view', onpress: viewSession_newtab},
            {name: 'Edit', bclass: 'edit', onpress: editSession},
            {name: 'Edit in new tab', bclass: 'edit', onpress: editSession_newtab},
            {name: 'Delete', bclass: 'delete', onpress: deleteSession},
            {name: 'Copy', bclass: 'copy', onpress: copySession},
//            {name: 'Share', bclass: 'share', onpress: shareSession},
            {name: 'Debrief', bclass: 'debrief', onpress: debirefSession},
            {name: 'Reassign', bclass: 'reassign', onpress: reassignSession},
            {name: 'Filter', bclass: 'filter', onpress: filterSession},
            {name: 'Search', bclass: 'search', onpress: searchSession},
            {name: 'Reload', bclass: 'reload', onpress: reloadSession}

        ],
        sortname: "updated",
        sortorder: "desc",
        usepager: true,
        title: "Sessions",
        useRp: false,
        rp: 30,
        showTableToggleBtn: false,
        resizable: false,
        width: 1115,
        height: 740,
        onSubmit: addFormData,
        onSuccess: flexi_colorGridRows, //change row colours here
        singleSelect: true
    });
});

//This function adds paramaters to the post of flexigrid. You can add a verification as well by return to false if you don't want flexigrid to submit
function addFormData() {
    //passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, you have to specify the input elements that the data will come from
    var dt = $('#sform').serializeArray();
    $("#flexgrid1").flexOptions({params: dt});
    return true;
}


function stopRKey(evt) {
    var evt = (evt) ? evt : ((event) ? event : null);
    var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && (node.type == "text")) {
        return false;
    }
}


function quickView(sessionid) {
    $.getJSON('api/session/get?sessionid=' + sessionid, function (data) {

        var html = "<H1>Quick view of session</H1>";

        html = html + "<b>" + data['title'] + "</b>";
        html = html + "<H2>Charter</H2>" + data['charter'];
        html = html + "<H2>Notes</H2>" + data['notes'];
        $('.qview').colorbox({
            html: html,
            open: true,
            width: "80%",
            height: "80%"
        });
    });

}
document.onkeypress = stopRKey;


$(document).ready(function () {

    $("#searchbox").hide();
    $("#filterbox").hide();

    $('#helpsearch').click(function () {
        $('#helpsearch').colorbox({
            href: 'api/help/search',
            open: true,
            iframe: false,
            width: 500,
            height: 500

        });
    });


    $("select").change(function () {
        $('#flexgrid1').flexOptions({newp: 1}).flexReload();
        setPermSearchUrl();
        return false;
    });

    function setPermSearchUrl() {
        var tester = $('#select_tester').val();
        var sprint = $('#select_sprint').val();
        var team = $('#select_team').val();
        var area = $('#select_area').val();
        var status = $('#select_status_type').val();

        var searchstring = $('#searchstring').val();
        var url = 'list.php?tester=' + tester + '&sprint=' + sprint + '&team=' + team + '&area=' + area + '&status=' + status + '&searchstring=' + searchstring;
        $('#urldiv').html('<a href="' + url + '">Perm link to filter/search</a>');
    }

    $('#searchSessions').click(function () {
        $('#flexgrid1').flexOptions({newp: 1}).flexReload();
        setPermSearchUrl();
    });



    $('#searchSessionsRef').click(function () {
        $('#flexgrid1').flexOptions({newp: 1}).flexReload();
        setPermSearchUrl();
    });

    $("#searchstring").change(function () {
        $("#select_status_type").val(0);
        $("#select_status_type").attr('disabled', '');
    });

    $('#clearSearchSessions').click(function () {
        $('#searchstring').val('');
        $("#select_status_type").removeAttr('disabled');
        $('#flexgrid1').flexOptions({newp: 1}).flexReload();
    });

    $('#clearSearchSessionsRef').click(function () {
        $('#searchstringref').val('');
        $("#select_status_type").removeAttr('disabled');
        $('#flexgrid1').flexOptions({newp: 1}).flexReload();
    });

    $(function () {
        /****************************************************************
         * Double click for product details
         ****************************************************************/
        $('#flexgrid1').dblclick(function (e) {
            var Browser = {
                Version: function () {
                    var version = 999; // we assume a sane browser
                    if (navigator.appVersion.indexOf("MSIE") != -1)
                    // bah, IE again, lets downgrade version number
                        version = parseFloat(navigator.appVersion.split("MSIE")[1]);
                    return version;
                }
            }
            var browser_version = Browser.Version();
            if (Browser.Version() > 8) {


                target = $(e.target);
                while (target.get(0).tagName != "TR") {
                    target = target.parent();
                }
                var tmp = target.get(0);
                var status = target.get(0).childNodes[1].textContent;
                var id = target.get(0).firstChild.textContent;
                var id_new = $('.trSelected td:nth-child(1) div').text();
                var command = "view"

                if (status == "Executed") {
                    command = "debrief";
                    var url = "view.php?sessionid=" + id + "&debrief=yes";

                }
                else if (status == "Closed") {
                    command = "view";
                    var url = "view.php?sessionid=" + id;

                }
                else if (status == "Debriefed") {
                    command = "view";
                    var url = "session.php?sessionid=" + id + "&command=" + command;

                }
                else {
                    var url = "edit.php?sessionid=" + id;

                }


                window.open(url, '_blank');
            }
            else {
                alert("IE 6+7+8 does not support doubleclick, please use navigation buttons above or upgrade to IE9, Firefox or Chrome.");
            }
        });
    });


});
