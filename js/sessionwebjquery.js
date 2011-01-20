$buglist = "";
$(document).ready(function(){

    $("#option_list").hide();

    if (myRequirements == null) {
        var myRequirements = new Array();
    }
    if (myBugs == null) {
        var myBugs = new Array();
    }
    $('#requirementlist_hidden').text(myRequirements.toString());
    $('#buglist_hidden').text(myBugs.toString());
    
    // Metrics calculation
    $("[class=metricoption]").change(function(){
        var totalPercentage = parseInt($("[name=oppertunitypercent]").val()) +
        parseInt($("[name=bugpercent]").val()) +
        parseInt($("[name=testpercent]").val()) +
        parseInt($("[name=setuppercent]").val());
        if (totalPercentage != 100) {
            $("#metricscalculation").html("<div id=\"metricscalculation_red\">Total percentage = " +
            totalPercentage +
            "%. Please adjust it to 100%.</div>");
        }
        else {
            $("#metricscalculation").html("Percentage = " +
            totalPercentage +
            "%");
        }
    });
    
    // Show search option in list.php
    $("#showoption").click(function(){
        $("#option_list").fadeIn("slow");
    });
    
    // Add bug to session and manage if it is deleted
    $("#add_bug").click(function(){
        var bugValue = $("#bug").val() + '';
        if (jQuery.inArray(bugValue, myBugs) == -1 &&
        bugValue != "") {
            myBugs.push(bugValue);
            $("#bug").attr('value', '');
            
            var newBugHtml = "<div id=\"bugdiv_" +
            bugValue +
            "\">" +
            "<table width=\"*\" border=\"0\">" +
            "    <tr>" +
            "        <td><a href=\"" +
            url_to_dms +
            "" +
            bugValue +
            "\" class=\"bugurl\" target=\”_blank\”>" +
            bugValue +
            "</a>" +
            "        </td>" +
            "        <td><div id=\"bug_" +
            bugValue +
            "\"> <img src=\"pictures/removeicon.png\" alt=\"[remove]\" /></div>" +
            "        </td>" +
            "    </tr>" +
            "</table>" +
            "</div>";
            
            $(newBugHtml).appendTo('#buglist_visible');
            
            $('#buglist_hidden').text(myBugs.toString());
            $("#bug_" + bugValue + "").click(function(){
                var thisIe = this.id;
                var bugUrlId = "bugdiv_" +
                bugValue;
                if (this.id != bugUrlId) {
                    var answer = confirm("Remove bug " +
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
    
    // Add requirement to session and manage if it is deleted
    $("#add_requirement").click(function(){
        var requirementValue = $("#requirement").val() + '';
        if (jQuery.inArray(requirementValue, myRequirements) == -1 &&
        requirementValue != "") {
            myRequirements.push(requirementValue);
            $("#requirement").attr('value', '');
            
            var newrequirementHtml = "<div id=\"requirementdiv_" +
            requirementValue +
            "\">" +
            "<table width=\"*\" border=\"0\">" +
            "    <tr>" +
            "        <td><a href=\"" +
            url_to_rms +
            "" +
            requirementValue +
            "\" class=\"requirementurl\" target=\”_blank\”>" +
            requirementValue +
            "</a>" +
            "        </td>" +
            "        <td><div id=\"requirement_" +
            requirementValue +
            "\"> <img src=\"pictures/removeicon.png\" alt=\"[remove]\" /></div>" +
            "        </td>" +
            "    </tr>" +
            "</table>" +
            "</div>";
            
            $(newrequirementHtml).appendTo('#requirementlist_visible');
            
            $('#requirementlist_hidden').text(myRequirements.toString());
            $("#requirement_" + requirementValue + "").click(function(){
                var thisIe = this.id;
                var requirementUrlId = "requirementdiv_" +
                requirementValue;
                if (this.id != requirementUrlId) {
                    var answer = confirm("Remove requirement " +
                    requirementValue +
                    "?")
                    if (answer) {
                        $("#requirementdiv_" +
                        requirementValue +
                        "").remove();
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
                alert("requirement with id " +
                requirementValue +
                " is already connected to session.");
            }
        }
    });
    
});
