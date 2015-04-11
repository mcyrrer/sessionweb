<?php
require_once 'PathHelper.php';
$rootPath = PathHelper::getRootPath("");
require_once $rootPath . '/config/auth.php.inc';
require_once $rootPath . '/classes/logging.php';

class authentication
{
    function getUserInfoThroughLdap($LDAPUser, $LDAPUserPassword)
    {
        $logger = new logging();
//        error_reporting(E_NOTICE); //Suppress some unnecessary messages

        $SearchFor = $LDAPUser;
        $LDAPFieldsToFind = array("cn", "givenname", "samaccountname");

        if (!LDAP_ENABLED) {
            return "LDAP_NOT_ENABLED";
        }

        //Check if password length = 0 , some LDAP/AD may bind if password is empty.
        if (strlen($LDAPUserPassword) == 0) {
            $logger->debug("Empty AD password for user $LDAPUser", __FILE__, __LINE__);
            return "EMPTY_PASSWORD";
        }

        $ldapConnection = ldap_connect(LDAP_HOST) or die("Can not connect to ldap server");

        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3); //Set the LDAP Protocol used by your AD service
        ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0); //This was necessary for my AD to do anything

        $bindres = ldap_bind($ldapConnection, $LDAPUser . LDAP_USER_DOMAIN, $LDAPUserPassword);

        if ($bindres == false) {
            $logger->warning("Could Not Bind To AD", __FILE__, __LINE__);
            return "COULD_NOT_BIND";

        }

        $filter = "(" . LDAP_SEARCH_FIELD . "=$SearchFor*)"; //Wildcard is * Remove it if you want an exact match
        $sr = ldap_search($ldapConnection, LDAP_DN, $filter, $LDAPFieldsToFind);
        $info = ldap_get_entries($ldapConnection, $sr);

        for ($x = 0; $x < $info["count"]; $x++) {
            $sam = $info[$x]['samaccountname'][0];
            $giv = $info[$x]['givenname'][0];
            $nam = $info[$x]['cn'][0];

            if (stristr($sam, "$SearchFor")) {
                $userInfo["name"] = $nam;
                $userInfo["samaccountname"] = $sam;
                return $userInfo;
            }
        }
        if ($x == 0) {
            $logger->debug("User not found, username was $LDAPUser", __FILE__, __LINE__);
            return "USER_NOT_FOUND";
        }

    }

    /**
     * Function to get information if a user is a AD user of a local sessionweb user based on the information
     * in the db table members
     * @param $username
     * @param $mysqlCon
     * @return int 1 if ad account, 0 if not, 2 if user does not exist in sessionweb db
     */
    function isUserLdapUser($username, $mysqliCon)
    {
        $logger = new logging();

        $sql = "SELECT * FROM members WHERE username = '$username'";
        $logger->sql($sql, __FILE__, __LINE__);
        $result = mysqli_query($mysqliCon,$sql);
        echo mysqli_error($mysqliCon);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_array($result);
            if ($row['adaccount'] == false) {
                return 0;
            } else {
                return 1;
            }
        } elseif (mysqli_num_rows($mysqliCon,$result) == 0) {

            return 2;
        } else {
            $logger->warning("More then one user have username $username", __FILE__, __LINE__);
            return 3;
        }
    }

}
