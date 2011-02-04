package com.sessionweb.autotest.tests;

import static org.testng.Assert.*;

import org.testng.annotations.Test;

import com.sessionweb.autotest.CommonSteps;
import com.sessionweb.autotest.SessionWebTest;

public class Settings extends SessionWebTest {
	CommonSteps cs = new CommonSteps();

	@Test public void addTeam() throws Exception {
		cs.cleanDb();
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
		cs.cleanDb();
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
	
	@Test public void addSprint() throws Exception {
		cs.cleanDb();
		cs.logIn(selenium);
		
		selenium.click("url_settings");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_addsprint");
		selenium.waitForPageToLoad("30000");
		selenium.type("//input[@name='sprintname']", "seleniumsprint1");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("seleniumsprint1"));
		selenium.click("url_addsprint");
		selenium.waitForPageToLoad("30000");
		selenium.type("//input[@name='sprintname']", "seleniumsprint2");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("seleniumsprint2 added to database"));
		selenium.click("url_addsprint");
		selenium.waitForPageToLoad("30000");
		selenium.type("//input[@name='sprintname']", "seleniumsprint3");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("seleniumsprint3 added to database"));
		selenium.click("url_list");
		selenium.waitForPageToLoad("30000");
		selenium.click("showoption");
		assertEquals(selenium.getTable("//form[@id='narrowform']/table.0.1"), "Sprint: seleniumsprint1 seleniumsprint2 seleniumsprint3");
		
		cs.logOut(selenium);
	}

	@Test public void addTeamSprint() throws Exception {
		cs.cleanDb();
		cs.logIn(selenium);
		
		selenium.click("url_settings");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addteamsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='teamsprintname']", "seleniumteamsprint1");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("seleniumteamsprint1 added to database"));
		selenium.click("url_addteamsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='teamsprintname']", "seleniumteamsprint2");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("seleniumteamsprint2 added to database"));
		selenium.click("url_addteamsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='teamsprintname']", "seleniumteamsprint3");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("seleniumteamsprint3 added to database"));
		selenium.click("url_list");
		selenium.waitForPageToLoad("15000");
		selenium.click("showoption");
		assertEquals(selenium.getTable("//form[@id='narrowform']/table.0.2"), "Team sprint: seleniumteamsprint1 seleniumteamsprint2 seleniumteamsprint3");
		
		cs.logOut(selenium);
	}
	
	@Test public void addArea() throws Exception {
		cs.cleanDb();
		cs.logIn(selenium);
		
		selenium.click("url_settings");
		selenium.waitForPageToLoad("30000");
		selenium.click("url_addarea");
		selenium.waitForPageToLoad("30000");
		selenium.type("//input[@name='areaname']", "seleniumarea1");
		selenium.click("//input[@value='Add area']");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("seleniumarea1 added to database"));
		selenium.click("url_addarea");
		selenium.waitForPageToLoad("30000");
		selenium.type("//input[@name='areaname']", "seleniumarea2");
		selenium.click("//input[@value='Add area']");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("seleniumarea2 added to database"));
		selenium.click("url_addarea");
		selenium.waitForPageToLoad("30000");
		selenium.type("//input[@name='areaname']", "seleniumarea3");
		selenium.click("//input[@value='Add area']");
		selenium.waitForPageToLoad("30000");
		assertTrue(selenium.isTextPresent("seleniumarea3 added to database"));
		selenium.click("url_list");
		selenium.waitForPageToLoad("30000");
		selenium.click("showoption");
		assertEquals(selenium.getTable("//form[@id='narrowform']/table/tbody/tr[2]/td[1]/table.0.1"), "seleniumarea1 seleniumarea2 seleniumarea3");
	
		cs.logOut(selenium);
	}

	@Test public void changePassword() throws Exception {
		cs.cleanDb();
		cs.logIn(selenium);
		
		selenium.click("url_settings");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_adduser");
		selenium.waitForPageToLoad("15000");
		selenium.type("fullname", "testpassword");
		selenium.type("username", "testpassword");
		selenium.type("swpassword1", "test");
		selenium.click("//input[@value='Add']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_logout");
		selenium.waitForPageToLoad("15000");
		selenium.type("myusername", "testpassword");
		selenium.type("mypassword", "test");
		selenium.click("Submit");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_settings");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_changepassword");
		selenium.waitForPageToLoad("15000");
		selenium.type("swpassword1", "123456");
		selenium.type("swpassword2", "123456");
		selenium.click("//input[@value='Change password']");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("Password changed"));
		selenium.click("url_logout");
		selenium.waitForPageToLoad("15000");
		selenium.type("myusername", "testpassword");
		selenium.type("mypassword", "test");
		selenium.click("Submit");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("Wrong Username or Password"));
		selenium.open("sessionweb/index.php?logout=yes");
		selenium.type("myusername", "testpassword");
		selenium.type("mypassword", "123456");
		selenium.click("Submit");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("[testpassword]"));
	
		cs.logOut(selenium);
	}
	
	@Test public void modulesActivatedAndDeactivated() throws Exception {
		cs.cleanDb();
		cs.logIn(selenium);
		//testdata
		selenium.click("url_newsession");
		selenium.waitForPageToLoad("15000");
		selenium.click("input_title");
		selenium.type("input_title", "testsession to test configuration options");
		selenium.click("input_submit");
		selenium.waitForPageToLoad("15000");
		//actual test
		selenium.click("url_list");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isElementPresent("tableheader_sprint"));
		assertTrue(selenium.isElementPresent("tableheader_teamsprint"));
		selenium.click("showoption");
		assertTrue(selenium.isElementPresent("select_sprint"));
		assertTrue(selenium.isElementPresent("select_teamsprint"));
		assertTrue(selenium.isElementPresent("select_team"));
		assertTrue(selenium.isElementPresent("select_area"));
		selenium.click("url_settings");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_configuration");
		selenium.waitForPageToLoad("15000");
		selenium.click("team");
		selenium.click("sprint");
		selenium.click("teamsprint");
		selenium.click("area");
		selenium.click("//input[@value='Change settings']");
		selenium.waitForPageToLoad("15000");
		Thread.sleep(2000);
		selenium.click("url_list");
		selenium.waitForPageToLoad("15000");
		selenium.click("showoption");
		assertFalse(selenium.isElementPresent("tableheader_sprint"));
		assertFalse(selenium.isElementPresent("tableheader_teamsprint"));
		selenium.click("showoption");
		assertFalse(selenium.isElementPresent("select_sprint"));
		assertFalse(selenium.isElementPresent("select_teamsprint"));
		assertFalse(selenium.isElementPresent("select_team"));
		assertFalse(selenium.isElementPresent("select_area"));
		
		//clean up the settings mess....
		selenium.click("url_settings");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_configuration");
		selenium.waitForPageToLoad("15000");
		selenium.click("team");
		selenium.click("sprint");
		selenium.click("teamsprint");
		selenium.click("area");
		selenium.click("//input[@value='Change settings']");
		selenium.waitForPageToLoad("15000");
		
		cs.logOut(selenium);
	}
}