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
		selenium.waitForPageToLoad("30000");
		selenium.click("input_title");
		selenium.type("input_title", "test session for public key");
		selenium.type("requirement", "req");
		selenium.click("add_requirement");
		selenium.type("bug", "defect");
		selenium.select("setuppercent", "label=80");
		selenium.select("testpercent", "label=5");
		selenium.select("bugpercent", "label=5");
		selenium.select("oppertunitypercent", "label=10");
		selenium.select("duration", "label=90");
		selenium.click("executed");
		selenium.click("input_submit");
		selenium.waitForPageToLoad("30000");
		selenium.click("view_session");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isElementPresent("publiclink"));
		selenium.click("publiclink");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("test session for public key"));
		assertTrue(selenium.isTextPresent("80"));

	}

    @Test
	public void viewSessionWithInValidKey() throws Exception {
        cs.cleanDb();
		cs.logIn(selenium);

		selenium.click("url_newsession");
		selenium.waitForPageToLoad("30000");
		selenium.click("input_title");
		selenium.type("input_title", "test session for public key");
		selenium.type("requirement", "req");
		selenium.click("add_requirement");
		selenium.type("bug", "defect");
		selenium.select("setuppercent", "label=80");
		selenium.select("testpercent", "label=5");
		selenium.select("bugpercent", "label=5");
		selenium.select("oppertunitypercent", "label=10");
		selenium.select("duration", "label=90");
		selenium.click("executed");
		selenium.click("input_submit");
		selenium.waitForPageToLoad("30000");
		selenium.click("view_session");
		selenium.waitForPageToLoad("30000");
		selenium.click("publiclink");
		selenium.waitForPageToLoad("30000");


        String url = selenium.getLocation();
        url = url.substring(0,url.length()-1);
        selenium.open(url);
        selenium.waitForPageToLoad("30000");
        assertTrue(selenium.isTextPresent("not valid"));
	}
}