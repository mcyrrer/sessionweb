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
	 * 
	 * -Dmysqlhost=localhost 
	 * -Dmysqlport=3306 
	 * -Dmysqluser=sessionweb
	 * -Dmysqlpassword=2easy
	 * 
	 */
	@BeforeClass
	public void setUp() throws Exception {
		String host = System.getProperty("host");
		String portString = System.getProperty("selport");
		System.out.println(System.getenv("host"));
		System.out.println(host);
		System.err.println("4444");
		int port = Integer.parseInt(portString);
		String browser = System.getProperty("browser");
		String url = System.getProperty("url");
		
		
		
		selenium = new DefaultSelenium(host, port, browser, url);
		selenium.start();
		selenium.windowMaximize();

	}
//	
//	@Test
//	public void logIn() throws Exception {
//		CommonSteps cs = new CommonSteps();
//		cs.logIn(selenium);
//		cs.logOut(selenium);
//	}

	@AfterClass
	public void tearDown() throws Exception {
		selenium.stop();
	}
}
