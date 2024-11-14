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
    | id | course | name | duedate    | intro |
    | 22 | 33      | test  | 1763119735 | hej |
    | 23 | 33      | test2 | 1763119735 | hej |
    And the following "users" exist:
    | username | firstname | lastname | email             |
    | user1    | John      | Doe      | user1@example.com |
    And the following "course enrolments" exist:
    | user  | course |
    | user1 | tc     |




  Scenario: Add the homework feed block on the dashboard and view as an user
    Given I log in as "admin"
    And I am on homepage
    And I turn editing mode on
    And I add the "[[pluginname]]" block
    And I log out
    When I log in as "user1"
    And I am on homepage
    Then I should see "Homework" in the "Homework feed" "block"
    And I should see "testingcourse" in the "Homework feed" "block"
    And I should see "test" in the "Homework feed" "block"
    And I should see "test2" in the "Homework feed" "block"
    And I should see "14-11-2025" in the "Homework feed" "block"