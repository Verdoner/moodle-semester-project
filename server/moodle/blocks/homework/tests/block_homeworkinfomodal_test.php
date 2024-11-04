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
 * @package   block_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace block_homework;

use advanced_testcase;
use core\exception\coding_exception;
use DOMDocument;

/**
 * Test for modal rendering.
 * @copyright group 1
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class block_homeworkinfomodal_test extends advanced_testcase {
    /**
     *
     * @return void
     * @throws coding_exception
     * @runInSeparateProcess
     * @covers :: \block_homework\external\get_infohomework_modal
     */
    public function test_get_homeworkinfo_modal(): void {
        global $DB;

        // Set up necessary data for the test, such as course and module.
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $homework = $this->getDataGenerator()->get_plugin_generator('block_homework')->create_instance(['course' => $course->id]);
        $homeworkid = $homework->id;
        // Data1.
        $data1 = [
            [
                'description' => 'Math homework on integrals',
                'endpage' => 10,
                'homework_id' => $homeworkid,
                'id' => 1,
                'introformat' => 1,
                'startpage' => 1,
                'timecreated' => strtotime('2023-10-01 10:00:00'),
                'timemodified' => strtotime('2023-10-02 12:00:00'),
            ],
            [
                'description' => 'Science project on climate change',
                'endpage' => 15,
                'homework_id' => $homeworkid,
                'id' => 2,
                'introformat' => 1,
                'startpage' => 11,
                'timecreated' => strtotime('2023-10-05 11:00:00'),
                'timemodified' => strtotime('2023-10-06 13:00:00'),
            ],
        ];

        // Data2.
        $data2 = [
            [
                'description' => 'Project guidelines',
                'link' => 'http://example.com/guidelines',
                'homework_id' => $homeworkid,
                'id' => 1,
                'timecreated' => strtotime('2023-10-01 10:00:00'),
                'timemodified' => strtotime('2023-10-02 12:00:00'),
                'usermodified' => 5,
            ],
            [
                'description' => 'Reference materials',
                'link' => 'http://example.com/references',
                'homework_id' => $homeworkid,
                'id' => 2,
                'timecreated' => strtotime('2023-10-03 09:00:00'),
                'timemodified' => strtotime('2023-10-04 14:00:00'),
                'usermodified' => 6,
            ],
        ];

        // Data3.
        $data3 = [
            [
                'description' => 'Presentation for math homework',
                'homework_id' => $homeworkid,
                'fileid' => 501,
                'id' => 1,
                'introformat' => 1,
                'timecreated' => strtotime('2023-10-01 10:00:00'),
                'timemodified' => strtotime('2023-10-02 12:00:00'),
            ],
            [
                'description' => 'Presentation for science project',
                'homework_id' => $homeworkid,
                'fileid' => 502,
                'id' => 2,
                'introformat' => 1,
                'timecreated' => strtotime('2023-10-05 11:00:00'),
                'timemodified' => strtotime('2023-10-06 13:00:00'),
            ],
        ];

        // Call the external function directly.
        $result = \block_homework\external\get_infohomework_modal::execute($homeworkid, $data1, $data2, $data3);

        // Verify that the result contains the expected HTML structure.
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('html', $result);

        // Parse the HTML using DOMDocument to check for the required elements.
        $dom = new DOMDocument();
        @$dom->loadHTML($result['html']);  // Suppressing warnings from invalid HTML.

        // Check that each element is present in the HTML.
        $this->assertNotNull($dom->getElementById('info-homework-modal'), 'Modal container is missing');
        $modaltitle = $dom->getElementsByTagName('h1')->item(0);
        $this->assertEquals('Mark homework completed', $modaltitle->textContent, 'Modal title is incorrect');
        $xpath = new \DOMXPath($dom);
        $this->assertNotNull($dom->getElementById('literature-1'));
        // Check for input with specific attributes & their labels.
        $litlabel1 = $xpath->query('//div[@id="literature-1"]//h3')->item(0);
        $this->assertEquals('Math homework on integrals', $litlabel1->textContent, 'Modal title is incorrect');
        $input = $xpath->query("//input[@class='homework-time-literature'][@id='1'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $input->length, 'Expected input with class \'homework-time-literature\'');
        $litlabel1 = $xpath->query('//div[@id="literature-2"]//h3')->item(0);
        $this->assertEquals('Science project on climate change', $litlabel1->textContent, 'Modal title is incorrect');
        $input = $xpath->query("//input[@class='homework-time-literature'][@id='2'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $input->length, 'Expected input with class \'homework-time-literature\'');
        $this->assertNotNull($dom->getElementById('literature-1'));
        // Check for input with specific attributes & their labels for links.
        $litlabel1 = $xpath->query('//div[@id="links-1"]//h3')->item(0);
        $this->assertEquals('Project guidelines', $litlabel1->textContent, 'Modal title is incorrect');
        $input = $xpath->query("//input[@class='homework-time-links'][@id='1'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $input->length, 'Expected input with class \'homework-time-links\'');
        $litlabel1 = $xpath->query('//div[@id="links-2"]//h3')->item(0);
        $this->assertEquals('Reference materials', $litlabel1->textContent, 'Modal title is incorrect');
        $input = $xpath->query("//input[@class='homework-time-links'][@id='2'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $input->length, 'Expected input with class \'homework-time-links\'');
        $this->assertNotNull($dom->getElementById('literature-1'));
        // Check for input with specific attributes & their labels, for videos.
        $litlabel1 = $xpath->query('//div[@id="videos-1"]//h3')->item(0);
        $this->assertEquals('Presentation for math homework', $litlabel1->textContent, 'Modal title is incorrect');
        $input = $xpath->query("//input[@class='homework-time-videos'][@id='1'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $input->length, 'Expected input with class \'homework-time-videos\'');
        $litlabel1 = $xpath->query('//div[@id="videos-2"]//h3')->item(0);
        $this->assertEquals('Presentation for science project', $litlabel1->textContent, 'Modal title is incorrect');
        $input = $xpath->query("//input[@class='homework-time-videos'][@id='2'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $input->length, 'Expected input with class \'homework-time-videos\'');
    }
}
