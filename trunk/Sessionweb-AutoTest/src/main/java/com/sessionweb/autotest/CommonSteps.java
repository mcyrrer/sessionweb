package com.sessionweb.autotest;

import com.thoughtworks.selenium.Selenium;
import de.svenjacobs.loremipsum.LoremIpsum;

import java.sql.*;
import java.util.Random;
import java.util.UUID;

import static org.testng.Assert.assertTrue;
import static org.testng.Assert.fail;

public class CommonSteps {
    private Connection connect = null;
    private Statement statement = null;
    private PreparedStatement preparedStatement = null;
    private ResultSet resultSet = null;
    LoremIpsum li = new LoremIpsum();
    Random generator = new Random();
    String mysqlhost = System.getProperty("mysqlhost");
    String mysqldb = System.getProperty("mysqldb");
    String mysqluser = System.getProperty("mysqluser");
    String mysqlpassword = System.getProperty("mysqlpassword");

    public void logIn(Selenium selenium) {
        selenium.open("/sessionweb/index.php?logout=yes");
        selenium.type("myusername", "admin");
        selenium.type("mypassword", "admin");
        selenium.click("Submit");
        selenium.waitForPageToLoad("15000");
        assertTrue(selenium.isTextPresent("[Administrator]"));
    }

    public void logInAsTestUser(Selenium selenium) {
        selenium.open("/sessionweb/index.php?logout=yes");
        selenium.type("myusername", "test");
        selenium.type("mypassword", "test");
        selenium.click("Submit");
        selenium.waitForPageToLoad("15000");
        assertTrue(selenium.isTextPresent("[test]"));
    }

    public void logOut(Selenium selenium) throws Exception {
        selenium.click("url_logout");
        selenium.waitForPageToLoad("15000");
        Thread.sleep(1000);
        assertTrue(selenium.isTextPresent("You are logged out"));
    }

    public void logInAsTestUserThroughUrl(Selenium selenium) throws Exception {
        selenium.open("/sessionweb/index.php?logout=yes");
        selenium.waitForPageToLoad("15000");
        Thread.sleep(1000);
        assertTrue(selenium.isTextPresent("You are logged out"));
    }

    public void createSession(Selenium selenium) throws Exception {
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("30000");
        String title = li.getWords(15, generator.nextInt(50));
        selenium.type("input_title", title);
        selenium.type("textarea1", li.getWords(50, generator.nextInt(50)));
        selenium.type("textarea2", li.getWords(50, generator.nextInt(50)));
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("30000");
    }

    public void createRandomSession(Selenium selenium) throws Exception {
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("30000");
        String title = li.getWords(15, generator.nextInt(50));
        selenium.type("input_title", title);
        selenium.type("textarea1", li.getWords(50, generator.nextInt(50)));
        selenium.type("textarea2", li.getWords(50, generator.nextInt(50)));
        int pos = generator.nextInt(2) + 1;
        selenium.select("select_team", "index=" + pos);
        pos = generator.nextInt(2) + 1;
        selenium.select("select_sprint", "index=" + pos);
        pos = generator.nextInt(2) + 1;
        selenium.select("select_teamsprint", "index=" + pos);
        pos = generator.nextInt(2) + 1;
        selenium.addSelection("select_area", "index=" + pos);
        selenium.select("setuppercent", "label=55");
        selenium.select("testpercent", "label=30");
        selenium.select("bugpercent", "label=30");
        selenium.select("setuppercent", "label=20");
        selenium.select("oppertunitypercent", "label=20");
        selenium.select("duration", "label=210");
        boolean executed = false;
        if (generator.nextBoolean()) {
            selenium.check("executed");
            executed = true;
        }


        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("30000");
        if (executed) {
            if (generator.nextBoolean()) {
                String sessionid = selenium.getText("sessionid");
                selenium.click("url_list");
                selenium.waitForPageToLoad("30000");
                selenium.click("debrief_session" + sessionid);
                selenium.waitForPageToLoad("30000");
                selenium.click("//input[@value='Continue']");
                selenium.waitForPageToLoad("30000");
            }
        }

    }

    String getRandom(int length) {
        UUID uuid = UUID.randomUUID();
        String myRandom = uuid.toString();
        return myRandom.substring(length);
    }

    public void cleanDb() throws SQLException, ClassNotFoundException {
        Class.forName("com.mysql.jdbc.Driver");


        connect = DriverManager.getConnection("jdbc:mysql://" + mysqlhost + "/" + mysqldb + "?connectTimeout=5000", mysqluser, mysqlpassword);
        // Statements allow to issue SQL queries to the database
        statement = connect.createStatement();
        // Result set get the result of the SQL query

        statement.execute("DELETE FROM mission_sessionmetrics");
        statement.execute("DELETE FROM mission_areas");
        statement.execute("DELETE FROM mission_bugs");
        statement.execute("DELETE FROM mission_debriefnotes");
        statement.execute("DELETE FROM mission_requirements");
        statement.execute("DELETE FROM mission_sessionmetrics");
        statement.execute("DELETE FROM mission_sessionsconnections");
        statement.execute("DELETE FROM mission_status");
        statement.execute("DELETE FROM user_settings");
        statement.execute("DELETE FROM mission");
        statement.execute("DELETE FROM sessionid");
        statement.execute("DELETE FROM sprintnames");
        statement.execute("DELETE FROM teamnames");
        statement.execute("DELETE FROM teamsprintnames");
        statement.execute("DELETE FROM members");
        statement.execute("DELETE FROM areas");
        statement.execute("DELETE FROM settings");


        String sql = ""
                + "INSERT INTO `sessionwebos`.`settings` "
                + "            (`normalized_session_time`, "
                + "             `team`, "
                + "             `sprint`, "
                + "             `teamsprint`, "
                + "             `area`, "
                + "             `analyticsid`, "
                + "             `url_to_dms`, "
                + "             `url_to_rms`) "
                + "VALUES      ('90', "
                + "             '1', "
                + "             '1', "
                + "             '1', "
                + "             '1', "
                + "             '', "
                + "             '', "
                + "             '')";

        statement.execute(sql);

        sql = ""
                + "INSERT INTO `members` "
                + "            (`username`, "
                + "             `fullname`, "
                + "             `active`, "
                + "             `superuser`, "
                + "             `admin`, "
                + "             `password`) "
                + "VALUES      ('admin', "
                + "             'Administrator', "
                + "             '1', "
                + "             '1', "
                + "             '1', "
                + "             '21232f297a57a5a743894a0e4a801fc3')";


        statement.execute(sql);

        sql = ""
                + "INSERT INTO `user_settings` "
                + "            (`username`) "
                + "VALUES      ('admin')";
        statement.execute(sql);

        connect.close();
        System.out.println("Sessionweb Database cleaned and ready for usage");

    }

    public void waitForText(Selenium selenium, String text) throws InterruptedException {
        for (int second = 0; ; second++) {
            if (second >= 30) fail("timeout: Could not find text " + text + ".");
            try {
                if (selenium.isTextPresent(text)) break;
            } catch (Exception e) {
            }
            Thread.sleep(1000);
        }
    }

    public String formatTableContentToCommonString(Selenium selenium, String xPath) {
        String tableToFormat = selenium.getTable(xPath);
        return tableToFormat.replace(":   ", ": ");
    }
}