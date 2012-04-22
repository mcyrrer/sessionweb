$(document).ready(function () {
    getUserSettings();

    populateRemoveAreasSelect();
    populateRemoveTeamsSelect();
    populateRemoveSprintsSelect();
    populateRemovetesTenvironmentsSelect();

    showAndHideHtml();

    addArea();
    removeArea();
    addTeam();
    removeTeam();
    addSprint();
    removeSprint();
    addtestEnvironment();
    removetestEnvironment();

    changePersonalPassword();
    updatePersonalSettings();

    updateCustomFields();

});

function updatePersonalSettings() {
    $('#change_personal_settings_exe').click(function () {

        $.ajax({
            type:"GET",
            data:"listsettings=" + $('#personal_changelistsettings_options').val()
                + "&team=" + $('#personal_select_team').val()
                + "&sprint=" + $('#personal_select_sprint').val()
                + "&area=" + $('#personal_select_area').val(),
            url:'api/settings/user/personalsettings/update/index.php',
            complete:function (data) {

                if (data.status == '201') {
                    $('#log').prepend('<div class="log_div">User settings updated.</div>');
                }
                else if (data.status == '400') {
                    $('#log').prepend('<div class="log_div">Error: User settings not added in request.</div>');
                }
                else if (data.status == '401') {
                    $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                }
                else if (data.status == '500') {
                    $('#log').prepend('<div class="log_div">Error: User settings not updated due to internal server error.</div>');
                }
                $('#areaname').val('')
            }
        });
    });
}

function applyUserSettingsToLayout(userSettings) {
    if (parseInt(userSettings['superuser']) == 0 && parseInt(userSettings['admin']) == 0) {
        $("#manage_content").hide();
        $("#site_settings").hide();
    }
    else if (parseInt(userSettings['admin']) != 1) {
        $("#team_menu").hide();
        $("#testenvironments_menu").hide();
        $("#sprint_menu").hide();
        $("#site_settings").hide();

    }

}

function getUserSettings() {
    $.ajax({
        type:"GET",
        url:'api/settings/user/sitesettings/get/',
        complete:function (data, xhr, statusText) {
            if (data.status == '200') {
                $('#remove_area_select').html('');
                var jsonResponseContent = jQuery.parseJSON(data.responseText);
                applyUserSettingsToLayout(jsonResponseContent);
            }
            else if (data.status == '500') {
                $('#log').prepend('<div class="log_div">Error: SQL Error during load of personal settings</div>');
            }
        }
    });
}

function populateRemoveAreasSelect() {
    $.ajax({
        type:"GET",
        url:'api/area/getareas/index.php',
        complete:function (data, xhr, statusText) {
            if (data.status == '200') {
                $('#remove_area_select').html('');
                var jsonResponseContent = jQuery.parseJSON(data.responseText);
                var optionTxt = "";
                $.each(jsonResponseContent, function (index, value) {
                    $('#remove_area_select').append('<option>' + value + '</option>');
                });
            }
            else if (data.status == '401') {
                $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

            }
            else if (data.status == '500') {
                $('#log').prepend('<div class="log_div">Error: SQL Error</div>');
            }
        }
    });
}


function showAndHideHtml() {
    $("#change_personal_password").hide();
    $("#change_personal_settings").hide();
    $("#add_remove_team").hide();
    $("#add_remove_sprint").hide();
    $("#add_remove_area").hide();
    $("#add_remove_testenvironment").hide();
    $("#add_remove_custom_fields").hide();
    $("#change_configuration").hide();
    $("#systemcheck").hide();
    $("#manage_customfields").hide();



    $('#change_personal_password_menu').click(function () {
        if ($("#change_personal_password").is(':hidden'))
            $("#change_personal_password").fadeIn("slow");
        else
            $("#change_personal_password").fadeOut("slow");
    });

    $('#change_personal_settings_menu').click(function () {
        if ($("#change_personal_settings").is(':hidden'))
            $("#change_personal_settings").fadeIn("slow");
        else
            $("#change_personal_settings").fadeOut("slow");
    });

    $('#team_menu').click(function () {
        if ($("#add_remove_team").is(':hidden'))
            $("#add_remove_team").fadeIn("slow");
        else
            $("#add_remove_team").fadeOut("slow");
    });

    $('#sprint_menu').click(function () {
        if ($("#add_remove_sprint").is(':hidden'))
            $("#add_remove_sprint").fadeIn("slow");
        else
            $("#add_remove_sprint").fadeOut("slow");
    });

    $('#area_menu').click(function () {
        if ($("#add_remove_area").is(':hidden'))
            $("#add_remove_area").fadeIn("slow");
        else
            $("#add_remove_area").fadeOut("slow");
    });

    $('#testenvironments_menu').click(function () {
        if ($("#add_remove_testenvironment").is(':hidden'))
            $("#add_remove_testenvironment").fadeIn("slow");
        else
            $("#add_remove_testenvironment").fadeOut("slow");
    });

    $('#custom_fields_menu').click(function () {
        if ($("#add_remove_custom_fields").is(':hidden'))
            $("#add_remove_custom_fields").fadeIn("slow");
        else
            $("#add_remove_custom_fields").fadeOut("slow");
    });

    $('#configuration_menu').click(function () {
        if ($("#change_configuration").is(':hidden'))
            $("#change_configuration").fadeIn("slow");
        else
            $("#change_configuration").fadeOut("slow");
    });

    $('#systemcheck_menu').click(function () {
        if ($("#systemcheck").is(':hidden'))
            $("#systemcheck").fadeIn("slow");
        else
            $("#systemcheck").fadeOut("slow");
    });

    $('#customfields_menu').click(function () {
        if ($("#manage_customfields").is(':hidden'))
            $("#manage_customfields").fadeIn("slow");
        else
            $("#manage_customfields").fadeOut("slow");
    });
}

