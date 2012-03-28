<?php
require_once('../../include/loggingsetup.php');
session_start();
require_once('../../include/validatesession.inc');
require_once("../../include/db.php");
define("GET_FILE_PATH", "get.php");
define("DELETE_FILE_PATH", "delete.php");
define("THUMB_FILE_PATH","thumbnails/");

include "../../config/db.php.inc";

$picture_mimetypes = array("jpg" => "image/jpeg","jpeg" => "image/jpeg","gif" => "image/gif","png" => "image/png");

GET_FILE_PATH;
//$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
//mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
$con=getMySqlConnection();





?>
<html lang="en" class="no-js ">
<head>
    <meta charset="utf-8">
    <title>Upload files</title>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css"
          id="theme">
    <link rel="stylesheet" href="../../js/jQuery-File-Upload/jquery.fileupload-ui.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="fileupload" class="ui-widget">
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="sessionid" value="<?php echo $_GET['sessionid'];?>">

        <div class="fileupload-buttonbar ui-widget-header ui-corner-top">
            <label class="fileinput-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
                   role="button"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span><span
                    class="ui-button-text">
                <span>Add files...</span>
                
            </span><input type="file" name="files[]" multiple=""></label>
            <button type="submit"
                    class="start ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
                    role="button" aria-disabled="false"><span
                    class="ui-button-icon-primary ui-icon ui-icon-circle-arrow-e"></span><span class="ui-button-text">Start upload</span>
            </button>
            <button type="reset"
                    class="cancel ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
                    role="button" aria-disabled="false"><span
                    class="ui-button-icon-primary ui-icon ui-icon-cancel"></span><span class="ui-button-text">Cancel upload</span>
            </button>
            <button type="button"
                    class="delete ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
                    role="button" aria-disabled="false"><span
                    class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete files</span>
            </button>
        </div>
    </form>
    <div class="fileupload-content ui-widget-content ui-corner-bottom">
        <table class="files">
            <tbody>
<?php
$sql = "SELECT id, mission_versionid, filename, size, mimetype FROM `mission_attachments` WHERE `mission_versionid` = " . $_GET['sessionid'];

$result = mysql_query($sql) or die($sql . 'Error, query failed');

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
 
    $id = $row[0];
    $filename = $row[2];
    $size = $row[3];
    $mimetype = $row[4];

    echo "<tr class='template-download' style=''>\n";
    if(in_array($mimetype,$picture_mimetypes))
    {
        echo "                <td class='preview'> <a href='".GET_FILE_PATH."?id=".$id."' target='_blank'><img src='".THUMB_FILE_PATH.htmlspecialchars($filename)."'></a></td>\n";
    }
    else
    {
        echo "                <td class='preview'></td>\n";

    }

        if(in_array($mimetype,$picture_mimetypes))
    {
        echo "                <td class='name'><a href='".GET_FILE_PATH."?id=".$id."' target='_blank'>".$filename."</a>\n";

        }
    else
    {
        echo "                <td class='name'><a href='".GET_FILE_PATH."?id=".$id."'>".$filename."</a>\n";


    }


    echo "                </td>\n";
    echo "                <td class='size'>".$size."</td>\n";
    echo "                <td colspan='2'></td>\n";
    echo "                <td class='delete'>\n";
    echo "                    <button data-type='DELETE'\n";
    echo "                            data-url='".DELETE_FILE_PATH."?id=".$id."'\n";
    echo "                            class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button'\n";
    echo "                            aria-disabled='false' title='Delete'><span\n";
    echo "                            class='ui-button-icon-primary ui-icon ui-icon-trash'></span><span class='ui-button-text'>Delete</span>\n";
    echo "                    </button>\n";
    echo "                </td>\n";
    echo "            </tr>\n";

}

mysql_close();
?>

