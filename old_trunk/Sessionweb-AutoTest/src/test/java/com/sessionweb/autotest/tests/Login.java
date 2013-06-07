package com.sessionweb.autotest.tests;

import com.sessionweb.autotest.CommonSteps;
import com.sessionweb.autotest.SessionWebTest;
import com.thoughtworks.selenium.*;
import org.testng.annotations.*;
import static org.testng.Assert.*;

public class Login extends SessionWebTest{
	CommonSteps cs = new CommonSteps();
	
	@Test
	public void logIn() throws Exception {
		cs.logIn(selenium);
		cs.logOut(selenium);
	}
}