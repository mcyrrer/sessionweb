
function flexi_colorGridRows(){
	$("tr").each(function() {
		var type = $(this).find('td[abbr="status"]').text()
		if (type == "Not Executed") {
			$(this).attr("class",$(this).attr("class") == "erow" ? "brown" : "brown" );
		} 
		else if (type == "In progress") {
			$(this).attr("class",$(this).attr("class") == "erow" ? "lightblue" : "lightblue" );
		}
        else if (type == "Executed") {
            $(this).attr("class",$(this).attr("class") == "erow" ? "yellow" : "yellow" );
        }
        else if (type == "Closed") {
            $(this).attr("class",$(this).attr("class") == "erow" ? "darkred" : "lightred" );
        }
        else if (type == "Debriefed") {
            $(this).attr("class",$(this).attr("class") == "erow" ? "green" : "green" );
        }
        else {
            $(this).attr("class",$(this).attr("class") == "erow" ? "white" : "white" );
        }
	});
};

function deleteSession(){
    alert('hi');
};

function editSession(){
    alert('hi');
};

function copySession(){
    alert('hi');
};


$(function() {
     $("#flexgrid1").flexigrid({
                url: 'api/list/',
                dataType: 'json',
                colModel : [
                        {display: 'ID', name : 'id', width : 25, sortable : true, align: 'left'},
                        {display: 'Status', name : 'status', width : 50, sortable : true, align: 'left'},
                        {display: 'Title', name : 'title', width : 150, sortable : true, align: 'left'},
                        {display: 'User', name : 'user', width : 200, sortable : true, align: 'left'},
                        {display: 'Sprint', name : 'sprint', width : 200, sortable : true, align: 'left'},
                        {display: 'Team', name : 'team', width : 200, sortable : true, align: 'left'},
                        {display: 'Updated', name : 'updated', width : 200, sortable : true, align: 'left'},
                        {display: 'Area', name : 'area', width : 200, sortable : true, align: 'left', hide:true}
                ],
                 buttons:[
                     {name:'Edit', bclass:'edit', onpress:editSession},
                     {name:'Delete', bclass:'delete', onpress:deleteSession},
                     {name:'Copy', bclass:'copy', onpress:copySession},
                     {name:'Share', bclass:'share', onpress:copySession},
                     {name:'Debrief', bclass:'debrief', onpress:copySession},
                     {name:'Reasign', bclass:'reasign', onpress:copySession},
                 ],
                sortname: "id",
                sortorder: "asc",
                usepager: true,
                title: "Incoming",
                useRp: false,
                rp: 50,
                showTableToggleBtn: false,
                resizable: false,
                width: 1115,
                height: 800,
				onSuccess: flexi_colorGridRows, //change row colours here
                singleSelect: true
		});
});