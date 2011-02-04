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
		assertTrue(selenium.isTextPresent("Percentage = 100%"));
		selenium.click("executed");
		selenium.click("input_submit");
		selenium.waitForPageToLoad("15000");
		selenium.click("view_session");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer elit turpis, adipiscing imperdiet ultrices sed"));
		assertTrue(selenium.isTextPresent("testteam1"));
		assertTrue(selenium.isTextPresent("testsprint1"));
		assertTrue(selenium.isTextPresent("testteamsprint1"));
		assertEquals(selenium.getTable("//td[2]/table.0.5"), "Status Executed");
		assertEquals(selenium.getTable("//td[2]/table.0.6"), "Debriefed Not debriefed");
		assertEquals(selenium.getTable("//table.2.1"), "Requirements connected to session #12:Link to requirement");
		assertEquals(selenium.getTable("//table.8.1"), "Defects connected to session #12:12");
		assertEquals(selenium.getTable("//tr[11]/td[2]/table.0.4"), "Sessions duration 180 (min)");
		assertEquals(selenium.getTable("//tr[11]/td[2]/table.0.3"), "Oppertunity 0 %");
		assertEquals(selenium.getTable("//tr[11]/td[2]/table.0.2"), "Bug 35 %");
		assertEquals(selenium.getTable("//tr[11]/td[2]/table.0.1"), "Test 35 %");
		assertEquals(selenium.getTable("//tr[11]/td[2]/table.0.0"), "Setup 30 %");
		assertEquals(selenium.getTable("//tr[11]/td[2]/table.0.5"), "Normalized Sessions count 2");

		cs.logOut(selenium);
	}
}
