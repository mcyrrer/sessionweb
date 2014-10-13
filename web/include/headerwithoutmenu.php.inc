<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=utf-8\">\n";
echo "      <title>Sessionweb</title>\n";

echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"".$basepathcss."sessionwebcss.css\">\n";
echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"".$basepathcss."css/colorbox.css\">\n";
echo "     <script src=\"".$basepathcss."js/jquery-1.9.1.min.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"".$basepathcss."js/jquery.autosave.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"".$basepathcss."js/jquery.validate.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"".$basepathcss."js/jquery.getparams.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"".$basepathcss."js/jquery.colorbox-min.js\" type=\"text/javascript\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"".$basepathcss."js/sessionwebjs-v27.js\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"".$basepathcss."js/sessionwebjquery-v27.js\"></script>\n";
if ($_SESSION['settings']['analyticsid'] != "") {
    echo " <script type=\"text/javascript\">\n";

    echo "   var _gaq = _gaq || [];\n";
    echo "   _gaq.push(['_setAccount', '" . $_SESSION['settings']['analyticsid'] . "']);\n";
    echo "   _gaq.push(['_trackPageview']);\n";

    echo "   (function() {\n";
    echo "     var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
    echo "     ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
    echo "     var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
    echo "   })();\n";

    echo " </script>\n";
}
echo "  </head>\n";

?>