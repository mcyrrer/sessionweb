$(document)
		.ready(

				function() {

					$("#option_list").hide();

					//Metrics calculation 
					$("[class=metricoption]")
							.change(
									function() {
										var totalPercentage = parseInt($(
												"[name=oppertunitypercent]")
												.val())
												+ parseInt($(
														"[name=bugpercent]")
														.val())
												+ parseInt($(
														"[name=testpercent]")
														.val())
												+ parseInt($(
														"[name=setuppercent]")
														.val());
										if (totalPercentage != 100) {
											$("#metricscalculation")
													.html(
															"<div id=\"metricscalculation_red\">Total percentage = "
																	+ totalPercentage
																	+ "%. Please adjust it to 100%.</div>");
										} else {
											$("#metricscalculation").html(
													"Percentage = "
															+ totalPercentage
															+ "%");
										}
									});
	
					//Show search option in list.php
					$("#showoption").click(function() {
							$("#option_list").fadeIn("slow");
					});
					
					
					//Add bug to session.
					$("#bug_entry").focusout(function() {
						
						("#bugs").html(
								"Percentage = ");
//						  fo++;
//						  $("#fo")
//						    .text("focusout fired: " + fo + "x");
//						}).blur(function() {
//						  b++;
//						  $("#b")
//						    .text("blur fired: " + b + "x");
						  
						});


				}

		);
