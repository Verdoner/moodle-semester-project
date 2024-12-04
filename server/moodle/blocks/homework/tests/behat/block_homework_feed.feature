@block_homework @homework
Feature: Enable the homework_feed  block on the dashboard and view its contents
  In order to allow users to view the homework feed
  As an admin
  I can add the homework feed block to the dashboard

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


  @javascript
  Scenario: Add the homework feed block on the dashboard and view as an user
    Given I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    And I turn editing mode on
    And I add the "Homework" block
    And I click on "Reset Dashboard for all users" "button"
    And I click on "Continue" "button"
    And I log out
    When I log in as "user1"
    And I am on homepage
    Then I should see "Homework" in the "Homework" "block"

  @javascript
  Scenario: Add the homework feed block on the dashboard, add some homework and view as an user
    Given I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    And I turn editing mode on
    And I click on ".block-add[data-blockregion='content']" "css_element"
    And I click on "Homework" "link"
    And I click on "Reset Dashboard for all users" "button"
    And I click on "Continue" "button"
    And I am on the "testingcourse" course page
    And I click on "Add an activity or resource" "button"
    And I click on "Add a new Homework" "link"
    And I set the field "name" to "testinghomework"
    And I click on "Due Date" "link"
    And I click on "duedateselector[enabled]" "checkbox"
    And I click on "duedateselector[day]" "select"
    And I click on "15" "option"
    And I click on "duedateselector[month]" "select"
    And I click on "October" "option"
    And I click on "duedateselector[year]" "select"
    And I click on "2025" "option"
    And I click on "Save and return to course" "button"
    And I am on the "testingcourse" course page
    And I click on "Add an activity or resource" "button" skipping visibility check
    And I click on "Add a new Homework" "link"
    And I set the field "name" to "testinghomework2"
    And I click on "Due Date" "link"
    And I click on "duedateselector[enabled]" "checkbox"
    And I click on "duedateselector[day]" "select"
    And I click on "15" "option"
    And I click on "duedateselector[month]" "select"
    And I click on "October" "option"
    And I click on "duedateselector[year]" "select"
    And I click on "2026" "option"
    And I click on "Save and return to course" "button"
    And I log out
    When I log in as "user1"
    And I am on homepage
    Then I should see "Homework" in the "Homework" "block"
    And I should see "testingcourse" in the "Homework" "block"
    And I should see "2025" in the "Homework" "block"
    Then I click on "sort" "select"
    And I click on "Due Date" "option"
    #this does not work but was intended to be used to test sorting: Then "15-10-2025" "text" should appear before "15-10-2026" "text"
