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
// File: mod/myplugin/tests/sample_test.php.

namespace block_homework;

use stdClass;

/**
 * Test for block homework
 * @copyright group 11
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class block_homework_test extends \basic_testcase {
    /**
     * The test method itself
     * @return void
     * @covers :: blockhomework
     */
    public function test_course_homeworkfilter(): void {
        /*
        // Create test data
        $tmparray = [];
        $homeworks = [];

        $testhomework1 = new stdClass();
            $testhomework1->id = 1;
            $testhomework1->course = 2;
            $testhomework1->name = 'test 1';
            $testhomework1->timecreated = time();
            $testhomework1->timemodified = 0;
            $testhomework1->intro = '<p> test 1 </p>';
            $testhomework1->introformat = 1;
            $testhomework1->description = '';
            $testhomework1->eventid = 0;

        $testhomework2 = new stdClass();
            $testhomework2->id = 2;
            $testhomework2->course = 3;
            $testhomework2->name = 'test 1';
            $testhomework2->timecreated = time();
            $testhomework2->timemodified = 0;
            $testhomework2->intro = '<p> test 2 </p>';
            $testhomework2->introformat = 1;
            $testhomework2->description = '';
            $testhomework2->eventid = 0;


        // Assert that a course belonging to the correct course is returned
        array_push($homeworks, $testhomework1);
        $tmparray = \block_homework::filter_homework_content('http://localhost/course/view.php?id=3', $homeworks);
        $this->assertEquals($tmparray, $homeworks);

        // Assert that homework can be removed if the ids don't match
        array_push($homeworks, $testhomework2);
        $tmparray = \block_homework::filter_homework_content('http://localhost/course/view.php?id=3', $homeworks);
        $this->assertNotContains($testhomework2, $tmparray);
        */
    }
}
