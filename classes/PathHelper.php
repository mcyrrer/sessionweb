<?php

class PathHelper
{

    function __construct()
    {
    }

    /**
     * @param $pathToRoot
     * @return string
     * @deprecated
     */
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

    /**
     * Get the base server path for sessionweb
     * @return mixed
     */
    public static function getRootPath_v2()
    {
        return PathHelper::getRootPath_v2_recursive(dirname(__FILE__));
    }

    private static function getRootPath_v2_recursive($path)
    {
        if (file_exists($path . '/about.php')) {
            return  $path;
        } else {
            return PathHelper::getRootPath_v2_recursive(dirname($path));
        }
    }

}

?>