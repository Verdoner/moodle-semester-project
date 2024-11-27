<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * PHPUnit test case for mod_homework's ability to link homework and events
 *
 * @package   mod_homework
 * @copyright 2024
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_homework;

use advanced_testcase;
use core_calendar\local\event\entities\event;
use dml_exception;
use mod_homework\external\homework_event_link;
/**
 * Class for testing linking homework and events.
 */
final class homework_event_link_test extends advanced_testcase {
    /**
     * Setup routine before running each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true); // Reset the Moodle environment after each test.
    }

    /**
     * Test linking a homework and event.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\get_events_for_homework
     */
    public function test_linking_homework_and_event(): void {
        global $DB;
        self::setAdminUser();
        // Create a user.
        $user = self::getDataGenerator()->create_user();
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        // Create an event that is linked to the course.
        $event = self::getDataGenerator()->create_event([
            'courseid' => $course->id,
            'name' => 'event1',
            'eventtype' => 'course',
        ]);
        self::setUser($user);

        // Create a homework which is added to that course.
        $homeworkdata = (object)['course' => $course->id, 'timecreated' => time(), 'timemodified' => time()];
        $homeworkid = homework_add_instance($homeworkdata);


        // Link the event and homework.
        homework_event_link::execute($homeworkid, 2, $event->id);

        // Get the homework data from the database.
        $homework = $DB->get_record('homework', ['id' => $homeworkid]);

        // Assert that they were linked correctly.
        self::assertEquals($event->id, $homework->eventid);
        self::assertEquals(2, $homework->course_module_id);
    }
}
