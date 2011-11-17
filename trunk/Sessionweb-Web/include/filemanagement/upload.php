<?php
/*
 * jQuery File Upload Plugin PHP Example 5.2.9
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*
 * TO make sure that MYSQL will work with files up to 16mb execute:
 * SHOW GLOBAL VARIABLES LIKE 'max_allowed_packet';
 * SET GLOBAL max_allowed_packet=1024*1024*16;
 * Change 16 to the nbr of mb you would like to use as max upload.
 * Ref: http://www.codingforums.com/archive/index.php/t-122544.html
 *
 * include\filemanagement\files
 * include\filemanagement\thumbnails
*/
require_once('../../include/loggingsetup.php');
include_once("../../include/loggedincheck.php");

//error_reporting(E_ALL | E_STRICT);

class UploadHandler
{
    private $options;

    const MAX_FILE_SIZE = 5248000;

    function __construct($options = null)
    {
        $this->options = array(
            'script_url' => $_SERVER['PHP_SELF'],
            'upload_dir' => dirname(__FILE__) . '/files/',
            'download_base' => dirname($_SERVER['PHP_SELF']) . '/',
            'upload_url' => dirname($_SERVER['PHP_SELF']) . '/files/',
            'param_name' => 'files',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            'max_number_of_files' => null,
            'discard_aborted_uploads' => true,
            'image_versions' => array(
                // Uncomment the following version to restrict the size of
                // uploaded images. You can also add additional versions with
                // their own upload directories:
                /*
                'large' => array(
                    'upload_dir' => dirname(__FILE__).'/files/',
                    'upload_url' => dirname($_SERVER['PHP_SELF']).'/files/',
                    'max_width' => 1920,
                    'max_height' => 1200
                ),
                */
                'thumbnail' => array(
                    'upload_dir' => dirname(__FILE__) . '/thumbnails/',
                    'upload_url' => dirname($_SERVER['PHP_SELF']) . '/thumbnails/',
                    'max_width' => 80,
                    'max_height' => 80
                )
            )
        );
        if ($options) {
            $this->options = array_replace_recursive($this->options, $options);
        }
    }

//    private function get_file_object($file_name)
//    {
//
//        //        include "../../config/db.php.inc";
//        //        $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
//        //        mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
//        //        $sql = "SELECT `id`, `mission_versionid`, `filename`, `size`, `data` FROM `mission_attachments` WHERE `id` = ". $_REQUEST['sessionid'];
//        //        $result = mysql_query($sql) or die('Error, query failed');
//        //
//        //        list($id, $mission_versionid, $filename, $size, $content) =  mysql_fetch_array($result);
//        //
//        //        mysql_close();
//        //
//        //        $file_path = $this->options['upload_dir'] . $file_name;
//        //        //if (is_file($file_path) && $file_name[0] !== '.') {
//        //        if(true)
//        //        {
//        //            $file = new stdClass();
//        //            //$file->name = $file_name;
//        //            //$file->size = filesize($file_path);
//        //            $file->name = $filename;
//        //            $file->size = $size;
//        //            $file->url = $this->options['upload_url'] . rawurlencode($file->name)."TJOHO";
//        //            foreach ($this->options['image_versions'] as $version => $options) {
//        //               // if (is_file($options['upload_dir'] . $file_name)) {
//        //                    $file->{$version . '_url'} = $options['upload_url']
//        //                                                 . rawurlencode($file->name)."TJOHO2";
//        //                //}
//        //            }
//        //            $file->delete_url = $this->options['script_url']
//        //                                . '?file=' . rawurlencode($file->name);
//        //            $file->delete_type = 'DELETE';
//        //            return $file;
//        //        }
//        //        return null;
//    }
//
//    private function get_file_objects()
//    {
//
//        //        return array_values(array_filter(array_map(
//        //                                             array($this, 'get_file_object'),
//        //                                             scandir($this->options['upload_dir'])
//        //                                         )));
//    }

