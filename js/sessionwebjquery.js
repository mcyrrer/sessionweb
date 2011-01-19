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
    
    // Add bug to session.
    $("#add_bug").click(function(){
        $bug = $("#bug").val()+'';
        
        myBugs.push($bug);
        
        
        $("<div id=\"bug_" + $bug + "\" class=\"test\"><p>" + $bug + "</p></div>").appendTo('#buglist_visible');
        alert(myBugs);
        for (var i = 0; i < myBugs.length; i++) {
            var value = myBugs[i];
            

            $("#bug_" + value + "").click(function(){
                $("#bug_" + value + "").remove();
                myBugs.remove(value);
            });
        }
        
        
        
    });
    
    //        $thisid = this.id; //Good to have when we want to remove bugs from list.....
    //        $bug = $("#bug").val();
    //        $buglist = $buglist + $bug + "|";
    //        
    //        var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + $bug);
    //        newTextBoxDiv.after().html("test!!!");
    //        
    //        
    //        //        newTextBoxDiv.appendTo("#buglist_visible");
    //        $('#buglist_visible').append(newTextBoxDiv);
    //        
    //        //        $('#buglist_visible').append($bug + "<br>");
    //        $('#buglist_hidden').text($buglist);

});
