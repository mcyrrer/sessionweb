
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
//    alert('hi');
    var id = $('.trSelected td:nth-child(1) div').text();
    window.open ('http://localhost/sessionweb/session.php?sessionid='+id+'&command=edit','_self',false)
};

function editSession_newtab(){
    var id = $('.trSelected td:nth-child(1) div').text();
    window.open ('http://localhost/sessionweb/session.php?sessionid='+id+'&command=edit','_blank',false)
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
                        {display: 'Status', name : 'status', width : 65, sortable : true, align: 'left'},
                        {display: 'Title', name : 'title', width : 300, sortable : false, align: 'left'},
                        {display: 'User', name : 'user', width : 100, sortable : false, align: 'left'},
                        {display: 'Sprint', name : 'sprint', width : 100, sortable : false, align: 'left'},
                        {display: 'Team', name : 'team', width : 100, sortable : false, align: 'left'},
                        {display: 'Updated', name : 'updated', width : 100, sortable : true, align: 'left'},
                        {display: 'Executed', name : 'executed', width : 100, sortable : true, align: 'left', hide:true},
                        {display: 'Area', name : 'area', width : 200, sortable : false, align: 'left', hide:true}
                ],
                 buttons:[
                     {name:'Edit', bclass:'edit', onpress:editSession},
                     {name:'Edit in new tab', bclass:'edit', onpress:editSession_newtab},
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