@block_homework
  Feature: Enable the homework_feed  block on the dashboard and view its contents
  In order to enable the homework feed block on the dashboard
  As an admin
  I can add the homework feed block to the dashboard

  Background:
    Given the following "courses" exist:
    | fullname | shortname | id |
    | testingcourse | tc   | 33  |
    And the following "block_homework > homework" exist:
    | id | course | name | duedate    | intro | timecreated | timemodified |
    | 22 | 33      | test  | 1763119735 | <p> hej </p> | 1732012683 | 1732012683 |
    | 23 | 33      | test2 | 1763119735 | <p> hej </p> | 1732012683 | 1732012683 |
    And the following "users" exist:
    | username | firstname | lastname | email             |
    | user1    | John      | Doe      | user1@example.com |
    And the following "course enrolments" exist:
    | user  | course | role |
    | user1 | tc     | student     |
    | admin | tc     | teacher     |



@javascript
  Scenario: Add the homework feed block on the dashboard and view as an user
    Given I log in as "admin"
    Then the following should exist in the "homework" table:
      | id | course | name | duedate    | intro | timecreated | timemodified |
      | 22 | 33      | test  | 1763119735 | <p> hej </p> | 1732012683 | 1732012683 |
      | 23 | 33      | test2 | 1763119735 | <p> hej </p> | 1732012683 | 1732012683 |
    And I navigate to "Appearance > Default Dashboard page" in site administration
    And I turn editing mode on
    And I add the "[[pluginname]]" block
    And I click on "Reset Dashboard for all users" "button"
    And I click on "Continue" "button"
  And I log out
    When I log in as "user1"
    And I am on homepage
    Then I should see "Homework" in the "Homework" "block"
    And I should see "testingcourse" in the "Homework" "block"
    And I should see "test" in the "Homework feed" "block"
    And I should see "test2" in the "Homework feed" "block"
    And I should see "14-11-2025" in the "Homework feed" "block"