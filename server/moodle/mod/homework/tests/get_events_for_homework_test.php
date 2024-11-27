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
 * PHPUnit test case for mod_homework's ability to get events
 *
 * @package   mod_homework
 * @copyright 2024
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_homework;

use advanced_testcase;
use dml_exception;
use mod_homework\external\get_events_for_homework;
use mod_homework\external\homework_event_link;

/**
 * Class for testing getting events for homework.
 */
final class get_events_for_homework_test extends advanced_testcase {
    /**
     * Setup routine before running each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true); // Reset the Moodle environment after each test.
    }

    /**
     * Test For getting events which are not linked at all.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\get_events_for_homework
     */
    public function test_getting_non_linked_events(): void {
        global $DB;
        self::setAdminUser();
        // Create a user.
        $user = self::getDataGenerator()->create_user();
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        // Create an event that is linked to the course.
        $event = self::getDataGenerator()->create_event([
            'courseid' => $course->id,
            'eventtype' => 'course',
        ]);
        self::setUser($user);
        // Create a homework which is added to that course.
        $homeworkdata = (object)['course' => $course->id, 'timecreated' => time(), 'timemodified' => time()];
        $homeworkid = homework_add_instance($homeworkdata);

        $event = $DB->get_record('event', ['id' => $event->id]);

        // Get events from the database to link.
        $result = get_events_for_homework::execute($homeworkid);

        // Test that the gotten event was correct.
        self::assertStringContainsString('Chose an event to link', implode(' ', $result));
        self::assertStringContainsString('event' . $event->id, implode(' ', $result));
    }

    /**
     * Test for getting other events when one is already linked.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\get_events_for_homework
     */
    public function test_getting_more_events_when_already_linked(): void {
        global $DB;
        self::setAdminUser();
        // Create a user.
        $user = self::getDataGenerator()->create_user();
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        // Create an event that is linked to the course.
        $event1 = self::getDataGenerator()->create_event([
            'courseid' => $course->id,
            'name' => 'event1',
            'eventtype' => 'course',
        ]);
        $event2 = self::getDataGenerator()->create_event([
            'courseid' => $course->id,
            'name' => 'event2',
            'eventtype' => 'course',
        ]);
        self::setUser($user);

        // Create a homework which is added to that course.
        $homeworkdata = (object)['course' => $course->id, 'timecreated' => time(), 'timemodified' => time()];
        $homeworkid = homework_add_instance($homeworkdata);

        // Get the full event data from the database.
        $event1 = $DB->get_record('event', ['id' => $event1->id]);
        $event2 = $DB->get_record('event', ['id' => $event2->id]);

        // Link event1 to homework.
        homework_event_link::execute($homeworkid, 2, $event1->id);

        // Get the events from the database.
        $result = get_events_for_homework::execute($homeworkid);

        // Assert that event1 has been linked and that event2 is able to be linked.
        self::assertStringContainsString("This homework is already linked to Event:", implode(' ', $result));
        self::assertStringContainsString($event1->name, implode(' ', $result));
        self::assertStringContainsString('Chose an event to link', implode(' ', $result));
        self::assertStringContainsString('event' . $event2->id, implode(' ', $result));
    }

    /**
     * Test linking a new event when one is already linked.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\get_events_for_homework
     */
    public function test_linking_new_event_when_already_linked(): void {
        global $DB;
        self::setAdminUser();
        // Create a user.
        $user = self::getDataGenerator()->create_user();
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        // Create an event that is linked to the course.
        $event1 = self::getDataGenerator()->create_event([
            'courseid' => $course->id,
            'name' => 'event1',
            'eventtype' => 'course',
        ]);
        $event2 = self::getDataGenerator()->create_event([
            'courseid' => $course->id,
            'name' => 'event2',
            'eventtype' => 'course',
        ]);
        self::setUser($user);

        // Create a homework which is added to that course.
        $homeworkdata = (object)['course' => $course->id, 'timecreated' => time(), 'timemodified' => time()];
        $homeworkid = homework_add_instance($homeworkdata);

        // Get the full event data from the database.
        $event1 = $DB->get_record('event', ['id' => $event1->id]);
        $event2 = $DB->get_record('event', ['id' => $event2->id]);

        // Link event1 to homework.
        homework_event_link::execute($homeworkid, 2, $event1->id);

        // Get the result from the database.
        $result = get_events_for_homework::execute($homeworkid);

        // Assert that event1 has been linked and that event2 is able to be linked.
        self::assertStringContainsString("This homework is already linked to Event:", implode(' ', $result));
        self::assertStringContainsString($event1->name, implode(' ', $result));
        self::assertStringContainsString('Chose an event to link', implode(' ', $result));
        self::assertStringContainsString('event' . $event2->id, implode(' ', $result));

        // Link the other event.
        homework_event_link::execute($homeworkid, 2, $event2->id);

        // Get the updated result from the database.
        $result = get_events_for_homework::execute($homeworkid);

        // Assert that event2 has been linked and that event1 is able to be linked.
        self::assertStringContainsString("This homework is already linked to Event:", implode(' ', $result));
        self::assertStringContainsString($event2->name, implode(' ', $result));
        self::assertStringContainsString('Chose an event to link', implode(' ', $result));
        self::assertStringContainsString('event' . $event1->id, implode(' ', $result));
    }

    /**
     * Test getting events when there are no available events.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\get_events_for_homework
     */
    public function test_getting_no_events(): void {
        global $DB;
        self::setAdminUser();
        // Create a user.
        $user = self::getDataGenerator()->create_user();
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        // Create a homework which is added to that course.
        $homeworkdata = (object)['course' => $course->id, 'timecreated' => time(), 'timemodified' => time()];
        $homeworkid = homework_add_instance($homeworkdata);

        // Try to get events from the database.
        $result = get_events_for_homework::execute($homeworkid);

        self::assertStringContainsString('There are no available events to link', implode(' ', $result));
    }
}
