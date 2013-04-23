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
    echo "<div id='footer'>";

    echo "<a href=\"about.php\"id=\"url_about\">About</a> | ";
    echo "<a href=\"http://www.sessionweb.org\" id=\"url_sessionweb_prj_page\">Project Home Page</a> | ";
    echo "<a href=\"http://code.google.com/p/sessionweb/issues/list\" id=\"url_submintbug\">Submit a bug report</a><br>\n";
    echo "</div>";
    echo "</div>";
    echo "  </body>\n";
    echo "</html>\n";
    ?>
