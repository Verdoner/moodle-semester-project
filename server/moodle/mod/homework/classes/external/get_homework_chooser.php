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
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework\external;
defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

use core\exception\coding_exception;
use core\output\mustache_engine;

/**
 *
 */
class get_homework_chooser extends external_api {
    /**
     *
     * @return external_function_parameters Is a definition of the functions parameter type and a description of it.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course Module ID'),
        ]);
    }

    /**
     * The logic making the custom html for modal client-side
     * @param $cmid - The current modules id
     * @return string[] - The html to be shown client-side
     */
    public static function execute() : array {
        $mustache = new Mustache_Engine();

        $templatepath = __DIR__ . "/../../templates/get_homework_chooser.mustache";
        if (!file_exists($templatepath)) {
            throw new coding_exception("Template file does not exist: " . $templatepath);
        }
        $templatecontent = file_get_contents($templatepath);

        return ['html' => $mustache->render($templatecontent)];
    }

    /**
     *
     * @return external_single_structure - Is a definition of the functions return type and a description of it
     */
    public static function execute_returns() : external_single_structure {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal'),
        ]);
    }
}
