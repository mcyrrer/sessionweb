package com.sessionweb.autotest;

import static org.testng.Assert.assertTrue;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import com.thoughtworks.selenium.Selenium;

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
		assertTrue(selenium.isTextPresent("You are logged out"));
	}

	public void cleanDb() throws SQLException, ClassNotFoundException {
			Class.forName("com.mysql.jdbc.Driver");

			
			connect = DriverManager.getConnection("jdbc:mysql://"+mysqlhost+"/"+mysqldb+"?connectTimeout=5000",mysqluser,mysqlpassword);
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
}