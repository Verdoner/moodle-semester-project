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
 * homework/classes/external/save_homework_video.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 *
 */
class save_homework_video extends \external_api {
    /**
     *
     * @return external_function_parameters Define the parameters expected by this function.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'inputfield' => new external_value(PARAM_TEXT, 'Input field value'),
            'instance' => new external_value(PARAM_INT, 'Instance field value'),
            'fileid' => new external_value(PARAM_INT, 'Uploaded file ID', VALUE_OPTIONAL),
        ]);
    }

    /**
     * The main function to handle the request.
     * @param $inputfield
     * @param $instance
     * @param $fileid
     * @return string[]
     * @throws \dml_exception
     */
    public static function execute($inputfield, $instance, $fileid = null) {
        global $DB, $USER;

        $record = new \stdClass();
        $filesrecord = new \stdClass();

        $record->userid = $USER->id;
        $record->description = $inputfield;
        $record->homework_id = $instance;
        if ($fileid) {
            $record->fileid = $fileid;

            $filesrecord->files_id = $fileid;
            $filesrecord->homework_id = $instance;

            try {
                $DB->insert_record('files_homework', $filesrecord);
            } catch (\dml_exception $e) {
                debugging("Error inserting into files_homework: " . $e->getMessage(), DEBUG_DEVELOPER);
                return ['status' => 'error', 'message' => 'Failed to save file record'];
            }
        }
        $record->timecreated = time();
        $record->timemodified = time();

        try {
            $DB->insert_record('homework_video', $record);
        } catch (\dml_exception $e) {
            debugging("Error inserting into homework_video: " . $e->getMessage(), DEBUG_DEVELOPER);
            return ['status' => 'error', 'message' => 'Failed to save homework record'];
        }

        // Return a success response.
        return ['status' => 'success', 'message' => 'Data saved successfully'];
    }

    /**
     *
     * @return external_single_structure Define the return values.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status of the request'),
            'message' => new external_value(PARAM_TEXT, 'Message with details about the request status'),
        ]);
    }
}
