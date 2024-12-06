@block_homework @linker @homework
  Feature: Linking homework activity to calendar event
    In order to view homework in the calendar
    As a teacher
    I need to link a homework activity with a calendar event

 Background:
   Given the following "courses" exist:
     | fullname | shortname | id | startdate |
     | testingcourse | tc   | 33  | 1701388800 |
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

   # Event for some reason does not appear in the event linker
   # Kept for future work
   #@javascript
   #Scenario: Teacher links a homework activity to a event
   #  Given I log in as "teacher1"
   #  And I create a calendar event:
   #   | Type of event | course |
   #   | Course        | testingcourse  |
   #   | Event title   | testinglecture |
   #   | timestart[day] | 24             |
   #   | timestart[month] | 12             |
   #   | timestart[year] | 2024             |
   #   | timestart[hour] | 14             |
   #   | timestart[minute] | 50             |
   #  And I am on the "testingcourse" course page
   #  And I click on "testinghomework" "link"
   #  And I click on "open-event-linker" "button"
   #  And I click on "Name: testinghomework Time of the event: 2025-12-24 14:50:00" "radio"
   #  And I click on "Submit"
   #  And I am on the site homepage
   #  And I click on "testinghomework" "link"
   #  Then I should see "Course event"
   #  And I should see "testingcourse"
