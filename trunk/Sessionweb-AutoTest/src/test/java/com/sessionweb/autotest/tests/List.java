package com.sessionweb.autotest.tests;

import com.sessionweb.autotest.SessionWebTest;
import org.testng.annotations.Test;
import static org.testng.Assert.*;

public class List extends SessionWebTest {
    @Test
    public void multiplePages() throws Exception {
        cs.cleanDb();
        cs.logIn(selenium);

        selenium.click("url_list");
        selenium.waitForPageToLoad("30000");
        assertFalse(selenium.isElementPresent("prev_page"));
        assertFalse(selenium.isElementPresent("next_page"));
        for (int j = 0; j < 40; j++) {
            cs.createSession(selenium);
        }
        selenium.click("url_list");
        selenium.waitForPageToLoad("30000");
        assertFalse(selenium.isElementPresent("prev_page"));
        assertTrue(selenium.isElementPresent("next_page"));
        selenium.click("link=Next page");
        selenium.waitForPageToLoad("30000");
        assertTrue(selenium.isElementPresent("prev_page"));
        assertFalse(selenium.isElementPresent("next_page"));
        cs.logOut(selenium);
    }
}
