package com.sessionweb.autotest.tests;

import com.sessionweb.autotest.CommonSteps;
import com.sessionweb.autotest.SessionWebTest;
import com.thoughtworks.selenium.*;
import org.testng.annotations.*;

import static org.testng.Assert.*;

import java.util.regex.Pattern;

public class Session extends SessionWebTest {
    CommonSteps cs = new CommonSteps();

    @Test
    public void basicSession() throws Exception {
            cs.cleanDb();

            cs.logIn(selenium);

            selenium.click("url_settings");
            selenium.waitForPageToLoad("15000");
            selenium.click("link=Add team");
            selenium.waitForPageToLoad("15000");
            selenium.type("teamtname", "testteam1");
            selenium.click("//input[@value='Add team']");
            selenium.waitForPageToLoad("15000");
            selenium.click("url_addsprint");
            selenium.waitForPageToLoad("15000");
            selenium.type("//input[@name='sprintname']", "testsprint1");
            selenium.click("//input[@value='Add name']");
            selenium.waitForPageToLoad("15000");
            selenium.click("url_addarea");
            selenium.waitForPageToLoad("15000");
            selenium.type("//input[@name='areaname']", "testarea1");
            selenium.click("//input[@value='Add area']");
            selenium.waitForPageToLoad("15000");
            selenium.click("url_addteamsprint");
            selenium.waitForPageToLoad("15000");
            selenium.type("//input[@name='teamsprintname']", "testteamsprint1");
            selenium.click("//input[@value='Add name']");
            selenium.waitForPageToLoad("15000");
            selenium.click("url_newsession");
            selenium.waitForPageToLoad("15000");

            selenium.type("input_title", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer elit turpis, adipiscing imperdiet ultrices sed,");
            selenium.select("select_team", "label=testteam1");
            selenium.select("select_sprint", "label=testsprint1");
            selenium.select("select_teamsprint", "label=testteamsprint1");
            selenium.addSelection("select_area", "label=testarea1");
            selenium.type("requirement", "12");
            selenium.click("add_requirement");
            selenium.type("bug", "12");
            selenium.click("add_bug");
            selenium.select("setuppercent", "label=30");
            selenium.select("testpercent", "label=35");
            selenium.select("bugpercent", "label=35");
            selenium.select("duration", "label=180");
            assertTrue(selenium.isTextPresent("100%"),"100% visible");
            selenium.click("executed");
            selenium.click("input_submit");
            selenium.waitForPageToLoad("15000");
            selenium.click("view_session");

            cs.waitForText(selenium, "Session title");

            assertTrue(selenium.isTextPresent("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer elit turpis, adipiscing imperdiet ultrices sed"));
            assertTrue(selenium.isTextPresent("testteam1"));
            assertTrue(selenium.isTextPresent("testsprint1"));
            assertTrue(selenium.isTextPresent("testteamsprint1"));
            assertEquals(selenium.getTable("//td[2]/table.0.6"), "Status Executed");
            assertEquals(selenium.getTable("//td[2]/table.0.7"), "Debriefed Not debriefed");
            assertTrue(selenium.isTextPresent("Requirements connected to session #12:Link to requirement"), "is req visible on view page");
            assertTrue(selenium.isTextPresent("#12:12"));
            assertTrue(selenium.isTextPresent("Sessions duration 180 (min)"));
            assertTrue(selenium.isTextPresent("Opportunity 0 %"));
            assertTrue(selenium.isTextPresent("Bug 35 %"));
            assertTrue(selenium.isTextPresent("Test 35 %"));
            assertTrue(selenium.isTextPresent("Setup 30 %"));
            assertTrue(selenium.isTextPresent("Normalized Sessions count 2"));

            cs.logOut(selenium);
    }

    @Test
    public void linkedToSession() throws Exception {
        cs.cleanDb();

        cs.logIn(selenium);

        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.click("input_title");
        selenium.type("input_title", "testsession 1");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        String sessionIdToLinkTo = selenium.getText("sessionid");
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.click("input_title");
        selenium.type("input_title", "session 2");
        selenium.type("sessionlink", sessionIdToLinkTo);
        selenium.click("add_sessionlink");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        selenium.click("view_session");
        cs.waitForText(selenium, "Session title");
        assertTrue(selenium.isTextPresent(sessionIdToLinkTo));

        cs.logOut(selenium);
    }

    @Test
    public void linkedFromSession() throws Exception {
        cs.cleanDb();

        cs.logIn(selenium);
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.click("input_title");
        selenium.type("input_title", "testsession 1");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        String sessionIdToValidate = selenium.getText("sessionid");
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("15000");
        selenium.click("sessionlink");
        selenium.type("sessionlink", sessionIdToValidate);
        selenium.click("add_sessionlink");
        selenium.type("input_title", "session test 2");
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_list");
        selenium.waitForPageToLoad("15000");
        selenium.click("view_session"+sessionIdToValidate);
        cs.waitForText(selenium, "Session title");
        assertTrue(selenium.isTextPresent("testsession 1"));

        cs.logOut(selenium);
    }
}