    private function create_scaled_image($file_name, $options)
    {
        $file_path = $this->options['upload_dir'] . $file_name;
        $new_file_path = $options['upload_dir'] . $file_name;
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        if ($scale > 1) {
            $scale = 1;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                break;
            default:
                $src_img = $image_method = null;
        }
        $success = $src_img && @imagecopyresampled(
                                    $new_img,
                                    $src_img,
                                    0, 0, 0, 0,
                                    $new_width,
                                    $new_height,
                                    $img_width,
                                    $img_height
                                ) && $write_image($new_img, $new_file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }

    private function has_error($uploaded_file, $file, $error)
    {
        if ($error) {
            return $error;
        }
        if (!preg_match($this->options['accept_file_types'], $file->name)) {
            return 'acceptFileTypes';
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = filesize($uploaded_file);
        } else {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }
        if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
        ) {
            return 'maxFileSize';
        }
        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size']) {
            return 'minFileSize';
        }
        if (is_int($this->options['max_number_of_files']) && (
                count($this->get_file_objects()) >= $this->options['max_number_of_files'])
        ) {
            return 'maxNumberOfFiles';
        }
        return $error;
    }

    private function trim_file_name($name, $type)
    {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
        // Add missing file extension for known image types:
        if (strpos($file_name, '.') === false &&
            preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $file_name .= '.' . $matches[1];
        }
        return $file_name;
    }


    private function handle_file_upload($uploaded_file, $name, $size, $type, $error, $logger)
    {
        $logger->debug('File trying to be uploaded:' . $name);
        $logger->debug('Temp file name:' . $uploaded_file);
        $logger->debug('Size:' . $size);
        $logger->debug('Type:' . $type);
        $file = new stdClass();
        $file->name = $this->trim_file_name($name, $type);
        $file->size = intval($size);
        $file->type = $type;
        $error = $this->has_error($uploaded_file, $file, $error);
        if ($file->size > self::MAX_FILE_SIZE) {
            $logger->debug($name . ' is to large. Max size:' . self::MAX_FILE_SIZE . ' File size:' . $file->size);

            $file->error = 'File to large. File size limit is ' . number_format(self::MAX_FILE_SIZE / 1024 / 1024, 2) . ' mb';
        }
        else
        {
            if (!$error && $file->name) {
                $file_path = $this->options['upload_dir'] . $file->name;
                $logger->debug('File_path:'.$file_path);
                $append_file = !$this->options['discard_aborted_uploads'] &&
                               is_file($file_path) && $file->size > filesize($file_path);
                clearstatcache();
                if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                    // multipart/formdata uploads (POST method uploads)
                    if ($append_file) {
                        file_put_contents(
                            $file_path,
                            fopen($uploaded_file, 'r'),
                            FILE_APPEND
                        );
                    } else {
                        $logger->debug('Will try to upload file to database');
                        $file->id = $this->uploadToDatabase($uploaded_file, $file, $logger);
                        move_uploaded_file($uploaded_file, $file_path);
                    }
                } else {
                    // Non-multipart uploads (PUT method support)
                    file_put_contents(
                        $file_path,
                        fopen('php://input', 'r'),
                        $append_file ? FILE_APPEND : 0
                    );
                }

                $logger->debug('File_path later:'.$file_path);
                $logger->debug('FileSize later:'.filesize($file_path));
                $file_size = filesize($file_path);

                if ($file_size === $file->size) {
                    $file->url = $this->options['download_base'] . "get.php?id=" . $file->id;
                    foreach ($this->options['image_versions'] as $version => $options) {
                        if ($this->create_scaled_image($file->name, $options)) {
                            $file->{$version . '_url'} = $options['upload_url']
                                                         . rawurlencode($file->name);
                        }
                    }
                } else if ($this->options['discard_aborted_uploads']) {
                    $logger->error('File size issue: ' . $file_size . " vs " . $file->size);
                    unlink($file_path);
                    $file->error = 'abort';
                }
                $file->size = $file_size;
                $file->delete_url = $this->options['download_base'] . "delete.php?id=" . $file->id;
                $file->delete_type = 'DELETE';
            } else {
                $logger->error($name . ': Other error');
                $file->error = $error;
            }
            unlink($file_path);
        }
        return $file;
    }

    private function uploadToDatabase($uploaded_file, $file, $logger)
    {
        try {


            $logger->debug('Will open file to be read into memory');
            $fp = fopen($uploaded_file, 'r');
            $content = fread($fp, filesize($uploaded_file));
            $logger->debug('File loaded into memory');

            include "../../config/db.php.inc";


            $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
            mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

            $content = addslashes($content);
            $file->name = addslashes($file->name);

            $sql = "INSERT INTO mission_attachments (mission_versionid, filename, mimetype, size, data ) " .
                   "VALUES (" . $_REQUEST['sessionid'] . ", '$file->name', '$file->type', '$file->size', '$content')";

            $sqlDebug = "INSERT INTO mission_attachments (mission_versionid, filename, mimetype, size, data ) " .
                        "VALUES (" . $_REQUEST['sessionid'] . ", '$file->name', '$file->type', '$file->size', '........')";

            $result = mysql_query($sql);

            if (!$result) {
                $logger->error($file->name . ' ' . mysql_error());
                $logger->debug($sqlDebug);
                //echo "upload_attachment: " . mysql_error() . "<br/>";

                //echo $sql;
            }

            $sqlFindLatestId = "SELECT id FROM `mission_attachments` ORDER BY `id` DESC LIMIT 0,1";
            $logger->debug($sqlFindLatestId);
            $result2 = mysql_query($sqlFindLatestId);
            $row = mysql_fetch_row($result2);
            //while ($row = mysql_fetch_array($result2, MYSQL_NUM)) {
            //$row = mysql_fetch_row($result2);
            //   print_r($row);
            $id = $row[0];
            //}
            $logger->debug('File id in database: ' . $id);
            mysql_close($con);
            return $id;
        }
        catch (Exception $e) {
            $logger->error($e->getMessage());
        }
    }

