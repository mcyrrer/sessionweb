package com.sessionweb.autotest;

import static org.testng.Assert.assertTrue;

import com.thoughtworks.selenium.Selenium;

public class CommonSteps {


	public void logIn(Selenium selenium) {
		selenium.open("/sessionweb/");
		selenium.type("myusername", "admin");
		selenium.type("mypassword", "admin");
		selenium.click("Submit");
		selenium.waitForPageToLoad("15000");		
		assertTrue(selenium.isTextPresent("[Administrator]"));
	}
	
	public void logOut(Selenium selenium) throws Exception {
		selenium.click("url_logout");
		selenium.waitForPageToLoad("15000");
		assertTrue(selenium.isTextPresent("You are logged out"));
	}
}