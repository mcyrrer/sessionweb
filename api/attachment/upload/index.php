<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');


/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(0);
require('UploadHandler.php');
$upload_handler = new UploadHandler();