//    public function get()
//    {
//        $file_name = isset($_REQUEST['file']) ?
//                basename(stripslashes($_REQUEST['file'])) : null;
//        if ($file_name) {
//            $info = $this->get_file_object($file_name);
//        } else {
//            $info = $this->get_file_objects();
//        }
//        header('Content-type: application/json');
//        echo json_encode($info);
//    }

    public function post($logger)
    {
        $upload = isset($_FILES[$this->options['param_name']]) ?
                $_FILES[$this->options['param_name']] : null;
        $info = array();
        if ($upload && is_array($upload['tmp_name'])) {
            $logger->debug('Uploaded file is in an array... will try to loop through all of them');
            foreach ($upload['tmp_name'] as $index => $value) {
                $info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    isset($_SERVER['HTTP_X_FILE_NAME']) ?
                            $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                    isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                            $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                    isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                            $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                    $upload['error'][$index], $logger
                );
            }
        } elseif ($upload) {
            $logger->debug('Singel file: ' . $upload['tmp_name']);
            $info[] = $this->handle_file_upload(
                $upload['tmp_name'],
                isset($_SERVER['HTTP_X_FILE_NAME']) ?
                        $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'],
                isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                        $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'],
                isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                        $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'],
                $upload['error'], $logger
            );
        }
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo json_encode($info);
    }

//    public function delete()
//    {
//        $file_name = isset($_REQUEST['file']) ?
//                basename(stripslashes($_REQUEST['file'])) : null;
//        $file_path = $this->options['upload_dir'] . $file_name;
//        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
//        if ($success) {
//            foreach ($this->options['image_versions'] as $version => $options) {
//                $file = $options['upload_dir'] . $file_name;
//                if (is_file($file)) {
//                    unlink($file);
//                }
//            }
//        }
//        header('Content-type: application/json');
//        echo json_encode($success);
//    }
}

$upload_handler = new UploadHandler();

header('Pragma: no-cache');
header('Cache-Control: private, no-cache');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        $logger->debug('Is a POST file upload');
        $upload_handler->post($logger);
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    case 'OPTIONS':
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
}
?>