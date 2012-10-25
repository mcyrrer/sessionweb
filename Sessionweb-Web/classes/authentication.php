<?php

require_once 'config/auth.php.inc';
require_once 'classes/logging.php';
class authentication
{
    function getUserInfoThroughLdap($LDAPUser, $LDAPUserPassword)
    {
        $logger = new logging();
        error_reporting(E_NOTICE); //Suppress some unnecessary messages

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

        $ldapConnection = ldap_connect("ldaphost.com") or die("Can not connect to ldap server");

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

}