function removeArea() {
    $('#remove_area').click(function () {
        $.ajax({
            type:"GET",
            data:"area=" + $('#remove_area_select').val(),
            url:'api/settings/area/remove/index.php',
            complete:function (data) {
                if (data.status == '200') {
                    $('#log').prepend('<div class="log_div">Area ' + $('#remove_area_select').val() + ' removed.</div>');
                }
                else if (data.status == '401') {
                    $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                }
                else if (data.status == '500') {
                    $('#log').prepend('<div class="log_div">Error: Item not removed</div>');
                }
                populateRemoveAreasSelect();
            }
        });
    });
}

function addArea() {
    $('#add_area').click(function () {
        if ($('#areaname').val() != '') {
            $.ajax({
                type:"GET",
                data:"area=" + $('#areaname').val(),
                url:'api/settings/area/add/index.php',
                complete:function (data) {
                    populateRemoveAreasSelect();
                    if (data.status == '201') {
                        $('#log').prepend('<div class="log_div">Area ' + $('#areaname').val() + ' added.</div>');
                    }
                    else if (data.status == '400') {
                        $('#log').prepend('<div class="log_div">Error: Area name not added in request.</div>');
                    }
                    else if (data.status == '401') {
                        $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                    }
                    else if (data.status == '409') {
                        $('#log').prepend('<div class="log_div">Area ' + $('#areaname').val() + ' already exist.</div>');
                    }
                    else if (data.status == '500') {
                        $('#log').prepend('<div class="log_div">Error: Item not added due to internal server error.</div>');
                    }
                    $('#areaname').val('')
                }
            });
        }
        else {
            $('#log').prepend('<div class="log_div">Area name not added in request.</div>');
        }
    });
}

function populateRemoveTeamsSelect() {
    $.ajax({
        type:"GET",
        url:'api/team/getteams/index.php',
        complete:function (data, xhr, statusText) {
            if (data.status == '200') {
                $('#remove_team_select').html('');
                var jsonResponseContent = jQuery.parseJSON(data.responseText);
                var optionTxt = "";
                $.each(jsonResponseContent, function (index, value) {
                    $('#remove_team_select').append('<option>' + value + '</option>');
                });
            }
            else if (data.status == '401') {
                $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

            }
            else if (data.status == '500') {
                $('#log').prepend('<div class="log_div">Error: SQL Error</div>');
            }
        }
    });
}

function updateCustomFields() {
    $('#update_customfields').click(function () {
            $.ajax({
                type:"GET",
                data:"cf1=" + $('#cf1Name').val()+
                    "&cf2=" + $('#cf2Name').val()+
                    "&cf3=" + $('#cf3Name').val()+
                    "&cf1multiselect=" + $('#cf1multiselect').is(':checked')+
                    "&cf2multiselect=" + $('#cf2multiselect').is(':checked')+
                    "&cf3multiselect=" + $('#cf3multiselect').is(':checked')+
                    "&cf1enabled=" + $('#cf1enabled').is(':checked')+
                    "&cf2enabled=" + $('#cf2enabled').is(':checked')+
                    "&cf3enabled=" + $('#cf3enabled').is(':checked'),
                url:'api/settings/customfields/addupdate/index.php',
                complete:function (data) {

                    if (data.status == '201') {
                        $('#log').prepend('<div class="log_div">Custom fields added/updated/enabled.</div>');
                        populateRemoveTeamsSelect();
                    }
                    else if (data.status == '400') {
                        $('#log').prepend('<div class="log_div">Error: Custom fields parameters not added in request.</div>');
                    }
                    else if (data.status == '401') {
                        $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                    }
                    else if (data.status == '500') {
                        $('#log').prepend('<div class="log_div">Error: Item not added due to internal server error.</div>');
                    }
                    $('#teamname').val('')
                }
            });

    });
}

