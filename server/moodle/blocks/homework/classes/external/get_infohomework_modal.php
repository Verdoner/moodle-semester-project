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
use external_multiple_structure;
use external_value;
use external_single_structure;

/**
 * The external function for requesting the modal for plugin.
 * @copyright group 1
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
    public static function execute($homework_id) {
        global $DB, $USER;
        $homework = $DB->get_record('homework', ['id' => $homework_id]);
        $course = $DB->get_record('course', ['id' => $DB->get_field('homework', 'course', ['id' => $homework_id])]);
        $literaturearray = $DB->get_records('homework_literature', ['homework' => $homework->id]);
        $linksarray = $DB->get_records('homework_links', ['homework' => $homework->id]);
        $videosarray = $DB->get_records('homework_video', ['homework' => $homework->id]);
        $completedmaterials = $DB->get_records('completions', ['user_id' => $USER->id]);
        return self::get_info($homework, $course, $literaturearray, $linksarray, $videosarray, $completedmaterials);
    }

    /**
     * Returns the structure of the function's response.
     * @return external_single_structure - Definition of the function's return type and description
     */
    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal'),
            'title' => new external_value(PARAM_TEXT, 'Title of the homework'),
            'course' => new external_value(PARAM_TEXT, 'Course name'),
            'duedate' => new external_value(PARAM_TEXT, 'Due date of the homework'),
            'courseurl' => new external_value(PARAM_TEXT, 'The URL for the course'),
            'homeworkurl' => new external_value(PARAM_TEXT, 'The URl for the homework'),

        ]);
    }

    public static function get_info($homework, $course, $literaturearray, $linksarray, $videosarray, $completedmaterials)
    {
        // Assuming you have the Mustache engine set up.
        $mustache = new \Mustache_Engine();
        $nohomework = "";


        foreach ($completedmaterials as $completedmaterial) {
            foreach ($literaturearray as $index => $literature) {
                $literaturearray[$index] = json_decode(json_encode($literature), true);
                if ($completedmaterial->literature_id == $literaturearray[$index]["id"]) {
                    unset($literaturearray[$index]);
                }
            }
            foreach ($linksarray as $index => $link) {
                $linksarray[$index] = json_decode(json_encode($link), true);
                if ($completedmaterial->link_id == $linksarray[$index]["id"]) {
                    unset($linksarray[$index]);
                }
            }
            foreach ($videosarray as $index => $video) {
                $videosarray[$index] = json_decode(json_encode($video), true);
                if ($completedmaterial->video_id == $videosarray[$index]["id"]) {
                    unset($videosarray[$index]);
                }
            }
        }
        $literaturearray = array_values($literaturearray);
        $linksarray = array_values($linksarray);
        $videosarray = array_values($videosarray);

        if (count($literaturearray) == 0 && count($linksarray) == 0 && count($videosarray) == 0) {
            $nohomework = "All completed";
        }
        // Prepare data for the template.
        $content = [
            'nohomework' => $nohomework,
            'homeworkdescription' => strip_tags($homework->intro),
            'literature' => $literaturearray,
            'links' => $linksarray,
            'videos' => $videosarray,
        ];

        // Render the template
        $html = $mustache->render(file_get_contents(__DIR__ . "/../../templates/timeinfotemplate.mustache"), $content);

        $duedate = date('H:i d-m-Y', $homework->duedate);
        $courseurl = "/course/view.php?id=".$course->id;
        $homeworkurl = "/mod/homework/view.php?id=".$homework->id;

        return ['html' => $html, 'title' => $homework->name, 'course' => $course->fullname,
            'duedate' => $duedate, 'courseurl' => $courseurl, 'homeworkurl' => $homeworkurl];
    }
}
