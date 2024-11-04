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

namespace block_homework\external;

defined('MOODLE_INTERNAL') || die();
global $CFG;
// require_once("$CFG->libdir/externallib.php");

use core_external\external_api;
use external_function_parameters;
use external_multiple_structure;
use external_value;
use external_single_structure;
class save_homeworktime extends external_api {
    /**
     * Use the official Moodle execute_parameters syntax to set up the parameters as a user id and 3 arrays of ID and time.
     * @return external_function_parameters Returns the parameters with the correct strucutre.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'user' => new external_value(PARAM_INT, 'user id'),
            'timeCompletedLiterature' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'literature id'),
                'time' => new external_value(PARAM_INT, 'time'),
            ])),
            'timeCompletedLinks' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'link id'),
                'time' => new external_value(PARAM_INT, 'time'),
            ])),
            'timeCompletedVideos' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'video id'),
                'time' => new external_value(PARAM_INT, 'time'),
            ])),

        ]);
    }

    /**
     * @param $user ID of currently logged in user.
     * @param $timecompletedliterature Array of objects containing an ID and a time.
     * @param $timecompletedlinks Array of objects containing an ID and a time.
     * @param $timecompletedvideos Array of objects containing an ID and a time.
     * @return string[] Returns a success message if successful
     * @throws \dml_exception On error, throws a dml exception as per Moodle standards
     */
    public static function execute($user, $timecompletedliterature, $timecompletedlinks, $timecompletedvideos) {
        global $DB;
        // Handle the input field value here.
        // For example, save to a database.
        // For each completed literature material, add the time taken and ID to a new completion.
        foreach ($timecompletedliterature as $currtimecompletedliterature) {
            $record = new \stdClass();
            $record->user_id = $user;
            $record->literature_id = $currtimecompletedliterature['id'];
            $record->time_taken = $currtimecompletedliterature['time'];
            $DB->insert_record('completions', $record);
        }

        // For each completed link material, add the time taken and ID to a new completion.
        foreach ($timecompletedlinks as $timecompletedlink) {
            $record = new \stdClass();
            $record->user_id = $user;
            $record->link_id = $timecompletedlink['id'];
            $record->time_taken = $timecompletedlink['time'];
            $DB->insert_record('completions', $record);
        }

        // For each completed video material, add the time taken and ID to a new completion.
        foreach ($timecompletedvideos as $timecompletedvideo) {
            $record = new \stdClass();
            $record->user_id = $user;
            $record->video_id = $timecompletedvideo['id'];
            $record->time_taken = $timecompletedvideo['time'];
            $DB->insert_record('completions', $record);
        }

        // Return a success response.
        return ['status' => 'success', 'message' => 'Data saved successfully'];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status of the request'),
            'message' => new external_value(PARAM_TEXT, 'Message with details about the request status'),
        ]);
    }
}
