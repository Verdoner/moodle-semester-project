@mod_homework
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
    And the following "course enrolments" exist:
      | user  | course | role |
      | user1 | tc     | student     |
      | admin | tc     | teacher     |
    And the following "activities" exists:
      | activity | homework         |
      | course   | tc               |
      | idnumber | 1                |
      | name     | testinghomework  |
      | intro    | Test description |
      | section  | 0                |


    @javascript
    Scenario: Teacher uploads homework materials
      Given I log in as "admin"
      And I am on the "testingcourse" course page
      And I click on "testinghomework" "activity"
      And I click on "Open Homework Chooser" "button"
      And I set the field "Input Field:" to "testingbog"
      And I set the field "Start Page" to "2"
      And I set the field "End Page" to "5"
      When I upload "mod/homework/tests/fixtures/homeworktest.txt" file to "Files" filemanager
      And I press "Submit"
      And I log out
      And I log in as "user1"
      And I am on the "testingcourse" course page
      And I click on "testinghomework" "activity"
      Then I should see "testingbog" in the "Homework" "activity"
      And I should see "Pages: 2 - 5" in the "Homework" "activity"