function addTeam() {
    $('#add_team').click(function () {
        if ($('#teamname').val() != '') {
            $.ajax({
                type:"GET",
                data:"team=" + $('#teamname').val(),
                url:'api/settings/team/add/index.php',
                complete:function (data) {

                    if (data.status == '201') {
                        $('#log').prepend('<div class="log_div">Team ' + $('#teamname').val() + ' added.</div>');
                        populateRemoveTeamsSelect();
                    }
                    else if (data.status == '400') {
                        $('#log').prepend('<div class="log_div">Error: Team name not added in request.</div>');
                    }
                    else if (data.status == '401') {
                        $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                    }
                    else if (data.status == '409') {
                        $('#log').prepend('<div class="log_div">Team ' + $('#teamname').val() + ' already exist.</div>');
                    }
                    else if (data.status == '500') {
                        $('#log').prepend('<div class="log_div">Error: Item not added due to internal server error.</div>');
                    }
                    $('#teamname').val('')
                }
            });
        }
        else {
            $('#log').prepend('<div class="log_div">Team name not added in request.</div>');
        }
    });
}

function removeTeam() {
    $('#remove_team').click(function () {
        $.ajax({
            type:"GET",
            data:"team=" + $('#remove_team_select').val(),
            url:'api/settings/team/remove/index.php',
            complete:function (data) {
                if (data.status == '200') {
                    $('#log').prepend('<div class="log_div">Team ' + $('#remove_team_select').val() + ' removed.</div>');
                }
                else if (data.status == '401') {
                    $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                }
                else if (data.status == '500') {
                    $('#log').prepend('<div class="log_div">Error: Item not removed</div>');
                }
                populateRemoveTeamsSelect();
            }
        });
    });
}


function populateRemoveSprintsSelect() {
    $.ajax({
        type:"GET",
        url:'api/sprint/getsprints/index.php',
        complete:function (data, xhr, statusText) {
            if (data.status == '200') {
                $('#remove_sprint_select').html('');
                var jsonResponseContent = jQuery.parseJSON(data.responseText);
                var optionTxt = "";
                $.each(jsonResponseContent, function (index, value) {
                    $('#remove_sprint_select').append('<option>' + value + '</option>');
                });
            }
            else if (data.status == '401') {
                $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

            }
            else if (data.status == '500') {
                $('#log').prepend('<div class="log_div">Error: SQL Error</div>');
            }
        }
    });
}


function addSprint() {
    $('#add_sprint').click(function () {
        if ($('#sprintname').val() != '') {
            $.ajax({
                type:"GET",
                data:"sprint=" + $('#sprintname').val(),
                url:'api/settings/sprint/add/index.php',
                complete:function (data) {

                    if (data.status == '201') {
                        $('#log').prepend('<div class="log_div">sprint ' + $('#sprintname').val() + ' added.</div>');
                        populateRemoveSprintsSelect();
                    }
                    else if (data.status == '400') {
                        $('#log').prepend('<div class="log_div">Error: sprint name not added in request.</div>');
                    }
                    else if (data.status == '401') {
                        $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                    }
                    else if (data.status == '409') {
                        $('#log').prepend('<div class="log_div">sprint ' + $('#sprintname').val() + ' already exist.</div>');
                    }
                    else if (data.status == '500') {
                        $('#log').prepend('<div class="log_div">Error: Item not added due to internal server error.</div>');
                    }
                    $('#sprintname').val('')
                }
            });
        }
        else {
            $('#log').prepend('<div class="log_div">sprint name not added in request.</div>');
        }
    });
}

function removeSprint() {
    $('#remove_sprint').click(function () {
        $.ajax({
            type:"GET",
            data:"sprint=" + $('#remove_sprint_select').val(),
            url:'api/settings/sprint/remove/index.php',
            complete:function (data) {
                if (data.status == '200') {
                    $('#log').prepend('<div class="log_div">sprint ' + $('#remove_sprint_select').val() + ' removed.</div>');
                }
                else if (data.status == '401') {
                    $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                }
                else if (data.status == '500') {
                    $('#log').prepend('<div class="log_div">Error: Item not removed</div>');
                }
                populateRemoveSprintsSelect();
            }
        });
    });
}

