<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="css/sessionwebcss.css">
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/counterstring.js"></script>
</head>
<body>
<h1>Test utilities</h1>

<h1>Create counterstring</h1>
Add length of counterstring to create:
<input type="text" id="cnt" name="cnt"/>
<input type="submit" id="pressme" value="Create"/>
<br>
<script type="text/javascript">
    $(document).ready(function () {
        $("#pressme").click(function (event) {
            if (parseInt($("#cnt").val()) <= 1000000) {
                var cntString = counterstring($("#cnt").val());
                $("#counterstring").val(cntString);

                var target = document.getElementById("counterstring");
                createSelection(0, $("#cnt").val(), target);
            }
            else {
                $("#counterstring").val("To high value. Max value is 1000000");
            }
        });
    });
</script>
Use ctrl-c and ctrl-v to copy and paste the string.<br>
If you choose a very large number like > 1000000 your browser will freeze so keep the value below 100000 and it will go
fast to create the string.
<table>
    <tr class="counterstingtable">
        <td class="counterstingtable">
            <textarea rows="10" cols="80" id="counterstring"></textarea>


        </td>
    </tr>
</table>

<h1>Test string for database tests and escape tests</h1>
<pre>'"$%><*!);--//</pre>

<h1>Test sentences</h1>
<pre><?php
echo file_get_contents('include/quickbrown.txt');

    ?>
</pre>
<?php
//$codeunits = array();
//for ($i = 0; $i<0xD800; $i++)
//    $codeunits[] = unichr($i);
//for ($i = 0xE000; $i<0xFFFF; $i++)
//    $codeunits[] = unichr($i);
//$all = implode($codeunits);
//echo $all;
?>
</body>
</html>

<?php

function unichr($i)
{
    return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
}


?>