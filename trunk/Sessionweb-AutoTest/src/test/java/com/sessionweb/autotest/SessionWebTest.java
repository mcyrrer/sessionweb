package com.sessionweb.autotest;

import com.thoughtworks.selenium.*;
import org.testng.annotations.*;


public class SessionWebTest {
	public Selenium selenium;

	/**
	 * Execute this by adding these to the VM arguments. E.g. 
	 * -Dhost=localhost
	 * -Dport=4444 
	 * -Dbrowser=*firefox 
	 * -Durl=http://localhost/sessionweb/
	 */
	@BeforeClass
	public void setUp() throws Exception {
		String host = System.getProperty("host");
		String portString = System.getProperty("selport");
		int port = Integer.parseInt(portString);
		String browser = System.getProperty("browser");
		String url = System.getProperty("url");
		
		
		
		selenium = new DefaultSelenium(host, port, browser, url);
		selenium.start();
		selenium.windowMaximize();

	}

	@AfterClass
	public void tearDown() throws Exception {
		selenium.stop();
	}
}
