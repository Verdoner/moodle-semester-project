<?php


/**
 * PHPUnit test case for mod_homework's save homework functionality.
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework\tests;

use advanced_testcase;

class saveHomework_test extends advanced_testcase {

    /**
     * Setup routine before running each test.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true); // Reset the Moodle environment after each test.
    }

    /**
     * Test saving literature homework with page range.
     * @runInSeparateProcess
     */

    public function test_save_homework_literature() {
        global $DB;

        // Call the external class method.
        $inputfield = 'Test Literature';
        $startpage = 1;
        $endpage = 10;

        $result = \mod_homework\external\save_homework_literature::execute($inputfield, $startpage, $endpage);

        // Assert that the status is 'success'
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Data saved successfully', $result['message']);

        // Verify that the data was saved in the database
        $record = $DB->get_record_select(
            'homework_literature',
            $DB->sql_compare_text('description') . ' = :description',
            ['description' => $inputfield],
            '*',
            MUST_EXIST
        );
        $this->assertEquals($startpage, $record->startpage);
        $this->assertEquals($endpage, $record->endpage);
    }

    /**
     * Test saving a homework with a link.
     * @runInSeparateProcess
     */
    public function test_save_homework_link() {
        global $DB;

        // Call the external class method.
        $inputfield = 'Test Link';
        $link = 'https://www.test.com';

        $result = \mod_homework\external\save_homework_link::execute($inputfield, $link);

        // Assert that the status is 'success'
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Data saved successfully', $result['message']);

        // Verify that the data was saved in the database
        $record = $DB->get_record_select(
            'homework_links',
            $DB->sql_compare_text('description') . ' = :description',
            ['description' => $inputfield],
            '*',
            MUST_EXIST
        );
        $this->assertEquals($link, $record->link);
    }
}
