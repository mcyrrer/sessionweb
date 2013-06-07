package com.sessionweb.autotest.tests;

import com.sessionweb.autotest.SessionWebTest;
import org.testng.annotations.Test;

import static org.testng.Assert.*;

public class List extends SessionWebTest {
    @Test
    public void multiplePages() throws Exception {
        cs.cleanDb();
        cs.logIn(selenium);

        selenium.click("url_list");
        selenium.waitForPageToLoad("30000");
        assertFalse(selenium.isElementPresent("prev_page"));
        assertFalse(selenium.isElementPresent("next_page"));
        for (int j = 0; j < 40; j++) {
            cs.createSession(selenium);
        }
        selenium.click("url_list");
        selenium.waitForPageToLoad("30000");
        assertFalse(selenium.isElementPresent("prev_page"));
        assertTrue(selenium.isElementPresent("next_page"));
        selenium.click("link=Next page");
        selenium.waitForPageToLoad("30000");
        assertTrue(selenium.isElementPresent("prev_page"));
        assertFalse(selenium.isElementPresent("next_page"));
        cs.logOut(selenium);
    }

    @Test
    public void deleteSessionFromList() throws Exception {
        cs.cleanDb();
        cs.logIn(selenium);
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.type("input_title", "deleteme");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        String sessionIdToDelete = selenium.getText("sessionid");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");
        selenium.click("delete_session" + sessionIdToDelete);
        assertTrue(selenium.getConfirmation().matches("^Delete session from database[\\s\\S]$"));
        selenium.waitForPageToLoad("15000");
        assertTrue(selenium.isTextPresent("Session " + sessionIdToDelete + " deleted from database"));
        cs.logOut(selenium);
    }

    @Test
    public void shareSessionFromList() throws Exception {
        cs.cleanDb();
        cs.logIn(selenium);
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.type("input_title", "ShareMe");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        String sessionIdToView = selenium.getText("sessionid");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");
        selenium.click("publicview_session" + sessionIdToView);
        cs.waitForText(selenium, "Session title");
        assertTrue(selenium.isTextPresent(sessionIdToView));
        cs.logInAsTestUserThroughUrl(selenium);
    }

    @Test
    public void reassignSessionFromList() throws Exception {
        cs.cleanDb();
        cs.logIn(selenium);

        selenium.click("url_settings");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_adduser");
        selenium.waitForPageToLoad("15000");
        selenium.type("fullname", "test");
        selenium.type("username", "test");
        selenium.type("swpassword1", "test");
        selenium.click("//input[@value='Add']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.click("input_title");
        selenium.type("input_title", "ChangeUserSession");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        String sessionToReassign = selenium.getText("sessionid");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");
        selenium.click("reassign_session" + sessionToReassign);
        selenium.waitForPageToLoad("15000");
        selenium.select("select_tester", "label=test");
        assertTrue(selenium.isTextPresent(sessionToReassign));
        selenium.click("//input[@value='Continue']");
        selenium.waitForPageToLoad("15000");
        assertTrue(selenium.isTextPresent("Session reassigned."));
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");
        assertTrue(selenium.isElementPresent("tablerowuser_" + sessionToReassign));

        cs.logOut(selenium);
    }

    @Test
    public void copySession() throws Exception {
        cs.cleanDb();
        cs.logIn(selenium);

        selenium.click("url_settings");
        selenium.waitForPageToLoad("15000");
        selenium.click("link=Add team");
        selenium.waitForPageToLoad("15000");
        selenium.type("teamtname", "t_test1");
        selenium.click("//input[@value='Add team']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addenv");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='envname']", "e_env1");
        selenium.click("//input[@value='Add environment']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='sprintname']", "s_sprname");
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addarea");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='areaname']", "aarreeaa11");
        selenium.click("//input[@value='Add area']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addarea");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='areaname']", "aarreeaa12");
        selenium.click("//input[@value='Add area']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addteamsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='teamsprintname']", "ttteemmsspprrinnt1");
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.type("input_title", "testtitle1");
        selenium.select("select_team", "label=t_test1");
        selenium.select("select_sprint", "label=s_sprname");
        selenium.select("select_teamsprint", "label=ttteemmsspprrinnt1");
        selenium.addSelection("select_area", "label=aarreeaa11");
        selenium.addSelection("select_area", "label=aarreeaa12");
        selenium.select("select_testenv", "label=e_env1");

        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        String sessionIdToView = selenium.getText("sessionid");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");


        assertTrue(selenium.isTextPresent("testtitle1"));

        selenium.open("/sessionweb/list.php");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");
        selenium.click("//img[@alt='Copy session']");
        selenium.waitForPageToLoad("15000");
        String copySessionId = selenium.getText("copySessionid");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");


        selenium.click("view_session" + copySessionId);
        cs.waitForText(selenium, "Session title");
        assertTrue(selenium.isTextPresent("testtitle1(COPY)"));
        assertTrue(selenium.isTextPresent("t_test1"));
        assertTrue(selenium.isTextPresent("s_sprname"));
        assertTrue(selenium.isTextPresent("ttteemmsspprrinnt1"));
        assertTrue(selenium.isTextPresent("e_env1"));


        cs.logOut(selenium);
    }
}
