<?php

$rootPath = checkIfRootFolder("");
require_once ($rootPath . '/classes/logging.php');
require_once ($rootPath . '/include/commonFunctions.php.inc');

/**
 * Class to help out with common mysql tasks
 */
class WisemappingManager
{
    private $logger;

    private $URL_LOGIN = "/c/j_spring_security_check";
    private $URL_LIST_MAPS = "/c/restful/maps/";
    private $URL_CREATE_MAP = "/c/restful/maps/";


    private $wisemapping;
    private $wisemapping_url;
    private $wisemapping_user;
    private $wisemapping_password;
    private $jsessionid;

    function __construct()
    {
        $this->logger = new logging();
        if (!in_array('curl', get_loaded_extensions())) {
            $this->logger->fatal("PHP_curl is not install, can not interact wiht Mindmaster", __FILE__, __LINE__);
            echo "FATAL: Checl logs for more information";
            die();
        }

        $this->wisemapping = $_SESSION['settings']['wisemapping'];
        $this->wisemapping_url = $_SESSION['settings']['wisemapping_url'];
        $this->wisemapping_user = $_SESSION['settings']['wisemapping_user'];
        $this->wisemapping_password = $_SESSION['settings']['wisemapping_password'];
    }

    /**
     * Execute an api call to Wisemapping rest api
     * @param $url Full Wisemapping api url
     * @param $jsessionid cookiedata excluded 'JSESSIONID=' Eg. only 1m1bm3xf7vsyscrdsn8f0qdls
     * @return mixed Output from the request
     */
    private function getApiCall($url, $jsessionid = null)
    {
        $id = rand(0, 100000);


        $this->logger->info('(' . $id . ')Executing GET: ' . $url, __FILE__, __LINE__);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_URL, $url);


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($jsessionid != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cookie: JSESSIONID=' . $jsessionid));
        }

        $output = curl_exec($ch);


        $info = curl_getinfo($ch);
        if (isset($info['http_code']))
            $this->logger->debug('(' . $id . ') HTTP Status code: ' . $info['http_code'], __FILE__, __LINE__);
        $this->logger->debug('(' . $id . ') Response:  ' . $output, __FILE__, __LINE__);

        $this->logger->debug(print_r($info, true), __FILE__, __LINE__);

        curl_close($ch);

        $this->logger->info('(' . $id . ') Api request finished: ' . $url, __FILE__, __LINE__);

        return $output;
    }


    private function postApiCall($url, $jsessionid = null, $postData, $returnHeader = false, $addJsonContentType = false)
    {
        $this->logger = new logging();
        $id = rand(0, 100000);
        $this->logger->info('(' . $id . ') Executing POST: ' . $url, __FILE__, __LINE__);
        $this->logger->info('(' . $id . ') Request data: ' . $postData, __FILE__, __LINE__);

        $ch = curl_init($url);
        if ($returnHeader) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }

        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

//        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);


        curl_setopt($ch, CURLOPT_POSTFIELDS, "$postData");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = array();
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31';

        if ($jsessionid != null) {
            $this->logger->debug('Setting Cookie: JSESSIONID=' . $jsessionid);
            $headers[] = 'Cookie: JSESSIONID=' . $jsessionid;
        }

        if ($addJsonContentType != null) {
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
            $headers[] = 'X-Requested-With: XMLHttpRequest';
            $headers[] = 'Referer: ' . $this->wisemapping_url . '/c/maps/';
            $headers[] = 'Accept-Language: sv-SE,sv;q=0.8,en-US;q=0.6,en;q=0.4';
            $headers[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3';
        }

        if (sizeof($headers) != 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $output = curl_exec($ch);

        $info = curl_getinfo($ch);
        if (isset($info['http_code']))
            $this->logger->debug('(' . $id . ') HTTP Status code: ' . $info['http_code'], __FILE__, __LINE__);
        $this->logger->debug(print_r($info, true), __FILE__, __LINE__);
        $responseData[] = array();
        $responseData['data'] = $output;
        $responseData['metadata'] = $info;

        curl_close($ch);

        return $responseData;
    }

    private function login()
    {
        if (isset($_SESSION['settings']['jsessionid'])) {
            $this->logger->debug("jsessionid already exist, will reuse it. jsessionid: " . $_SESSION['settings']['jsessionid'], __FILE__, __LINE__);
            return;
        } else {

            $url = $this->wisemapping_url . $this->URL_LOGIN;
            $postData = "j_username=" . $this->wisemapping_user . "&j_password=" . $this->wisemapping_password;
            $responseData = $this->postApiCall($url, null, $postData, true);

            $response = $responseData['data'];
            $metadata = $responseData['metadata'];

            $re1 = '.*?'; # Non-greedy match on filler
            $re2 = '='; # Any Single Character 1
            $re3 = '(.*?)'; # Non-greedy match on filler
            $re4 = ';'; # Alphanum 1

            if ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4 . "/is", $response, $matches)) {
                $this->jsessionid = $matches[1][0];
                $_SESSION['settings']['jsessionid'] = $this->jsessionid;
                $this->logger->debug($response, __FILE__, __LINE__);
                $this->logger->debug("New jsessionid:" . $this->jsessionid, __FILE__, __LINE__);
            } else {
                $this->logger->error("Could not log into wisemapping", __FILE__, __LINE__);
            }
        }

    }

    /**
     * List ALL MindMaps
     * @return mixed Json with meta data of mindmaps
     */
    public function listMaps()
    {
        $this->login();
        $url = $this->wisemapping_url . $this->URL_LIST_MAPS;
        $this->login();
        return $this->getApiCall($url, $this->jsessionid);
    }

    /**
     * Creates a new MinMap
     * @param $title
     * @param $description
     * @return int id of mindmap
     */
    public function createMap($title, $description)
    {
        unset($_SESSION['settings']['jsessionid']);
        if (!isset($_SESSION['settings']['jsessionid'])) {
            $this->login();
        }

        $url = $this->wisemapping_url . $this->URL_CREATE_MAP;
        $postData['title'] = $title;
        $postData['description'] = $description;

        $responseData = $this->postApiCall($url, $_SESSION['settings']['jsessionid'], json_encode($postData), true, true);
        $response = $responseData['data'];
        $metadata = $responseData['metadata'];
        if ($metadata['http_code'] == 201) {
            $this->logger->info("MindMap created! MindMap title: " . $title, __FILE__, __LINE__);
        } else {
            $this->logger->error("MindMap not created! MindMap title: " . $title, __FILE__, __LINE__);
            return false;
        }
        $part1OfFindingId = explode("ResourceId: ", $response);
        $part2OfFindingId = explode("Content-Length:", $part1OfFindingId[1]);
        $mapId = $part2OfFindingId[0];
        $mapId = intval($mapId);

        $responseData['url'] = $_SESSION['settings']['wisemapping_url'].'/c/maps/'.$mapId.'/edit';
        $responseData['mapid']=$mapId;
        return $responseData;
    }

}

?>