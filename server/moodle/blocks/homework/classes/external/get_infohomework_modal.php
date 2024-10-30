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
require_once("$CFG->libdir/externallib.php");

use core_external\external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

class get_infohomework_modal extends external_api {
    /**
     * Returns the parameters for the execute function.
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'homework_id' => new external_value(PARAM_INT, 'The ID of the homework item'),
        ]);
    }

    /**
     * Generates the custom HTML for the homework chooser modal.
     * @param int $homework_id The ID of the homework item
     * @return string[] - The HTML to be shown client-side
     */
    public static function execute($homeworkid) {
        global $DB;

        // Optionally, you can fetch data from the database using $homework_id if needed.

        // Custom HTML for the homework chooser modal.
        $html = '
            <div id="info-homework-modal">
                <h1>Mark homework completed</h1>
                <p>Homework ID: ' . $homeworkid . '</p>
                <!-- Add additional content or buttons here -->
            </div>
        ';

        return ['html' => $html];
    }

    /**
     * Returns the structure of the function's response.
     * @return external_single_structure - Definition of the function's return type and description
     */
    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal'),
        ]);
    }
}
