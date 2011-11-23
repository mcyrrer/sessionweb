$buglist = "";

$(document).ready(function() {

    $("#option_list").hide();

    //Import issue # from textarea to an js array
    var myRequirements = $('#requirementlist_hidden').text().split(',');
    var myBugs = $('#buglist_hidden').text().split(',');
    var mysessionlinks = $('#sessionlinklist_hidden').text().split(',');

    //Get the command GET parameter and name of page loaded
    var command = "";
    try {
        var command = $(document).getUrlParam("command");
    }
    catch(err) {

    }

    try {
        var sessionid = $(document).getUrlParam("sessionid");
    }
    catch(err) {
        var sessionid = "null";
    }

    var sPath = window.location.pathname;
    var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

    //Initiation of WYSIWUG editor
    if (sPage == "session.php" && command == "edit" || sPage == "session.php" && command == "new") {
        CKEDITOR.config.toolbar_Basic =
                [
                    ['Source','-','-','-','-','-','-'],
                    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','-', 'SpellChecker', 'Scayt'],
                    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
                    '/',
                    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
                    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
                    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    ['BidiLtr', 'BidiRtl' ],
                    ['Link','Unlink','Anchor'],
                    ['Image','-','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','-'],
                    '/',
                    ['Styles','Format','Font','FontSize'],
                    ['TextColor','BGColor'],
                    ['Maximize', 'ShowBlocks','-','About']
                ];
        CKEDITOR.config.toolbar = 'Basic';
        $('#textarea1').ckeditor({ toolbar : 'Basic' });//, { toolbar : 'ToolBar' };
        $('#textarea2').ckeditor({ toolbar : 'Basic' });//, { skin : 'office2003' };
    }

//************Colorbox ifram for counterstring page****
    $(".counterstring").colorbox({iframe:true, width:"80%", height:"80%"});


//**************FILE UPLOAD i session.php
$(".uploadajax").colorbox({iframe:true, width:"80%", height:"80%"});


//***************WordCloud add word to stoplist
$("[class=wordcloudword]").click(function() {
    var word = $(this).text();
    $.get("wordcloud.php", { word: $(this).text() },
            function(data) {
                $("#addedword").html(word + " added to black list");
//                    alert(": " + data);
            });
    $(this).html("");

});

//***************Autosave implementation start***************


if (sPage == "session.php" && command == "edit") {

    $.get('api/user/settings/autosave', function(data) {
        $('.result').html(data);


//        var res = confirm("Would you like to enable automatic save of you session? (it will save once a minute)");
        if (data == 1) {
            $("#autosaved").empty().append("Autosave enabled...");
            if ($(document).getUrlParam("command") == "edit") {

                $('#sessionform').autosave({
                    interval:     60000,//60000=every 1min
                    save:         function(e, o) {
                        var today = new Date();
                        var h = today.getHours();
                        var m = today.getMinutes();
                        var s = today.getSeconds();
                        m = checkTime(m);
                        s = checkTime(s);
                        $("#autosaved").empty().append(h + ":" + m + ":" + s);
                    }
                });
            }
        }
        else {
            $("#autosaved").empty().append("Autosave disabled by user, change user settings under settings to enable it.");
        }
    });
}


//***************Metric check at submit***************
$("#input_submit").click(function() {
    if ($("#executed").is(':checked')) {
        var setup = $("#setuppercent").val();
        var test = $("#testpercent").val();
        var bug = $("#bugpercent").val();
        var opp = $("#oppertunitypercent").val();

        var totalPercent = parseInt(setup) + parseInt(test) + parseInt(bug) + parseInt(opp);
        if (totalPercent == "NaN") {
            totalPercent = 0;
        }
        if (parseInt(totalPercent) != 100) {
            alert("Percentage for session is " + parseInt(totalPercent) + "%. It has to be 100%.");
            return false;
        } else {
            return true;
        }
    }
});


//***************Metrics calculation***************
$("[class=metricoption]").change(function() {
    var setup = $("#setuppercent").val();
    var test = $("#testpercent").val();
    var bug = $("#bugpercent").val();
    var opp = $("#oppertunitypercent").val();

    var totalPercentage = parseInt(setup) + parseInt(test) + parseInt(bug) + parseInt(opp);
    if (totalPercentage == NaN) {
        totalPercentage = 0;
    }

    if (totalPercentage != 100) {

        $("#metricscalculation").html("<div id=\"metricscalculation_red\">Total percentage = "
                + totalPercentage
                + "%. Please adjust it to 100%.</div>");
    }
    else {
        $("#metricscalculation").html("Percentage = " +
                totalPercentage +
                "%");
    }
});


//***************Show search option in list.php***************
$("[class=showoption]").click(function() {
    $("#option_list").fadeIn("slow");
    $("#showoptiontext").text("");
});

//***************Add sessionlink to session and manage if it is deleted***************
$("#add_sessionlink").click(function() {
    var sessionlinkValue = $("#sessionlink").val() + '';
    if (jQuery.inArray(sessionlinkValue, mysessionlinks) == -1 &&
            sessionlinkValue != "") {
        mysessionlinks.push(sessionlinkValue);
        $("#sessionlink").attr('value', '');
        var newsessionlinkHtml = "<div id=\"sessionlinkdiv_" + sessionlinkValue
                + "\">"
                + "<table width=\"*\" border=\"0\">"
                + "    <tr>"
                + "        <td><a href=\""
                + "session.php?sessionid="
                + sessionlinkValue
                + "&command=view"
                + "\" class=\"sessionlinkurl\" target=\”_blank\”>" + sessionlinkValue + "</a>"
                + "        </td>" + "        <td><div id=\"sessionlink_" + sessionlinkValue
                + "\"> <img src=\"pictures/removeicon.png\" alt=\"[remove]\" /></div>"
                + "        </td>"
                + "    </tr>"
                + "</table>"
                + "</div>";
        $(newsessionlinkHtml).appendTo('#sessionlinklist_visible');
        $('#sessionlinklist_hidden').text(mysessionlinks.toString());
        $("#sessionlink_" + sessionlinkValue + "").click(function() {
            var thisIe = this.id;
            var sessionlinkUrlId = "sessionlinkdiv_" + sessionlinkValue;
            if (this.id != sessionlinkUrlId) {
                var answer = confirm("Remove  sessionlink " +
                        sessionlinkValue +
                        "?")
                if (answer) {
                    $("#sessionlinkdiv_" + sessionlinkValue + "").remove();
                    sessionlinkPos = jQuery.inArray(sessionlinkValue, mysessionlinks);
                    if (sessionlinkPos != -1) {
                        var removedelements = mysessionlinks.splice(sessionlinkPos, 1);// remove();
                        $('#sessionlinklist_hidden').text(mysessionlinks.toString());
                    }
                }
            }
        });
    }
    else {
        if (sessionlinkValue == "") {
        }
        else {
            alert("sessionlink  with id " +
                    sessionlinkValue +
                    " is already connected to session.");
        }
    }
});

//***************Add bug to session and manage if it is deleted***************
$("#add_bug").click(function() {
    var bugValue = $("#bug").val() + '';
    if (jQuery.inArray(bugValue, myBugs) == -1 &&
            bugValue != "") {
        myBugs.push(bugValue);
        $("#bug").attr('value', '');
        var newBugHtml = "<div id=\"bugdiv_" + bugValue + "\">"
                + "<table width=\"*\" border=\"0\">"
                + "    <tr>"
                + "        <td><a href=\""
                + url_to_dms
                + ""
                + bugValue
                + "\" class=\"bugurl\" target=\”_blank\”>"
                + bugValue
                + "</a>"
                + "        </td>"
                + "        <td><div id=\"bug_"
                + bugValue
                + "\"> <img src=\"pictures/removeicon.png\" alt=\"[remove]\" /></div>"
                + "        </td>"
                + "    </tr>"
                + "</table>"
                + "</div>";
        $(newBugHtml).appendTo('#buglist_visible');
        $('#buglist_hidden').text(myBugs.toString());
        $("#bug_" + bugValue + "").click(function() {
            var thisIe = this.id;
            var bugUrlId = "bugdiv_" + bugValue;
            if (this.id != bugUrlId) {
                var answer = confirm("Remove  bug " +
                        bugValue +
                        "?")
                if (answer) {
                    $("#bugdiv_" +
                            bugValue +
                            "").remove();
                    bugPos = jQuery.inArray(bugValue, myBugs);
                    if (bugPos != -1) {
                        var removedelements = myBugs.splice(bugPos, 1);// remove();
                        $('#buglist_hidden').text(myBugs.toString());
                    }
                }
            }
        });
    }
    else {
        if (bugValue == "") {
        }
        else {
            alert("Bug with id " +
                    bugValue +
                    " is already connected to session.");
        }
    }
});

//***************Add requirement to session and manage if it is deleted***************
$("#add_requirement").click(function() {
    var requirementValue = $("#requirement").val() + '';
    if (jQuery.inArray(requirementValue, myRequirements) == -1 &&
            requirementValue != "") {
        myRequirements.push(requirementValue);
        $("#requirement").attr('value', '');
        var newrequirementHtml = "<div id=\"requirementdiv_" + requirementValue + "\">" + "<table width=\"*\" border=\"0\">" + "    <tr>" + "        <td><a href=\"" + url_to_rms + "" + requirementValue + "\" class=\"requirementurl\" target=\”_blank\”>" + requirementValue + "</a>" + "        </td>" + "        <td><div id=\"requirement_" + requirementValue + "\"> <img src=\"pictures/removeicon.png\" alt=\"[remove]\" /></div>" + "        </td>" + "    </tr>" + "</table>" + "</div>";
        $(newrequirementHtml).appendTo('#requirementlist_visible');
        $('#requirementlist_hidden').text(myRequirements.toString());
        $("#requirement_" + requirementValue + "").click(function() {
            var thisIe = this.id;
            var requirementUrlId = "requirementdiv_" + requirementValue;
            if (this.id != requirementUrlId) {
                var answer = confirm("Remove  requirement " +
                        requirementValue +
                        "?")
                if (answer) {
                    $("#requirementdiv_" + requirementValue + "").remove();
                    requirementPos = jQuery.inArray(requirementValue, myRequirements);
                    if (requirementPos != -1) {
                        var removedelements = myRequirements.splice(requirementPos, 1);// remove();
                        $('#requirementlist_hidden').text(myRequirements.toString());
                    }
                }
            }
        });
    }
    else {
        if (requirementValue == "") {
        }
        else {
            alert("requirement  with id " +
                    requirementValue +
                    " is already connected to session.");
        }
    }
});


//***************Statistic graph typ changed***************
$("#choosegraph").change(function() {
//        $("#graphframe").html($("#choosegraph").val());
//        $("#select_tester").attr('disabled', 'disabled');
//        $("#select_team").attr('disabled', 'disabled');
//        $("#select_sprint").attr('disabled', 'disabled');
});

//***************Statistic Change Iframe to choosen graph***************
$("#showgraph").click(function() {
    var type = $.URLEncode($("#choosegraph").val());
    var tester = $.URLEncode($("#select_tester").val());
    var team = $.URLEncode($("#select_team").val());
    var sprint = $.URLEncode($("#select_sprint").val());

    var url = "graph/index.php?type=" + type;
    if (tester != "")
        url += "&tester=" + tester;
//        if (team != "")
//            alert(team);
//            if (team != "undefined")
//                url += "&team=" + team;
    if (sprint != "")
        url += "&sprint=" + sprint;
    if (team != "")
        url += "&team=" + team;


//        + "&tester=" + tester + "&team=" + team + "&sprint=" + sprint + "";
//        $("#url_graph").html("<a href='" + url + "'>" + url + "</a>");
    $("#iframegraph").attr('src', url);
});

})
;

$.extend({URLEncode:function(c) {
    var o = '';
    var x = 0;
    c = c.toString();
    var r = /(^[a-zA-Z0-9_.]*)/;
    while (x < c.length) {
        var m = r.exec(c.substr(x));
        if (m != null && m.length > 1 && m[1] != '') {
            o += m[1];
            x += m[1].length;
        } else {
            if (c[x] == ' ')o += '+'; else {
                var d = c.charCodeAt(x);
                var h = d.toString(16);
                o += '%' + (h.length < 2 ? '0' : '') + h.toUpperCase();
            }
            x++;
        }
    }
    return o;
},
    URLDecode:function(s) {
        var o = s;
        var binVal,t;
        var r = /(%[^%]{2})/;
        while ((m = r.exec(o)) != null && m.length > 1 && m[1] != '') {
            b = parseInt(m[1].substr(1), 16);
            t = String.fromCharCode(b);
            o = o.replace(m[1], t);
        }
        return o;
    }
});