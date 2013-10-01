<?php

class PathHelper
{

    function __construct()
    {
    }

    public static function getRootPath($pathToRoot)
    {
        if (file_exists($pathToRoot . 'about.php')) {
            //echo "Found root at " . $pathToRoot . "about.php<br>";
            return "./" . $pathToRoot;
        } else {
            //echo "Not Root<br>";
            $pathToRoot .= "../";
            $pathToRoot = PathHelper::getRootPath($pathToRoot);
        }
        return $pathToRoot;
    }
}

?>