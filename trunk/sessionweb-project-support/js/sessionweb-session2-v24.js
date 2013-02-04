$(document).ready(function () {

//    $('#divTitle').hide();
    var sessionID = $(document).getUrlParam("sessionid");
    $.ajax({
        type: "GET",
        data: { sessionid: sessionID},
        url: 'api/session/get/index.php',
        complete: function (data) {

            if (data.status == '200') {
                var jsonResponseContent = jQuery.parseJSON(data.responseText);
                setSessionData(jsonResponseContent);
                $("#tabs").tabs();

            }
            else if (data.status == '400') {
                $('#message').prepend('<div class="log_div">Error: Some parameters was missing in request.</div>');
            }
            else if (data.status == '401') {
                $('#message').prepend('<div class="log_div">Error: Unauthorized.</div>');
            }
            else if (data.status == '409') {
                $('#message').prepend('<div class="log_div">Warning: User ' + $('#user_username').val() + 'already exist!</div>');
            }
            else if (data.status == '500') {
                $('#message').prepend('<div class="log_div">Error: User not added due to internal server error.</div>');
            }
        }
    });


    AddRequirementManager();

});

function AddRequirementManager() {
    $('#new_requirement').hide();
    $('#addReq').click(function () {
        $('#new_requirement').show();
        $('#new_requirement').focus();
    });
    $("#new_requirement").keypress(function () {
        if (event.which == 13) {
            AddSingleRequirement(this.value);
            $('#new_requirement').val("");
        }
    });
    $("#new_requirement").focusout(function () {
        $('#new_requirement').hide();
        $('#new_requirement').val("");
    });
}

function AddRequirementManager() {
    $('#new_sessionlink').hide();
    $('#addSessionLink').click(function () {
        $('#new_sessionlink').show();
        $('#new_sessionlink').focus();
    });
    $("#new_sessionlink").keypress(function () {
        if (event.which == 13) {
            AddSingleSessionLink(this.value);
            $('#new_sessionlink').val("");
        }
    });
    $("#new_sessionlink").focusout(function () {
        $('#new_sessionlink').hide();
        $('#new_sessionlink').val("");
    });
}

/**
 * Set all data in the form to the stored values from DB
 * @param jsonResponseContent jQuery.parseJSON(data) content from URL api/session/get/index.php
 */
function setSessionData(jsonResponseContent) {
    //Title
    $('#input_title').val(jsonResponseContent['title']);
    $('#input_title_span').text(jsonResponseContent['title']);
    //Team
    $('#idTeam').val(jsonResponseContent['teamname']);

    //Sprint
    $('#idSprint').val(jsonResponseContent['sprintname']);

    //Area
    $('#idArea').val(jsonResponseContent['areas']);

    //AdditionalTester
    $('#idAdditionalTester').val(jsonResponseContent['additional_testers']);

    //testenvironment
    $('#idEnvironment').val(jsonResponseContent['testenvironment']);

    //idSoftwareUnderTest
    $('#idSoftwareUnderTest').val(jsonResponseContent['software']);

    //Requriements
    PopulateRequirements(jsonResponseContent['requirements']);

    //Link to other sessions
    PopulateLinksToOtherSessions(jsonResponseContent['linked_to_session']);

    //Autofetched sw
    PopulateAutofetchedSoftware(jsonResponseContent['softwareuseautofetched']);


    //Charter Content
    setTimeout(function () {
        SetContentsCharter(jsonResponseContent['charter']);
    }, 100);

    //Notes Content
    setTimeout(function () {
        SetContentsNotes(jsonResponseContent['notes']);
    }, 100);

    $(".colorPopUp").colorbox({iframe: true, width: "80%", height: "80%"});
}

function PopulateAutofetchedSoftware(softwareids) {
    softwareids.forEach(function (aId) {
        $('#autoSoftwareVersions').append("<a class=\"colorPopUp cboxElement\" id=\"swauto_" + aId + "\" href=\"api/environments/getrunningversions/index.php?id=" + aId + "\">api/environments/getrunningversions/index.php?id=" + aId + "</a>");
        //alert("<p id='swauto_"+aId+">api/environments/getrunningversions/index.php?id="+aId+"</p>");
    });
}

function PopulateRequirements(req) {

    req.forEach(function (aReq) {
        AddSingleRequirement(aReq)
    });

}

function AddSingleRequirement(aReq)
{
    $('#testReqId').append('<p id="' + aReq + 'REQ">' + aReq + ': Loading title</p>');
    $.ajax({
        type: "GET",
        data: { reqId: aReq},

        url: 'api/titles/requirement/get/index.php',
        complete: function (data) {

            if (data.status == '200') {
                var title = data.responseText;
                $('#' + aReq + 'REQ').html(+aReq + ': ' + title + '');
            }
        }
    });
}

function AddSingleSessionLink(aLink)
{
    $('#linkToOtherSessions').append('<p id="' + aLink + 'SessionLink">' + aLink + ': Loading title</p>');
//    $.ajax({
//        type: "GET",
//        data: { reqId: aLink},
//
//        url: 'api/titles/requirement/get/index.php',
//        complete: function (data) {
//
//            if (data.status == '200') {
//                var title = data.responseText;
//                $('#' + aLink + 'SessionLink').html(+aLink + ': ' + title + '');
//            }
//        }
//    });
}


function PopulateLinksToOtherSessions(linksToOtherSessions) {

    linksToOtherSessions.forEach(function (aLink) {
        $('#linkToOtherSessions').append('<p id="' + aLink + 'REQ">' + aLink + ': Loading title</p>');
        $.ajax({
            type: "GET",
            data: { sessionid: aLink},

            url: 'api/titles/session/get/index.php',
            complete: function (data) {
                if (data.status == '200') {
                    var title = data.responseText;
                    $('#' + aLink + 'REQ').html(+aLink + ': ' + title + '');
                }
            }
        });
    });
}

function SetContentsCharter(text) {
    var editor = CKEDITOR.instances.chartereditor;
    editor.setData(text);
}

function SetContentsNotes(text) {
    var editor = CKEDITOR.instances.noteseditor;
    editor.setData(text);

}

function GetContentsCharter() {
    var editor = CKEDITOR.instances.chartereditor;
    return editor.getData();
}

function GetContentsNotes() {
    var editor = CKEDITOR.instances.noteseditor;
    return editor.getData();
}

