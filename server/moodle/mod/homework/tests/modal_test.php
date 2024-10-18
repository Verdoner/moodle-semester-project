<?php

/**
 * homework/tests/modalTest.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework\tests;

use advanced_testcase;
use DOMDocument;

class modal_test extends advanced_testcase {

    /**
     * @return void
     * @throws coding_exception
     *@runInSeparateProcess
     */
    public function test_get_homework_chooser() {
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
        $this->assertNotNull($dom->getElementById('page-range-input'), 'Page range input container is missing');
        $this->assertNotNull($dom->getElementById('startPage'), 'Start page input is missing');
        $this->assertNotNull($dom->getElementById('endPage'), 'End page input is missing');
        $this->assertNotNull($dom->getElementById('linkDiv'), 'Link div is missing');
        $this->assertNotNull($dom->getElementById('link'), 'Link input field is missing');
    }
}
