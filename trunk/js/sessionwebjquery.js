$buglist = "";
$(document).ready(function(){

    $("#option_list").hide();
    var myBugs = new Array();
    
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
        if (jQuery.inArray(bugValue, myBugs) == -1 && bugValue!="") {
            myBugs.push(bugValue);
            $("#bug").attr('value', '');
            $("<div id=\"bug_" + bugValue + "\" class=\"test\"><p>" + bugValue + "</p></div>").appendTo('#buglist_visible');
            $('#buglist_hidden').text(myBugs.toString());
            $("#bug_" + bugValue + "").click(function(){
                var answer = confirm("Remove bug " + bugValue + "?")
                if (answer) {
                    $("#bug_" + bugValue + "").remove();
                    bugPos = jQuery.inArray(bugValue, myBugs);
                    if (bugPos != -1) {
                        var removedelements = myBugs.splice(bugPos, 1);//remove();
                        $('#buglist_hidden').text(myBugs.toString());
                    }
                }
            });
        }
        else {
			if (bugValue == "") {
//				alert("Bug with id " + bugValue + " is already connected to session.");
			}
			else
			{
				alert("Bug with id " + bugValue + " is already connected to session.");
			}
        }
    });
    
    
});
