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

use coding_exception;
use core_external\external_api;
use dml_exception;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
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
        return new external_function_parameters([]);
    }

    /**
     * Generates the custom HTML for the homework chooser modal.
     *
     * @param int $homeworkID The ID of the homework item
     * @return string[] - The HTML to be shown client-side
     * @throws dml_exception|JsonException
     */
    public static function execute(): array {
        global $DB, $USER;

        // The weight indicates the number of minutes after which the user's reading speed will be prioritized over the average.
        $weight = 180;
        // The global reading speed in minutes.
        $globalreadingspeed = 0.5;

        $sql = "
            SELECT c.*, hm.startpage, hm.endpage
            FROM {completions} c
            LEFT JOIN {homework_materials} hm ON c.material_id = hm.id
            WHERE c.usermodified = :userid
        ";
        $params = ['userid' => $USER->id];
        $records = $DB->get_records_sql($sql, $params);

        $availablematerials = $DB->get_records('homework_materials');

        $totalminutes = 0;
        $totalreadingtime = 0;
        $totalpages = 0;
        $totaldays = 0;

        foreach ($records as $record) {

            // Timestamps are in seconds, so we get the day difference by dividing by seconds per day.
            // Use the time from the first homework completion as the start time for these stats.
            $totaldays = floor(time() - ($record->timecreated) / 86400);

            $totalminutes += $record->timetaken;

            $startpage = $record->startpage;
            $endpage = $record->endpage;

            if ($startpage != null && $endpage != null) {
                $totalpages += $endpage - $startpage;
                $totalreadingtime += $record->timetaken;
            }
        }
        $weightedreadingspeed = $globalreadingspeed;
        $timeperday = 0;
        if ($totaldays != 0) {
            $timeperday = $totalminutes / $totaldays;
        }

        if ($totalpages != 0) {
            $readingspeed = $totalreadingtime / $totalpages;
            // The reading speed is weighted. When no pages have been read, it will be the global average a page per minute.
            // Once the number of minutes reaches the weight, the user's speed will be weighted more than the average.
            $weightedreadingspeed = $globalreadingspeed + ($readingspeed - $globalreadingspeed) * $totalminutes / ($totalminutes + $weight);
        }

        $percentcompleted = 0;
        if (count($records) && count($availablematerials)) {
            $percentcompleted = count($records) / count($availablematerials) * 100;
        }

        $mustache = new Mustache_Engine();

        // Prepare data for the template.
        $content = [
            'weightedreadingspeed' => strip_tags($weightedreadingspeed),
            'percentcompleted' => strip_tags($percentcompleted),
            'timeperday' => strip_tags($timeperday),

        ];

        $html = $mustache->render(file_get_contents(__DIR__ . "/../../templates/stats.mustache"), $content);

        return ['html' => $html];
    }

    /**
     * Returns the structure of the function's response.
     * @return external_single_structure - Definition of the function's return type and description
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'html' => new external_value(PARAM_TEXT, 'modal thml'),
        ]);
    }
}
