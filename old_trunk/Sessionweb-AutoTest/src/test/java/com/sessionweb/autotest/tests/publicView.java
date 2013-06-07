package com.sessionweb.autotest.tests;

import com.sessionweb.autotest.CommonSteps;
import com.sessionweb.autotest.SessionWebTest;
import com.thoughtworks.selenium.*;
import org.testng.annotations.*;
import static org.testng.Assert.*;

public class publicView extends SessionWebTest {
	CommonSteps cs = new CommonSteps();

	@Test
	public void viewSessionWithValidKey() throws Exception {
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

		cs.waitForText(selenium,"Session title");

		assertTrue(selenium.isTextPresent("ShareMe"));
	}

    @Test
	public void viewSessionWithInValidKey() throws Exception {
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

		cs.waitForText(selenium,"Session title");

        String url = selenium.getLocation();
        url = url.substring(0,url.length()-1);
        selenium.open(url);
        selenium.waitForPageToLoad("30000");
        assertTrue(selenium.isTextPresent("not valid"));
	}
}