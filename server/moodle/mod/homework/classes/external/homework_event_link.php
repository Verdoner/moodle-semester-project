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
 * homework/classes/external/homework_event_link.php
 *
 * @package   mod_homework
 * @copyright 2024
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_homework\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use dml_exception;
use invalid_parameter_exception;


/**
 * Class for linking homework and events.
 */
class homework_event_link extends external_api {
    /**
     *
     * @return external_function_parameters Is a definition of the functions parameter type and a description of it.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'homeworkid' => new external_value(PARAM_INT, 'homework ID'),
            'course_module_id' => new external_value(PARAM_INT, 'Course module ID'),
            'eventid' => new external_value(PARAM_INT, 'Event ID'),
        ]);
    }

    /**
     * The function called when the client wants to link homework.
     * @param $homeworkid
     * @param $coursemoduleid
     * @param $eventid
     * @return string[] - The html to be shown client-side
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function execute($homeworkid, $coursemoduleid, $eventid) {
        // Validate the parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'homeworkid' => $homeworkid,
            'course_module_id' => $coursemoduleid,
            'eventid' => $eventid,
        ]);

        global $DB;
        // Get the relevant data for the homework.
        $homework = $DB->get_record('homework', ['id' => $params['homeworkid']]);
        $homework->course_module_id = $params['course_module_id'];
        $homework->eventid = $params['eventid'];

        // Update the homework with the new event data.
        try {
            $DB->update_record('homework', $homework);
        } catch (dml_exception $e) {
            return ['status' => 'error', 'message' => 'Failed to add homework event link due to ' . $e];
        }

        return ['status' => 'success', 'message' => 'Success'];
    }

    /**
     * Handle what will be returned to the client.
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
