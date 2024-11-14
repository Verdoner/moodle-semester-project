@mod @mod_homework
Feature: Add a homework assignment
  In order to assign tasks to students
  As a teacher
  I need to create a homework assignment


  # Background:
  #   Given the following "users" exist:
  #     | username | firstname | lastname | email                |
  #     | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
  #     | student1 | Sam1      | Student1 | student1@example.com |
  #   And the following "courses" exist:
  #     | fullname | shortname | category |
  #    | Course 1 | C1        | 0        |
  #   And the following "course enrolments" exist:
  #     | user     | course | role           |
  #     | teacher1 | C1     | editingteacher |
  #     | student1 | C1     | student        |
  #   And the following "activity" exists:
  #     | activity | homework                  |
  #     | course   | C1                        |
  #     | idnumber | 00001                     |
  #     | name     | Test homework name        |
  #     | intro    | Test homework description |
  #     | section  | 1                         |
  #     | grade    | 10                        |

  @javascript
  Scenario: Login and add homework
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Homework" to section "1" using the activity chooser
    # And I add a "Homework" item to the "Test homework name" homework with:
    #   | Literature name | First literature item           |
    #   | Description     | Read the first chapter          |
    #   | Link            | https://example.com/resource    |
    And I log out

    # And I am on the "Test homework name" homework activity page logged in as "student1"
    # And I press "Submit homework"
    # Then I should see "First literature item"
    # And I should see "Read the first chapter"
    # And I set the field "Link" to "https://example.com/resource"
    # And I press "Finish submission"
    # And I should see "Submission saved"
    # And I press "Submit all and finish"

  @javascript @skip_chrome_zerosize
  Scenario: Add and configure small homework and perform a submission as a student with Javascript enabled
    Then I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "Thank you for your submission"
    And I should see "Please check back for feedback"
    And I follow "Finish review"
    And I should see "Grade: 0.00 / 10.00."

  Scenario: Add and configure small homework and perform a submission as a student with Javascript disabled
    Then I should see "Thank you for your submission"
    And I should see "Please check back for feedback"
    And I follow "Finish review"
    And I should see "Grade: 0.00 / 10.00."