<!--<tr class="template-download" style="">-->
<!--    <td class="preview"></td>-->
<!--    <td class="name"><a href="/sessionweb/include/filemanagement/files/apache-continuum-1.3.7-bin.zip">apache-continuum-1.3.7-bin.zip</a>-->
<!--    </td>-->
<!--    <td class="size">29.21 MB</td>-->
<!--    <td colspan="2"></td>-->
<!--    <td class="delete">-->
<!--        <button data-type="DELETE"-->
<!--                data-url="/sessionweb/include/filemanagement/upload.php?file=apache-continuum-1.3.7-bin.zip"-->
<!--                class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button"-->
<!--                aria-disabled="false" title="Delete"><span-->
<!--                class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete</span>-->
<!--        </button>-->
<!--    </td>-->
<!--</tr>-->
<!--<tr class="template-download" style="">-->
<!--    <td class="preview"></td>-->
<!--    <td class="name"><a href="/sessionweb/include/filemanagement/files/apache-maven-2.2.1-bin.zip">apache-maven-2.2.1-bin.zip</a>-->
<!--    </td>-->
<!--    <td class="size">2.85 MB</td>-->
<!--    <td colspan="2"></td>-->
<!--    <td class="delete">-->
<!--        <button data-type="DELETE"-->
<!--                data-url="/sessionweb/include/filemanagement/upload.php?file=apache-maven-2.2.1-bin.zip"-->
<!--                class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button"-->
<!--                aria-disabled="false" title="Delete"><span-->
<!--                class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete</span>-->
<!--        </button>-->
<!--    </td>-->
<!--</tr>-->
<!--<tr class="template-download" style="">-->
<!--    <td class="preview"></td>-->
<!--    <td class="name"><a href="/sessionweb/include/filemanagement/files/bbtsta2.exe">bbtsta2.exe</a></td>-->
<!--    <td class="size">10.16 MB</td>-->
<!--    <td colspan="2"></td>-->
<!--    <td class="delete">-->
<!--        <button data-type="DELETE" data-url="/sessionweb/include/filemanagement/upload.php?file=bbtsta2.exe"-->
<!--                class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button"-->
<!--                aria-disabled="false" title="Delete"><span-->
<!--                class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete</span>-->
<!--        </button>-->
<!--    </td>-->
<!--</tr>-->
<!--<tr class="template-download" style="">-->
<!--    <td class="preview"></td>-->
<!--    <td class="name"><a-->
<!--            href="/sessionweb/include/filemanagement/files/blueimp-jQuery-File-Upload-b927a2d.zip">blueimp-jQuery-File-Upload-b927a2d.zip</a>-->
<!--    </td>-->
<!--    <td class="size">35.37 KB</td>-->
<!--    <td colspan="2"></td>-->
<!--    <td class="delete">-->
<!--        <button data-type="DELETE"-->
<!--                data-url="/sessionweb/include/filemanagement/upload.php?file=blueimp-jQuery-File-Upload-b927a2d.zip"-->
<!--                class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button"-->
<!--                aria-disabled="false" title="Delete"><span-->
<!--                class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete</span>-->
<!--        </button>-->
<!--    </td>-->
<!--</tr>-->
<!--<tr class="template-download" style="">-->
<!--    <td class="preview"><a href="/sessionweb/include/filemanagement/files/Water%20lilies.jpg"-->
<!--                           target="_blank"><img-->
<!--            src="/sessionweb/include/filemanagement/thumbnails/Water%20lilies.jpg"></a></td>-->
<!--    <td class="name"><a href="/sessionweb/include/filemanagement/files/Water%20lilies.jpg" target="_blank">Water-->
<!--        lilies.jpg</a></td>-->
<!--    <td class="size">83.79 KB</td>-->
<!--    <td colspan="2"></td>-->
<!--    <td class="delete">-->
<!--        <button data-type="DELETE"-->
<!--                data-url="/sessionweb/include/filemanagement/upload.php?file=Water%20lilies.jpg"-->
<!--                class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button"-->
<!--                aria-disabled="false" title="Delete"><span-->
<!--                class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete</span>-->
<!--        </button>-->
<!--    </td>-->
<!--</tr>-->
            </tbody>
        </table>
        <div class="fileupload-progressbar ui-progressbar ui-widget ui-widget-content ui-corner-all"
             style="display: none; " role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="ui-progressbar-value ui-widget-header ui-corner-left" style="display: none; width: 0%; "></div>
        </div>
    </div>
</div>
<script id="template-upload" type="text/x-jquery-tmpl">
    <tr class="template-upload{{if error}} ui-state-error{{/if}}">
        <td class="preview"></td>
        <td class="name">{{if name}}${name}{{else}}Untitled{{/if}}</td>
        <td class="size">${sizef}</td>
        {{if error}}
        <td class="error" colspan="2">Error:
            {{if error === 'maxFileSize'}}File is too big
            {{else error === 'minFileSize'}}File is too small
            {{else error === 'acceptFileTypes'}}Filetype not allowed
            {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
            {{else}}${error}
            {{/if}}
        </td>
        {{else}}
        <td class="progress">
            <div></div>
        </td>
        <td class="start">
            <button>Start</button>
        </td>
        {{/if}}
        <td class="cancel">
            <button>Cancel</button>
        </td>
    </tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
    <tr class="template-download{{if error}} ui-state-error{{/if}}">
        {{if error}}
        <td></td>
        <td class="name">${name}</td>
        <td class="size">${sizef}</td>
        <td class="error" colspan="2">Error:
            {{if error === 1}}File exceeds upload_max_filesize (php.ini directive)
            {{else error === 2}}File exceeds MAX_FILE_SIZE (HTML form directive)
            {{else error === 3}}File was only partially uploaded
            {{else error === 4}}No File was uploaded
            {{else error === 5}}Missing a temporary folder
            {{else error === 6}}Failed to write file to disk
            {{else error === 7}}File upload stopped by extension
            {{else error === 'maxFileSize'}}File is too big
            {{else error === 'minFileSize'}}File is too small
            {{else error === 'acceptFileTypes'}}Filetype not allowed
            {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
            {{else error === 'uploadedBytes'}}Uploaded bytes exceed file size
            {{else error === 'emptyResult'}}Empty file upload result
            {{else}}${error}
            {{/if}}
        </td>
        {{else}}
        <td class="preview">
            {{if thumbnail_url}}
            <a href="${url}" target="_blank"><img src="${thumbnail_url}"></a>
            {{/if}}
        </td>
        <td class="name">
            <a href="${url}"{{if thumbnail_url}} target="_blank"{{/if}}>${name}</a>
        </td>
        <td class="size">${sizef}</td>
        <td colspan="2"></td>
        {{/if}}
        <td class="delete">
            <button data-type="${delete_type}" data-url="${delete_url}">Delete</button>
        </td>
    </tr>
</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
<script src="../../js/jQuery-File-Upload/jquery.iframe-transport.js"></script>
<script src="../../js/jQuery-File-Upload/jquery.fileupload.js"></script>
<script src="../../js/jQuery-File-Upload/jquery.fileupload-ui.js"></script>
<script src="application.js"></script>

</body>
</html>