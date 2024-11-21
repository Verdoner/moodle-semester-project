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
use core_external\external_api;
use dml_exception;
use external_function_parameters;
use external_value;
use external_single_structure;
use JsonException;

class get_courses extends \external_api {

    /**
     * @return external_function_parameters
     */
    public static function execute_parameters(){
        return new external_function_parameters([]);
    }

    /**
     * @return array
     * @throws JsonException
     */
    public static function execute(){
        global $USER;
        $usercourses = enrol_get_users_courses($USER->id, true);

        return ["courses" => json_encode($usercourses, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR];
    }

    /**
     * @return external_single_structure
     */
    public static function execute_returns(){
        return new external_single_structure([
            'courses' => new external_value(PARAM_TEXT, 'Data array of courses'),
        ]);
    }
}