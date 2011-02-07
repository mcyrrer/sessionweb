package com.sessionweb.autotest.setupForManualTest;

import static org.testng.Assert.*;

import com.thoughtworks.selenium.Selenium;
import org.testng.annotations.Test;

import com.sessionweb.autotest.CommonSteps;
import com.sessionweb.autotest.SessionWebTest;

public class ManualSetup extends SessionWebTest {
	@Test public void setupDbForManualTest() throws Exception {
		cs.logIn(selenium);
		selenium.click("url_settings");
		selenium.waitForPageToLoad("15000");
		selenium.click("link=Add team");
		selenium.waitForPageToLoad("15000");
		selenium.type("teamtname", "seleniumteam1");
		selenium.click("//input[@value='Add team']");
		selenium.waitForPageToLoad("15000");
		selenium.click("link=Add team");
		selenium.waitForPageToLoad("15000");
		selenium.type("teamtname", "seleniumteam2");
		selenium.click("//input[@value='Add team']");
		selenium.waitForPageToLoad("15000");
		selenium.click("link=Add team");
		selenium.waitForPageToLoad("15000");
		selenium.type("teamtname", "seleniumteam3");
		selenium.click("//input[@value='Add team']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_adduser");
		selenium.waitForPageToLoad("15000");
		selenium.type("fullname", "test");
		selenium.type("username", "test");
		selenium.type("swpassword1", "test");
		selenium.click("//input[@value='Add']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='sprintname']", "seleniumsprint1");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='sprintname']", "seleniumsprint2");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='sprintname']", "seleniumsprint3");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addarea");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='areaname']", "seleniumarea1");
		selenium.click("//input[@value='Add area']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addarea");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='areaname']", "seleniumarea2");
		selenium.click("//input[@value='Add area']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addarea");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='areaname']", "seleniumarea3");
		selenium.click("//input[@value='Add area']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addteamsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='teamsprintname']", "seleniumteamsprint1");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addteamsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='teamsprintname']", "seleniumteamsprint2");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");
		selenium.click("url_addteamsprint");
		selenium.waitForPageToLoad("15000");
		selenium.type("//input[@name='teamsprintname']", "seleniumteamsprint3");
		selenium.click("//input[@value='Add name']");
		selenium.waitForPageToLoad("15000");

        for(int j=0;j<40;j++)
        {
            cs.createSession(selenium);
        }
        cs.logOut(selenium);
	}
}
