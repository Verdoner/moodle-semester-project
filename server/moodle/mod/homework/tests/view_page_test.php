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
 * homework/tests/view_page_test.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework;

// use advanced_testcase;
use core\exception\coding_exception;
use core\exception\moodle_exception;
use DOMDocument;
use moodle_url;

/**
 *
 */
final class view_page_test extends \advanced_testcase {

    // Set up the necessary environment (moodle, etc.) for your test.
    protected function setUp(): void {
        global $DB, $CFG;

        parent::setUp();

        // Ensure the user is logged in (if necessary for the page to load).
        $this->setAdminUser();

    }

    /**
     *
     * @return void
     * @covers :: \mod_homework\view.php
     * @throws \coding_exception
     * @throws moodle_exception
     */
    public function test_view_page(): void {
        global $DB, $CFG;

        // Set up necessary data for the test, such as course and module.
        $this->resetAfterTest();
        $result = null;
        $course = $this->getDataGenerator()->create_course();
        $homework = $this->getDataGenerator()->create_module('homework', ['course' => $course->id, 'homework' => 'test']);

        // Simulate a request to the page.
        $cm = get_coursemodule_from_instance('homework', $homework->id);
        $this->assertNotFalse($cm, 'Failed to retrieve course module for homework instance.');
        $url = new moodle_url('/mod/homework/view.php', ['id' => $cm->id]);

        $bookrecord = new \stdClass();
        $bookrecord->homework_id = $homework->id;
        $bookrecord->description = 'This is a book.';
        $bookrecord->timecreated = time();
        $bookrecord->timemodified = time();
        $bookrecord->usermodified = 0;
        $bookrecord->introformat = 0;
        $bookrecord->startpage = 10;
        $bookrecord->endpage = 20;
        $bookrecord->file_id = 2;

        $videorecord = new \stdClass();
        $videorecord->homework_id = $homework->id;
        $videorecord->description = 'This is a video.';
        $videorecord->timecreated = time();
        $videorecord->timemodified = time();
        $videorecord->usermodified = 0;
        $videorecord->introformat = 0;
        $videorecord->starttime = 90;
        $videorecord->endtime = 3601;
        $videorecord->file_id = 3;

        $linkrecord = new \stdClass();
        $linkrecord->homework_id = $homework->id;
        $linkrecord->description = 'This is a link.';
        $linkrecord->timecreated = time();
        $linkrecord->timemodified = time();
        $linkrecord->usermodified = 0;
        $linkrecord->introformat = 0;
        $linkrecord->link = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $DB->insert_record('homework_materials', $bookrecord);
        $DB->insert_record('homework_materials', $videorecord);
        $DB->insert_record('homework_materials', $linkrecord);
        // Call the external function directly.

        // Generate plugin page.
        require_once($CFG->dirroot . '/mod/homework/view.php?id=' . $homework->id);
        $output = ob_get_clean();
        // Parse the HTML using DOMDocument to check for the required elements.
        $dom = new \DOMDocument();
        @$dom->loadHTML($output);  // Suppress warnings for invalid HTML.

        // Check that each element is present in the HTML.
        $this->assertNotNull($dom->getElementsByTagName('div')->item(0), 'Modal container is missing');
    }
}
