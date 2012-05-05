$(document).ready(function () {
    setupDatePicker();
});


function setupDatePicker()
{
    var dates = $( "#from, #to" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onSelect: function( selectedDate ) {
            var option = this.id == "from" ? "minDate" : "maxDate",
                instance = $( this ).data( "datepicker" ),
                date = $.datepicker.parseDate(
                    instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                    selectedDate, instance.settings );
            dates.not( this ).datepicker( "option", option, date );
        }
    });
    $( "#from" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
    $( "#to" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

}



