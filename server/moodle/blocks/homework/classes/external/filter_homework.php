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
 * homework/classes/external/get_homework_chooser.php
 * A class defining an external API function
 *
 * @package   block_homework
 * @copyright 2024, cs-24-sw-5-13 <cs-24-sw-5-13@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace block_homework\external;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/externallib.php");

use coding_exception;
use dml_exception;
use external_function_parameters;
use external_value;
use external_single_structure;
use JsonException;

/**
 * Class used to filter homework on block plugin.
 */
class filter_homework extends \external_api {
    /**
     *
     * @return external_function_parameters Is a definition of the functions parameter type and a description of it.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'filter' => new external_value(PARAM_TEXT, 'Filtering parameter'),
        ]);
    }

    /**
     * The logic making the custom html for modal client-side
     * @param $filter - The current modules id
     * @return array - The html to be shown client-side
     * @throws JsonException|dml_exception
     */
    public static function execute($filter) {
        global $DB, $USER;
        $usercourses = enrol_get_users_courses($USER->id, true);
        $homeworkarray = [];
        foreach ($usercourses as $course) {
            $homeworkrecords = $DB->get_records('homework', ['course_id' => $course->id]);
            foreach ($homeworkrecords as $homework) {
                $homeworkarray[] = [
                    'id' => $homework->id,
                    'name' => $homework->name,
                    'intro' => strip_tags($homework->intro),
                    'duedate' => date('d-m-y', $homework->duedate),
                    'time' => $homework->duedate,
                    'course' => $course->fullname,
                ];
            }
        }
        $returnarray = self::filter($filter, $homeworkarray);
        return ["homework" => json_encode($returnarray, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR];
    }

    /**
     *
     * @return external_single_structure an array of homework
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'homework' => new external_value(PARAM_TEXT, 'Data array of homework'),
        ]);
    }

    /**
     *
     * @param $filter - The current modules id
     * @param $homeworkarray - Array containing all homework to be filtered
     * @return array - The html to be shown client-side
     */
    public static function filter($filter, $homeworkarray): array {
        $returnarray = [];
        switch ($filter) {
            case ("all"):
                return $homeworkarray;
            case ("current"):
                foreach ($homeworkarray as $homework) {
                    if ($homework["time"] > time()) {
                        $returnarray[] = $homework;
                    }
                }
                break;
            case ("previous"):
                foreach ($homeworkarray as $homework) {
                    if ($homework["time"] < time()) {
                        $returnarray[] = $homework;
                    }
                }
                break;
            default:
                foreach ($homeworkarray as $homework) {
                    if ($homework["course"] == $filter) {
                        $returnarray[] = $homework;
                    }
                }
        }
        return $returnarray;
    }
}
