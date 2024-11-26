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
 * homework/classes/external/get_events_for_homework.php
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

/**
 * Class for getting events for homework.
 */
class get_events_for_homework extends external_api {
    /**
     * Returns parameters homeworkid.
     *
     * @return external_function_parameters Define the parameters expected by this function.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'homeworkid' => new external_value(PARAM_INT, 'Homework ID'),
        ]);
    }
    /**
     * The main function to handle the request.
     *
     * @param $homeworkid
     * @return string[]
     * @throws dml_exception
     */
    public static function execute($homeworkid) {
        global $DB;
        // Get the homework data based on the id.
        $homework = $DB->get_record('homework', ['id' => $homeworkid]);

        // Initialize an empty html string.
        $html = '';

        // If the homework already has homework attached then send that event data back to the user.
        if ($homework->eventid != null) {
            $homeworkevent = $DB->get_record('event', ['id' => $homework->eventid]);
            $html .= 'This homework is already linked to Event: Name: ' . $homeworkevent->name . '<br> Time of the event: ' .
                date("Y-m-d H:i:s", $homeworkevent->timestart) . '<br>';
        }

        // Create an sql query that only gets events which are not already linked.
        $sql = 'SELECT * FROM {event} e
                    WHERE e.courseid = ?
                    AND e.id NOT IN (
                    SELECT h.eventid FROM {homework} h
                    WHERE h.eventid IS NOT NULL
                )';

        $events = $DB->get_records_sql($sql, [$homework->course_id]);

        // Check if there are any events. If not then send an empty html string which is handled by the js.
        if (count($events) > 0) {
            $html .= '<form id="evntlinkerform"">
                      <p>Chose an event to link</p>';
            foreach ($events as $event) {
                $html .= '<input type="radio" id="event' . $event->id . '" name="eventtolink" value="' . $event->id . '" required>
                      <label for="event' . $event->id . '">Name: ' . $event->name . ' Time of the event: '
                    . date("Y-m-d H:i:s", $event->timestart) . '</label><br>';
            }
            $html .= '</form>';
        } else {
            $html .= "There are no available events to link";
        }

        return ['events' => $html];
    }
    /**
     * Handle what will be returned to the client.
     *
     * @return external_single_structure Define the return values.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'events' => new external_value(PARAM_RAW, 'test'),
        ]);
    }
}
