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

        // Data for literature.
        $dataliterature = [
            [
                'description' => 'Math homework on integrals',
                'endpage' => 10,
                'homework_id' => $homeworkid,
                'id' => 1,
                'introformat' => 1,
                'startpage' => 1,
                'timecreated' => strtotime('2023-10-01 10:00:00'),
                'timemodified' => strtotime('2023-10-02 12:00:00'),
                'usermodified' => 3,
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
                'usermodified' => 4,
            ],
        ];

        // Data for links.
        $datalinks = [
            [
                'description' => 'Project guidelines',
                'link' => 'https://example.com/guidelines',
                'homework_id' => $homeworkid,
                'id' => 3,
                'timecreated' => strtotime('2023-10-01 10:00:00'),
                'timemodified' => strtotime('2023-10-02 12:00:00'),
                'usermodified' => 5,
            ],
            [
                'description' => 'Reference materials',
                'link' => 'https://example.com/references',
                'homework_id' => $homeworkid,
                'id' => 4,
                'timecreated' => strtotime('2023-10-03 09:00:00'),
                'timemodified' => strtotime('2023-10-04 14:00:00'),
                'usermodified' => 6,
            ],
        ];

        // Data for videos.
        $datavideos = [
            [
                'description' => 'Presentation for math homework',
                'endtime' => 10,
                'homework_id' => $homeworkid,
                'fileid' => 501,
                'id' => 5,
                'introformat' => 1,
                'starttime' => 1,
                'timecreated' => strtotime('2023-10-01 10:00:00'),
                'timemodified' => strtotime('2023-10-02 12:00:00'),
                'usermodified' => 7,
            ],
            [
                'description' => 'Presentation for science project',
                'endtime' => 15,
                'homework_id' => $homeworkid,
                'fileid' => 502,
                'id' => 6,
                'introformat' => 1,
                'starttime' => 11,
                'timecreated' => strtotime('2023-10-05 11:00:00'),
                'timemodified' => strtotime('2023-10-06 13:00:00'),
                'usermodified' => 8,
            ],
        ];

        // Call the external function directly.
        $resultliterature = \block_homework\external\get_infohomework_modal::execute($homeworkid, $dataliterature);
        $resultlinks = \block_homework\external\get_infohomework_modal::execute($homeworkid, $datalinks);
        $resultvideos = \block_homework\external\get_infohomework_modal::execute($homeworkid, $datavideos);

        // Verify that the result contains the expected HTML structure.
        $this->assertNotEmpty($resultliterature);
        $this->assertArrayHasKey('html', $resultliterature);

        $this->assertNotEmpty($resultlinks);
        $this->assertArrayHasKey('html', $resultlinks);

        $this->assertNotEmpty($resultvideos);
        $this->assertArrayHasKey('html', $resultvideos);

        // Parse the HTML using DOMDocument to check for the required elements.
        $domliterature = new DOMDocument();
        @$domliterature->loadHTML($resultliterature['html']);  // Suppressing warnings from invalid HTML.

        $domlinks = new DOMDocument();
        @$domlinks->loadHTML($resultlinks['html']);  // Suppressing warnings from invalid HTML.

        $domvideos = new DOMDocument();
        @$domvideos->loadHTML($resultvideos['html']);  // Suppressing warnings from invalid HTML.

        // Check that each element is present in the HTML for literature.
        $this->assertNotNull($domliterature->getElementById('info-homework-modal'), 'Modal container is missing');
        $modaltitleliterature = $domliterature->getElementsByTagName('h1')->item(0);
        $this->assertEquals('Mark homework completed', $modaltitleliterature->textContent, 'Modal title is incorrect');
        $xpathliterature = new \DOMXPath($domliterature);
        $this->assertNotNull($domliterature->getElementById('materials-1'));
        $this->assertNotNull($domliterature->getElementById('materials-2'));

        // Check that each element is present in the HTML for links.
        $this->assertNotNull($domlinks->getElementById('info-homework-modal'), 'Modal container is missing');
        $modaltitlelinks = $domlinks->getElementsByTagName('h1')->item(0);
        $this->assertEquals('Mark homework completed', $modaltitlelinks->textContent, 'Modal title is incorrect');
        $xpathlinks = new \DOMXPath($domlinks);
        $this->assertNotNull($domlinks->getElementById('materials-3'));
        $this->assertNotNull($domlinks->getElementById('materials-4'));

        // Check that each element is present in the HTML for videos.
        $this->assertNotNull($domvideos->getElementById('info-homework-modal'), 'Modal container is missing');
        $modaltitlevideos = $domvideos->getElementsByTagName('h1')->item(0);
        $this->assertEquals('Mark homework completed', $modaltitlevideos->textContent, 'Modal title is incorrect');
        $xpathvideos = new \DOMXPath($domvideos);
        $this->assertNotNull($domvideos->getElementById('materials-5'));
        $this->assertNotNull($domvideos->getElementById('materials-6'));

        // Check for input with specific attributes & their labels for literature.
        $literaturelabel1 = $xpathliterature->query('//div[@id="materials-1"]//h3')->item(0);
        $this->assertEquals('Math homework on integrals', $literaturelabel1->textContent, 'Modal title is incorrect');
        $literatureinput1 = $xpathliterature->query("//input[@class='homework-time'][@id='1'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $literatureinput1->length, 'Expected input with class \'homework-time-literature\'');
        $literaturelabel2 = $xpathliterature->query('//div[@id="materials-2"]//h3')->item(0);
        $this->assertEquals('Science project on climate change', $literaturelabel2->textContent, 'Modal title is incorrect');
        $literatureinput2 = $xpathliterature->query("//input[@class='homework-time'][@id='2'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $literatureinput2->length, 'Expected input with class \'homework-time-literature\'');

        // Check for input with specific attributes & their labels for links.
        $linkslabel1 = $xpathlinks->query('//div[@id="materials-3"]//h3')->item(0);
        $this->assertEquals('Project guidelines', $linkslabel1->textContent, 'Modal title is incorrect');
        $linksinput1 = $xpathlinks->query("//input[@class='homework-time'][@id='3'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $linksinput1->length, 'Expected input with class \'homework-time-links\'');
        $linkslabel2 = $xpathlinks->query('//div[@id="materials-4"]//h3')->item(0);
        $this->assertEquals('Reference materials', $linkslabel2->textContent, 'Modal title is incorrect');
        $linksinput2 = $xpathlinks->query("//input[@class='homework-time'][@id='4'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $linksinput2->length, 'Expected input with class \'homework-time-links\'');

        // Check for input with specific attributes & their labels for videos.
        $videoslabel1 = $xpathvideos->query('//div[@id="materials-5"]//h3')->item(0);
        $this->assertEquals('Presentation for math homework', $videoslabel1->textContent, 'Modal title is incorrect');
        $videosinput1 = $xpathvideos->query("//input[@class='homework-time'][@id='5'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $videosinput1->length, 'Expected input with class \'homework-time-videos\'');
        $videoslabel2 = $xpathvideos->query('//div[@id="materials-6"]//h3')->item(0);
        $this->assertEquals('Presentation for science project', $videoslabel2->textContent, 'Modal title is incorrect');
        $videosinput2 = $xpathvideos->query("//input[@class='homework-time'][@id='6'][@name='homework-time'][@min='1']");
        $this->assertEquals(1, $videosinput2->length, 'Expected input with class \'homework-time-videos\'');
    }
}
