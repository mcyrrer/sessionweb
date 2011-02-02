package com.sessionweb.autotest.tests;

import static org.testng.Assert.*;

import org.testng.annotations.Test;

import com.sessionweb.autotest.CommonSteps;
import com.sessionweb.autotest.SessionWebTest;

public class Settings extends SessionWebTest {
	CommonSteps cs = new CommonSteps();

	@Test public void addTeam() throws Exception {
		cs.logIn(selenium);
		
		selenium.click("url_settings");
		selenium.waitForPageToLoad("30000");
		System.out.println(selenium.getHtmlSource());
		selenium.click("link=Add team");
		selenium.waitForPageToLoad("30000");
		selenium.type("teamtname", "testteam1");
		selenium.click("//input[@value='Add team']");
		selenium.waitForPageToLoad("30000");
		selenium.click("link=Add team");
		selenium.waitForPageToLoad("30000");
		selenium.type("teamtname", "testteam2");
		selenium.click("//input[@value='Add team']");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_list");
		selenium.waitForPageToLoad("30000");
		selenium.click("showoption");
		assertTrue(selenium.isTextPresent("testteam1"));
		assertTrue(selenium.isTextPresent("testteam2"));
		
		cs.logOut(selenium);
	}
	
	@Test public void addUser() throws Exception {
		
		cs.logIn(selenium);

		selenium.click("url_settings");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_adduser");
		selenium.waitForPageToLoad("30000");
		selenium.type("fullname", "Test User");
		selenium.type("username", "test");
		selenium.type("swpassword1", "test");
		selenium.click("admin");
		selenium.click("superuser");
		selenium.click("//input[@value='Add']");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_listusers");
		selenium.waitForPageToLoad("30000");
		
		assertTrue(selenium.isTextPresent("Test User"));
		assertEquals(selenium.getText("//tr[3]/td[3]"), "1");
		assertEquals(selenium.getText("//tr[3]/td[4]"), "1");
		assertEquals(selenium.getText("//tr[3]/td[5]"), "1");
		selenium.click("link=Test User");
		selenium.waitForPageToLoad("30000");
		selenium.click("admin");
		selenium.click("superuser");
		selenium.click("active");
		selenium.click("//input[@value='Update']");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_listusers");
		selenium.waitForPageToLoad("30000");
	
		assertEquals(selenium.getText("//tr[3]/td[3]"), "0");
		assertEquals(selenium.getText("//tr[3]/td[4]"), "0");
		assertEquals(selenium.getText("//tr[3]/td[5]"), "0");
		selenium.click("link=Test User");
		selenium.waitForPageToLoad("30000");
		selenium.click("active");
		selenium.click("//input[@value='Update']");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_listusers");
		selenium.waitForPageToLoad("30000");
		
		assertEquals(selenium.getText("//tr[3]/td[3]"), "1");
		assertEquals(selenium.getText("//tr[3]/td[4]"), "0");
		assertEquals(selenium.getText("//tr[3]/td[5]"), "0");
		
		cs.logOut(selenium);
	}
}