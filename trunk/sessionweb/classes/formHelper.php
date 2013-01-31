<?php
require_once 'logging.php';
require_once 'dbHelper.php';
require_once 'queryHelper.php';
require_once 'sessionObject.php';
class formHelper
{
    private $logger;
    private $queryHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->queryHelper = new queryHelper();
    }

    /**
     * Help function that create a html
     * <select>
     *   <option>n items</option>
     * </select>
     * @param $selectArray Array to create a html selection code
     * @param int $selectedIndex option index to select
     * @param bool $includeEmptyRow first row empty => true, no empty row=> false
     * @param $htmlTagId html id tag
     * @param $htmlTagName html name tab
     * @param string $extraParameters extra paramterts to include in <SELECT> tag
     * @return string html of a <select>
     */
    private function formSelectListSingle($selectArray, $selectedName = "", $includeEmptyRow = true, $htmlTagId, $htmlTagName, $extraParameters = "")
    {
        echo $selectedName;
        $index = 0;
        $html = "<select id=\"$htmlTagId\" name=\"$htmlTagName\" $extraParameters>\n";
        if ($includeEmptyRow) {
            if (strlen($selectedName) == 0) { //$selectedIndex == $index) {
                $html .= "<option selected='selected'></option>\n";
                $index++;
            } else {
                $html .= "<option></option>\n";
                $index++;
            }

        }
        foreach ($selectArray as $key => $option) {
            if (strstr($selectedName, $option) != false) { //$selectedIndex == $index) {
                $html .= "<option value='$key' selected='selected'>" . htmlspecialchars($option) . "</option>\n";
            } else {
                $html .= "<option value='$key'>" . htmlspecialchars($option) . "</option>\n";
            }
            $index++;
        }
        $html .= "</select>";

        return $html;
    }

    private function formSelectListMultiple($selectArray, $selectedName = array(), $includeEmptyRow = true, $htmlTagId, $htmlTagName, $extraParameters = "")
    {

        $index = 0;
        $html = "<select id=\"$htmlTagId\" name=\"" . $htmlTagName . "[]\" multiple=\"multiple\" $extraParameters>\n";
        if ($includeEmptyRow) {
            if (count($selectedName) == 0) { //$selectedIndex == $index) {
                $html .= "<option selected='selected'></option>\n";
                $index++;
            } else {
                $html .= "<option></option>\n";
                $index++;
            }

        }
        foreach ($selectArray as $key => $option) {
            if(array_key_exists($key,$selectedName))
            {
                $html .= "<option value='$key' selected='selected'>" . htmlspecialchars($option) . "</option>\n";
            }
            else
            {
                $html .= "<option value='$key'>" . htmlspecialchars($option) . "</option>\n";
            }
//            foreach ($selectedName as $toSelectKey => $toSelectValue)
//            {
//                if (strstr($toSelectValue, $option) != false) { //$selectedIndex == $index) {
//                    $html .= "<option value='$key' selected='selected'>" . htmlspecialchars($option) . "</option>\n";
//                } else {
//                    $html .= "<option value='$key'>" . htmlspecialchars($option) . "</option>\n";
//                }
//            }
            $index++;
        }
        $html .= "</select>";

        return $html;
    }

    public function getTeamSelect($selectedName = "")
    {
        $teamArray = $this->queryHelper->getTeamNamesActive();
        return $this->formSelectListSingle($teamArray, $selectedName, true, "idTeam", "nameTeam");
    }

    public function getAreaSelect($selectedName = array())
    {
        $teamArray = $this->queryHelper->getAreasActive();
        return $this->formSelectListMultiple($teamArray, $selectedName, true, "idArea", "nameArea");
    }

    public function AdditionalTester($selectedNames = array())
    {
        $teamArray = $this->queryHelper->getAdditionalTester();
        return $this->formSelectListMultiple($teamArray, $selectedNames, true, "idAdditionalTester", "nameAdditionalTester");
    }

    public function getSprintSelect($selectedName = "")
    {
        $sprintArray = $this->queryHelper->getSprintNamesActive();
        return $this->formSelectListSingle($sprintArray, $selectedName, true, "idSprint", "nameSprint");
    }

    public function getEnvironmentSelect($selectedName = "")
    {
        $environmentArray = $this->queryHelper->getEnvironmentsNames();
        return $this->formSelectListSingle($environmentArray, $selectedName, true, "idEnvironment", "nameEnvironment");
    }

    private function checkIfRootFolder($pathToRoot)
    {
        if (file_exists($pathToRoot . 'about.php')) {
            //echo "Found root at " . $pathToRoot . "about.php<br>";
            return "./" . $pathToRoot;
        } else {
            //echo "Not Root<br>";
            $pathToRoot .= "../";
            $pathToRoot = checkIfRootFolder($pathToRoot);
        }
        return $pathToRoot;
    }

}

?>