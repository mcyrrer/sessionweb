package com.sessionweb.autotest.setupForManualTest;

import com.sessionweb.autotest.SessionWebTest;
import de.svenjacobs.loremipsum.LoremIpsum;
import org.testng.annotations.Test;

import java.util.Random;

public class ManualSetup extends SessionWebTest {
    LoremIpsum li = new LoremIpsum();
    Random generator = new Random();

    @Test
    public void setupDbForManualTest() throws Exception {


        cs.logIn(selenium);
        selenium.click("url_settings");
        selenium.waitForPageToLoad("15000");
        selenium.click("link=Add team");
        selenium.waitForPageToLoad("15000");
        selenium.type("teamtname", "1-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add team']");
        selenium.waitForPageToLoad("15000");
        selenium.click("link=Add team");
        selenium.waitForPageToLoad("15000");
        selenium.type("teamtname", "2-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add team']");
        selenium.waitForPageToLoad("15000");
        selenium.click("link=Add team");
        selenium.waitForPageToLoad("15000");
        selenium.type("teamtname", "3-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add team']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_adduser");
        selenium.waitForPageToLoad("15000");
        selenium.type("fullname", "test");
        selenium.type("username", "test");
        selenium.type("swpassword1", "test");
        selenium.click("superuser");
        selenium.click("//input[@value='Add']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='sprintname']", "4-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='sprintname']", "5-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='sprintname']", "6-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addarea");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='areaname']", "7-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add area']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addarea");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='areaname']", "8-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add area']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addarea");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='areaname']", "9-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add area']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addteamsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='teamsprintname']", "10-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addteamsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='teamsprintname']", "11-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        selenium.click("url_addteamsprint");
        selenium.waitForPageToLoad("15000");
        selenium.type("//input[@name='teamsprintname']", "12-" + li.getWords(1, generator.nextInt(50)));
        selenium.click("//input[@value='Add name']");
        selenium.waitForPageToLoad("15000");
        cs.logOut(selenium);
        cs.logInAsTestUser(selenium);
        for (int j = 0; j < 40; j++) {
            cs.createRandomSession(selenium);
        }
        cs.logOut(selenium);
    }

    @Test
    public void testUntitled() throws Exception {
        cs.logIn(selenium);
        selenium.open("/sessionweb/session.php?command=new");
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("30000");
        selenium.type("input_title", "TESTTITLE");
        selenium.type("textarea1", "TITLE");
        selenium.click("input_submit");
        selenium.waitForPageToLoad("30000");
    }

}
