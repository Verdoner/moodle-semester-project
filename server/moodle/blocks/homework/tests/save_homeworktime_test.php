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

namespace block_homework;

use advanced_testcase;
use block_homework\external\save_homeworktime;

/**
 * Test for the external function saving time taken for homework
 * @copyright group 1
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class save_homeworktime_test extends advanced_testcase {
    /**
     * Test the save_homeworktime execute function
     * @throws \dml_exception
     * @covers :: \block_homework\external\save_homeworktime
     */
    public function test_save_timeindb(): void {
        global $DB;

        // Reset the database after each test.
        $this->resetAfterTest();

        // Prepare test data.
        $user = $this->getDataGenerator()->create_user();
        $userid = $user->id;

        $timecompletedliterature = [
            ['id' => 1, 'time' => 30],
            ['id' => 2, 'time' => 45],
        ];

        $timecompletedlinks = [
            ['id' => 3, 'time' => 20],
            ['id' => 4, 'time' => 35],
        ];

        $timecompletedvideos = [
            ['id' => 5, 'time' => 50],
            ['id' => 6, 'time' => 25],
        ];

        // Execute the external function.
        $result = save_homeworktime::execute($userid, $timecompletedliterature, $timecompletedlinks, $timecompletedvideos);

        // Check that the return structure and values are as expected.
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Data saved successfully', $result['message']);

        // Verify data has been saved in the database for literature.
        foreach ($timecompletedliterature as $item) {
            $this->assertRecordExists('completions', [
                'user_id' => $userid,
                'literature_id' => $item['id'],
                'time_taken' => $item['time'],
            ]);
        }

        // Verify data has been saved in the database for links.
        foreach ($timecompletedlinks as $item) {
            $this->assertRecordExists('completions', [
                'user_id' => $userid,
                'link_id' => $item['id'],
                'time_taken' => $item['time'],
            ]);
        }

        // Verify data has been saved in the database for videos.
        foreach ($timecompletedvideos as $item) {
            $this->assertRecordExists('completions', [
                'user_id' => $userid,
                'video_id' => $item['id'],
                'time_taken' => $item['time'],
            ]);
        }
    }

    /**
     * Helper function to check if a record exists in the database.
     *
     * @param string $table The database table name.
     * @param array $conditions Array of conditions to match.
     * @throws \dml_exception
     */
    private function assertrecordexists(string $table, array $conditions): void {
        global $DB;
        $exists = $DB->record_exists($table, $conditions);
        $this->assertTrue($exists, 'Record does not exist in ' . $table . ' table for conditions: ' . json_encode($conditions));
    }
}
