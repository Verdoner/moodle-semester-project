@mod_homework @homework
Feature: Create a homework activity
  In order to assign homework to students
  As an teacher
  I can create a homework activity

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

  @javascript
  Scenario: Create a homework activity
    Given I log in as "teacher1"
    And I turn editing mode on
    And I am on the "testingcourse" course page
    And I click on "Add an activity or resource" "button"
    And I click on "Add a new Homework" "link"
    And I set the field "name" to "testinghomework"
    When I click on "Save and return to course" "button"
    Then I should see "testinghomework"