function populateRemovetesTenvironmentsSelect() {
    $.ajax({
        type:"GET",
        url:'api/testenvironment/gettestenvironment/index.php',
        complete:function (data, xhr, statusText) {
            if (data.status == '200') {
                $('#remove_testenvironment_select').html('');
                var jsonResponseContent = jQuery.parseJSON(data.responseText);
                var optionTxt = "";
                $.each(jsonResponseContent, function (index, value) {
                    $('#remove_testenvironment_select').append('<option>' + value + '</option>');
                });
            }
            else if (data.status == '401') {
                $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

            }
            else if (data.status == '500') {
                $('#log').prepend('<div class="log_div">Error: SQL Error</div>');
            }
        }
    });
}


function addtestEnvironment() {
    $('#add_testenvironment').click(function () {
        if ($('#testenvironmentname').val() != '') {
            $.ajax({
                type:"GET",
                data:"environment=" + $('#teName').val() +
                    "&url=" + $('#teUrl').val() +
                    "&username=" + $('#teUser').val() +
                    "&password=" + $('#tePassword').val(),
                url:'api/settings/testenvironment/add/index.php',
                complete:function (data) {

                    if (data.status == '201') {
                        $('#log').prepend('<div class="log_div">testenvironment ' + $('#teName').val() + ' added.</div>');
                        populateRemovetesTenvironmentsSelect();
                    }
                    else if (data.status == '400') {
                        $('#log').prepend('<div class="log_div">Error: testenvironment name not added in request.</div>');
                    }
                    else if (data.status == '401') {
                        $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                    }
                    else if (data.status == '409') {
                        $('#log').prepend('<div class="log_div">testenvironment ' + $('#teName').val() + ' already exist.</div>');
                    }
                    else if (data.status == '500') {
                        $('#log').prepend('<div class="log_div">Error: Item not added due to internal server error.</div>');
                    }
                    $('#testenvironmentname').val('')
                }
            });
        }
        else {
            $('#log').prepend('<div class="log_div">testenvironment name not added in request.</div>');
        }
    });
}

function removetestEnvironment() {
    $('#remove_testenvironment').click(function () {
        $.ajax({
            type:"GET",
            data:"environment=" + $('#remove_testenvironment_select').val(),
            url:'api/settings/testenvironment/remove/index.php',
            complete:function (data) {
                if (data.status == '200') {
                    $('#log').prepend('<div class="log_div">testenvironment ' + $('#remove_testenvironment_select').val() + ' removed.</div>');
                }
                else if (data.status == '401') {
                    $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                }
                else if (data.status == '500') {
                    $('#log').prepend('<div class="log_div">Error: Item not removed</div>');
                }
                populateRemovetesTenvironmentsSelect();
            }
        });
    });
}

function changePersonalPassword() {
    $('#change_personal_password_exe').click(function () {

        if ($('#changepasswordold').val() != '' && $('#changepassword1').val() != '' && $('#changepassword2').val() != '') {
            if ($('#changepassword1').val() == $('#changepassword2').val()) {
                if ($('#changepassword1').val().length > 5) {
                    $.ajax({
                        type:"GET",
                        data:"changepasswordold=" + $('#changepasswordold').val() +
                            "&changepassword1=" + $('#changepassword1').val() +
                            "&changepassword2=" + $('#changepassword2').val(),
                        url:'api/settings/user/password/change/index.php',
                        complete:function (data) {

                            if (data.status == '201') {
                                $('#log').prepend('<div class="log_div">Password changed.</div>');

                            }
                            else if (data.status == '400') {
                                $('#log').prepend('<div class="log_div">Error: parameters is not correct.</div>');
                            }
                            else if (data.status == '401') {
                                $('#log').prepend('<div class="log_div">Error: Unauthorized.</div>');

                            }
                            else if (data.status == '500') {
                                $('#log').prepend('<div class="log_div">Error: Item not added due to internal server error.</div>');
                            }
                            else {
                                $('#log').prepend('<div class="log_div">Error: Some error that could not be determined have happend.</div>');

                            }

                        }
                    });
                }
                else {
                    $('#log').prepend('<div class="log_div">Password is too short. Need to longer then 5 characters.</div>');

                }
            }
            else {
                $('#log').prepend('<div class="log_div">New Password does not match</div>');

            }
        }
        else {
            $('#log').prepend('<div class="log_div">You need to add your old password AND the new one(twice)</div>');
        }
    });
}



