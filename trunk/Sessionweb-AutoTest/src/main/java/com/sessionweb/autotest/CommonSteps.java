package com.sessionweb.autotest;

import com.thoughtworks.selenium.Selenium;

import java.io.InterruptedIOException;
import java.sql.*;
import java.util.UUID;

import static org.testng.Assert.assertTrue;
import static org.testng.Assert.fail;

public class CommonSteps {
    private Connection connect = null;
    private Statement statement = null;
    private PreparedStatement preparedStatement = null;
    private ResultSet resultSet = null;
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

    public void logOut(Selenium selenium) throws Exception {
        selenium.click("url_logout");
        selenium.waitForPageToLoad("15000");
        Thread.sleep(1000);
        assertTrue(selenium.isTextPresent("You are logged out"));
    }

    public void createSession(Selenium selenium) throws Exception {
        selenium.click("url_newsession");
        selenium.waitForPageToLoad("30000");
        selenium.click("input_title");
        String title = getRandom(30);
        selenium.type("input_title", title);
        selenium.click("//input[@value='Save']");
        selenium.waitForPageToLoad("30000");
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