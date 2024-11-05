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
 * PHPUnit test case for mod_homework's save homework functionality.
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_homework;

use advanced_testcase;
use dml_exception;

/**
 *
 */
final class save_homework_test extends advanced_testcase
{
    /**
     * Setup routine before running each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetAfterTest(true); // Reset the Moodle environment after each test.
    }

    /**
     * Test saving literature homework with page range.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\save_homework_literature
     */
    public function test_save_homework_literature(): void
    {
        global $DB;

        // Call the external class method.
        $inputfield = 'Test Literature';
        $startpage = 1;
        $endpage = 10;
        $homework = 1;

        $result = \mod_homework\external\save_homework_literature::execute($inputfield, $startpage, $endpage, $homework);

        // Assert that the status is 'success'.
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Data saved successfully', $result['message']);

        // Verify that the data was saved in the database.
        $record = $DB->get_record_select(
            'homework_literature',
            $DB->sql_compare_text('description') . ' = :description',
            ['description' => $inputfield],
            '*',
            MUST_EXIST
        );
        $this->assertEquals($startpage, $record->startpage);
        $this->assertEquals($endpage, $record->endpage);
        $this->assertEquals($homework, $record->homework);
    }

    /**
     * Test saving a homework with a link.
     *
     * @runInSeparateProcess
     * @throws dml_exception
     * @covers :: \mod_homework\external\save_homework_link
     */
    public function test_save_homework_link(): void
    {
        global $DB;

        // Call the external class method.
        $inputfield = 'Test Link';
        $link = 'https://www.test.com';
        $homework = 1;

        $result = \mod_homework\external\save_homework_link::execute($inputfield, $link, $homework);

        // Assert that the status is 'success'.
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Data saved successfully', $result['message']);

        // Verify that the data was saved in the database.
        $record = $DB->get_record_select(
            'homework_links',
            $DB->sql_compare_text('description') . ' = :description',
            ['description' => $inputfield],
            '*',
            MUST_EXIST
        );
        $this->assertEquals($link, $record->link);
        $this->assertEquals($homework, $record->homework);
    }
}
