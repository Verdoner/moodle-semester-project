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
 * homework/tests/modal_test.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework;

use advanced_testcase;
use core\exception\coding_exception;
use DOMDocument;

/**
 *
 */
final class modal_test extends advanced_testcase {
    /**
     *
     * @return void
     * @throws coding_exception
     * @runInSeparateProcess
     * @covers :: \mod_homework\external\get_homework_chooser
     */
    public function test_get_homework_chooser(): void {
        global $DB;

        // Set up necessary data for the test, such as course and module.
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $homework = $this->getDataGenerator()->get_plugin_generator('mod_homework')->create_instance(['course' => $course->id]);
        // Call the external function directly.
        $result = \mod_homework\external\get_homework_chooser::execute($homework->id);

        // Verify that the result contains the expected HTML structure.
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('html', $result);

        // Parse the HTML using DOMDocument to check for the required elements.
        $dom = new DOMDocument();
        @$dom->loadHTML($result['html']);  // Suppressing warnings from invalid HTML.

        // Check that each element is present in the HTML.
        $this->assertNotNull($dom->getElementById('homework-chooser-modal'), 'Modal container is missing');
        $this->assertNotNull($dom->getElementById('inputField'), 'Input field is missing');
        $this->assertNotNull($dom->getElementById('option1'), 'Option 1 radio button is missing');
        $this->assertNotNull($dom->getElementById('option2'), 'Option 2 radio button is missing');
        $this->assertNotNull($dom->getElementById('option3'), 'Option 2 radio button is missing');
        $this->assertNotNull($dom->getElementById('page-range-input'), 'Page range input container is missing');
        $this->assertNotNull($dom->getElementById('startPage'), 'Start page input is missing');
        $this->assertNotNull($dom->getElementById('endPage'), 'End page input is missing');
        $this->assertNotNull($dom->getElementById('video-range-input'), 'Video range input container is missing');
        $this->assertNotNull($dom->getElementById('startTime'), 'Start time input is missing');
        $this->assertNotNull($dom->getElementById('endTime'), 'End time input is missing');
        $this->assertNotNull($dom->getElementById('linkDiv'), 'Link div is missing');
        $this->assertNotNull($dom->getElementById('link'), 'Link input field is missing');
        $this->assertNotNull($dom->getElementById('dropzone-pdf-container'), 'Container dropzone pdf is missing');
        $this->assertNotNull($dom->getElementById('dropzone-video-container'), 'Container dropzone video is missing');
    }
}
