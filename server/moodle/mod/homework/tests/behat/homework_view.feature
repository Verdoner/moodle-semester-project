@mod @mod_homework @homework
Feature: Viewing the homework page
  In order to view homework materials and content
  As a student
  I need to visit the homework view page and check the content

  Background:
    Given the following "courses" exist:
      | fullname | shortname | id |
      | testingcourse | tc   | 33  |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | John      | Doe      | user1@example.com |
      | teacher1 | Jane      | Doe      | teacher1@edu.com  |
    And the following "course enrolments" exist:
      | user  | course | role |
      | user1 | tc     | student     |
      | admin | tc     | teacher     |
      | teacher1 | tc   | editingteacher     |
    And the following "activities" exists:
      | activity | homework         |
      | course   | tc               |
      | idnumber | 1                |
      | name     | testinghomework  |
      | intro    | Test description |
      | section  | 0                |

  @javascript
  Scenario: Teacher views the homework view page
    Given I log in as "teacher1"
    And I am on the "testingcourse" course page
    When I click on "testinghomework" "link"
    Then I should see "Homework"
    And I should see "Settings"
    And I should see "Submissions"
    And I should see "Edit"
    And I should see "More"
    And I should see "Open Homework Chooser"
    And I should see "testinghomework"


  @javascript
  Scenario: Student views the homework view page
    Given I log in as "user1"
    And I am on the "testingcourse" course page
    When I click on "testinghomework" "link"
    Then I should see "Submissions"
    And I should see "testinghomework"