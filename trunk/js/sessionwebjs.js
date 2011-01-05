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

function validate_metrics(setuppercent, testpercent, bugpercent,
		oppertunitypercent, executed) {

	if (executed.checked == 1) {
		var setup;
		var test;
		var bug;
		var oppertunity;

		
		with (setuppercent) {
			setup = value;
		}

		with (testpercent) {
			test = value;
		}

		with (setuppercent) {
			setup = value;
		}

		with (bugpercent) {
			bug = value;
		}

		with (oppertunitypercent) {
			oppertunity = value;
		}

		var percent = parseInt(setup) + parseInt(test) + parseInt(bug)
				+ parseInt(oppertunity);

		if (parseInt(percent) != 100) {
			alert("Percentage for session is " + parseInt(percent)
					+ "%. It has to be 100%.");
			return false;
		} else {
			return true;
		}
	} else {
		return true;
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
