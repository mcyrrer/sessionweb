<?php
include ('headerinstall.php');
?>

<body>
<div id="container">

    <div>
        <H1>Install/Upgrade sessionweb</H1>
        <p>To install/upgrade sessionweb use menu above.</p>
        <H2>Release notes</H2>
        <pre>
<?php
echo file_get_contents('../README');
             ?>
        </pre>
    </div>


    <?php
    include ('footerinstall.php');
    ?>
