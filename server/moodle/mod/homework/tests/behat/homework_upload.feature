@mod_homework @homework
Feature: Upload homework
  In order to give students materials to study
  As a teacher
  I need to be able to upload materials for homework

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
    Scenario: Student tries to upload materials
      Given I log in as "user1"
      And I am on the "testingcourse" course page
      When I click on "testinghomework" "link"
      Then I should not see "Open Homework Chooser"


    @javascript
    Scenario: Teacher uploads homework materials
      Given I log in as "teacher1"
      And I am on the "testingcourse" course page
      And I click on "testinghomework" "link"
      And I click on "open-homework-chooser" "button"
      And I set the field "Input Field:" to "testingbog"
      And I set the field "Start Page" to "2"
      And I set the field "End Page" to "5"
      And I press "Submit"
      And I log out
      When I log in as "user1"
      And I am on the "testingcourse" course page
      And I click on "testinghomework" "link"
      Then I should see "testingbog"
      And I should see "Pages: 2 - 5"