function validate_required(field, alerttxt) {
	with (field) {
		if (value == null || value == "") {
			alert(alerttxt);
			return false;
		} else {
			return true;
		}
	}
}

function validate_form(thisform) {

	with (thisform) {
		if (validate_required(title, "Title must be filled out!") == false) {
			title.focus();
			return false;
		}
		
		if (validate_metrics(setuppercent, testpercent, bugpercent,
				oppertunitypercent, executed) == false) {
			setuppercent.focus();
			return false;
		}

	}
}
