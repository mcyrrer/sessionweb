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

function deleteSession() {
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "") {
        $.fn.colorbox({
            href:'api/session/delete/?sessionid=' + id,
            open:true,
            iframe:true,
            width:500,
            height:500,
            onClosed:function () {
                jQuery("#flexgrid1").flexReload();
            }
        });
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
            window.open('session.php?sessionid=' + id + '&command=debrief', '_blank');
    }
    else {
        $("#msgdiv").fadeIn("slow");
        $("#msgdiv").text("Session is not executed and can therefore not be debriefed.");
        $('#msgdiv').fadeOut(3000, function () {
            // Animation complete.
        });
    }
}

function editSession() {
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('session.php?sessionid=' + id + '&command=edit', '_self', false);
    else {
        displaySelectSessionMsg();
    }
}

function viewSession() {
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('session.php?sessionid=' + id + '&command=view', '_self', false);
    else {
        displaySelectSessionMsg();
    }
}

function viewSession_newtab() {
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('session.php?sessionid=' + id + '&command=view', '_blank');
    else {
        displaySelectSessionMsg();
    }
}



function editSession_newtab() {
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "")
        window.open('session.php?sessionid=' + id + '&command=edit', '_blank');
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
    if (id != "") {
        $.fn.colorbox({
            href:'api/session/copy/?sessionid=' + id,
            open:true,
            iframe:true,
            width:500,
            height:500,
            onClosed:function () {
                jQuery("#flexgrid1").flexReload();
            }
        });
    }
    else {
        displaySelectSessionMsg();
    }
}


function reassignSession() {
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "") {
        $.fn.colorbox({
            href:'api/session/reassign/?sessionid=' + id,
            open:true,
            iframe:true,
            width:500,
            height:500,
            onClosed:function () {
                jQuery("#flexgrid1").flexReload();
            }
        });
    }
    else {
        displaySelectSessionMsg();
    }
}

function shareSession() {
    var id = $('.trSelected td:nth-child(1) div').text();
    if (id != "") {
        $.fn.colorbox({
            href:'api/session/share/?sessionid=' + id,
            open:true,
            iframe:true,
            width:500,
            height:500
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

function searchSession() {
    if ($("#searchbox").is(':hidden'))
        $("#searchbox").fadeIn("slow");
    else
        $("#searchbox").fadeOut("slow");
}


$(function () {
    $("#flexgrid1").flexigrid({
        url:'api/list/',
        dataType:'json',
        colModel:[
            {display:'ID', name:'id', width:25, sortable:true, align:'left'},
//            {display:'Notes', name:'notes', width:30, sortable:true, align:'left'},
            {display:'Status', name:'status', width:65, sortable:true, align:'left'},
            {display:'Title', name:'title', width:300, sortable:false, align:'left'},
            {display:'User', name:'user', width:100, sortable:false, align:'left'},
            {display:'Sprint', name:'sprint', width:100, sortable:false, align:'left'},
            {display:'Team', name:'team', width:100, sortable:false, align:'left'},
            {display:'Area', name:'area', width:100, sortable:false, align:'left', hide:false},
//            {display:'Environment', name:'env', width:100, sortable:false, align:'left', hide:false},
            {display:'Updated', name:'updated', width:105, sortable:true, align:'left'},
            {display:'Executed', name:'executed', width:105, sortable:true, align:'left', hide:true}
        ],
        buttons:[
            {name:'View', bclass:'view', onpress:viewSession},
            {name:'View in new tab', bclass:'view', onpress:viewSession_newtab},
            {name:'Edit', bclass:'edit', onpress:editSession},
            {name:'Edit in new tab', bclass:'edit', onpress:editSession_newtab},
            {name:'Delete', bclass:'delete', onpress:deleteSession},
            {name:'Copy', bclass:'copy', onpress:copySession},
            {name:'Share', bclass:'share', onpress:shareSession},
            {name:'Debrief', bclass:'debrief', onpress:debirefSession},
            {name:'Reasign', bclass:'reasign', onpress:reassignSession},
            {name:'Filter', bclass:'filter', onpress:filterSession},
            {name:'Search', bclass:'search', onpress:searchSession}

        ],
        sortname:"id",
        sortorder:"desc",
        usepager:true,
        title:"Sessions",
        useRp:false,
        rp:50,
        showTableToggleBtn:false,
        resizable:false,
        width:1115,
        height:1140,
        onSubmit:addFormData,
        onSuccess:flexi_colorGridRows, //change row colours here
        singleSelect:true
    });
});

//This function adds paramaters to the post of flexigrid. You can add a verification as well by return to false if you don't want flexigrid to submit
function addFormData() {
    //passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, you have to specify the input elements that the data will come from
    var dt = $('#sform').serializeArray();
    $("#flexgrid1").flexOptions({params:dt});
    return true;
}


function stopRKey(evt) {
    var evt = (evt) ? evt : ((event) ? event : null);
    var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

document.onkeypress = stopRKey;


$(document).ready(function () {



    $("#searchbox").hide();
    $("#filterbox").hide();

    $('#helpsearch').click(function () {
        $.fn.colorbox({
            href:'api/help/search',
            open:true,
            iframe:true,
            width:500,
            height:500
        });
    });

    $("select").change(function () {
        $('#flexgrid1').flexOptions({newp:1}).flexReload();
        setPermSearchUrl();
        return false;
    });

    function setPermSearchUrl()
    {
        var tester = $('#select_tester').val();
        var sprint = $('#select_sprint').val();
        var team = $('#select_team').val();
        var area = $('#select_area').val();
        var status = $('#select_status_type').val();

        var searchstring = $('#searchstring').val();
        var url = 'list2.php?tester='+tester+'&sprint='+sprint+'&team='+team+'&area='+area+'&status='+status+'&searchstring='+searchstring;
        $('#urldiv').html('<a href="'+url+'">Perm link to filter/search</a>');
    }

    $('#searchSessions').click(function () {
        $('#flexgrid1').flexOptions({newp:1}).flexReload();
        setPermSearchUrl();
    });

    $('#clearSearchSessions').click(function () {
        $('#searchstring').val('');
        $('#flexgrid1').flexOptions({newp:1}).flexReload();
    });

    $(function () {
        /****************************************************************
         * Double click for product details
         ****************************************************************/
        $('#flexgrid1').dblclick(function (e) {
            target = $(e.target);
            while (target.get(0).tagName != "TR") {
                target = target.parent();
            }
            var tmp = target.get(0);
            var status = target.get(0).childNodes[1].textContent;
            var id = target.get(0).firstChild.textContent;
            var command = "view"
            if (status == "Not Executed") {
                command = "edit";
            }
            if (status == "Executed") {
                command = "debrief";
            }
            var url = "session.php?sessionid=" + id + "&command=" + command;
            window.open(url, '_blank');
        });
    });

});
