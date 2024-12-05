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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

use coding_exception;
use dml_exception;
use JsonException;
use Mustache_Engine;

/**
 * The external function for requesting the modal for plugin.
 * @copyright group 1
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_stats_modal extends external_api {
    /**
     * Returns the parameters for the execute function.
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'stats' => new external_single_structure([
                'timeperday' => new external_value(PARAM_FLOAT, 'Time spent on homework per day'),
                'weightedreadingspeed' => new external_value(PARAM_FLOAT, 'Weighted reading speed average'),
                'percentcompleted' => new external_value(PARAM_FLOAT, 'Percent of homework completed'),
            ]),
        ]);
    }

    /**
     * Generates the custom HTML for the stats modal.
     *
     * @param string[] $stats The stats array
     * @return string[] - The HTML to be shown client-side
     * @throws dml_exception|JsonException
     */
    public static function execute($stats): array {
        $mustache = new Mustache_Engine();

        // Prepare data for the template.
        $content = [
            'weightedreadingspeed' => round($stats['weightedreadingspeed'], 2),
            'percentcompleted' => round($stats['percentcompleted'], 2),
            'timeperday' => round($stats['timeperday'], 2),
        ];

        $templatepath = __DIR__ . "/../../templates/stats.mustache";
        if (!file_exists($templatepath)) {
            throw new coding_exception("Template file does not exist: " . $templatepath);
        }

        $templatecontent = file_get_contents($templatepath);
        if (!$templatecontent) {
            throw new coding_exception("Template file is empty or could not be read: " . $templatepath);
        }

        $html = $mustache->render($templatecontent, $content);

        return [
            'html' => $html,
        ];
    }

    /**
     * Returns the structure of the function's response.
     * @return external_single_structure - Definition of the function's return type and description
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the stats modal'),
        ]);
    }
}
