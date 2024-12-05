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

defined('MOODLE_INTERNAL') || die();

// require for the pdf reader
require_once(__DIR__ . '/pdf_reader.php');
/**
 * Block definition class for the block_homework plugin.
 *
 * @package   block_homework
 * @copyright Year, You Name <your@email.address>
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_homework extends block_base {
    /**
     * Constructor for the block
     * @return void Saves the title as the correct title and nothing else
     * @throws coding_exception Moodle standard exception if error
     */
    public function init() {
        $this->title = get_string('homework', 'block_homework');
    }

    /**
     * Retrieves and prepares the content to be displayed by the block
     * @return stdClass|null
     */
    public function get_content() {

        global $OUTPUT, $DB, $USER;

        $stats = $this->getstats();

        // Fetch courses user is enrolled in.
        $usercourses = enrol_get_users_courses($USER->id, true);

        $homeworks = [];
        foreach ($usercourses as $course) {
            // Fetch homeworks using get_records_select.

            $tmp = $DB->get_records('homework', ['course_id' => $course->id]);
            foreach ($tmp as $tm) {
                $homeworks[] = $tm;
            }
        }

        $data = [];

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        // If the current page is a course then remove unrelated homework.
        if ($this->page->pagetype == 'course-view-topics') {
            $homeworks = $this->filter_homework_content($this->page->url, $homeworks);
        }

        // Retrieving all of the user's completions.
        $homeworkcompletionrecords = $DB->get_records('completions', ['usermodified' => $USER->id]);

        // Adding the details of each homework module to an associative array that will be pushed to the data array.
        foreach ($homeworks as $homework) {
            $tmp = [];
            $tmp['id'] = $homework->id;
            $tmp['name'] = $homework->name;
            $tmp['duedate'] = date('d-m-Y', $homework->duedate);
            $tmp['intro'] = strip_tags($homework->intro);
            $tmp['description'] = ($homework->description);
            $tmp['courseTitle'] = $DB->get_field('course', 'fullname', ['id' => $homework->course_id]);
            $tmp['expectedTime'] = 0;

            // Retrieving the records of all material of the current homework module.
            $materialrecords = $DB->get_records('homework_materials', ['homework_id' => $homework->id]);

            foreach ($materialrecords as $material) {
                if ($material->startime != null && $material->endtime != null) {
                    $tmp['expectedTime'] += ceil(($material->endtime - $material->starttime)/60);
                }
                if ($material->startpage != null && $material->endpage != null) {
                    $tmp['expectedTime'] += ceil(($material->endpage - $material->startpage) * $stats["weightedreadingspeed"]);
                }
            }

            $files = [];

            // Get ids of homeworkfiles.
            $fileids = [];

            $homeworkfiles = $DB->get_records('homework_materials', ['homework_id' => $homework->id]);
            foreach ($homeworkfiles as $homeworkfile) {
                array_push($fileids, $homeworkfile->file_id);
            }

            // Get file records.
            if (!empty($fileids)) {
                $filerecords = $DB->get_records_list('files', 'id', $fileids);
                $fs  = get_file_storage();
                foreach ($filerecords as $file) {
                    $contextid = $file->contextid;
                    $component = $file->component;
                    $filearea = $file->filearea;
                    $itemid = $file->itemid;
                    $filepath = $file->filepath;
                    $filename = $file->filename;

                    // Generate url.
                    $url = moodle_url::make_pluginfile_url(
                        $contextid,
                        $component,
                        $filearea,
                        $itemid,
                        $filepath,
                        $filename,
                        false
                    );

                    // Get appropriate icon for file type.
                    $iconurl = $OUTPUT->image_url(file_mimetype_icon($file->mimetype));

                    // Initialize time estimate as null
                    $timeestimate = null;

                    // Initialize average words read per minute
                    $averagewordsperminute = 220;

                    $file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);

                    // Check file type and get page count if it's a PDF or DOCX
                    if (str_ends_with(strtolower($filename), '.pdf')) {
                        // Initialize word count reader
                        $algorithm = new pdf_reader();

                        // Use word reading algorithm and save the value in wordcount
                        $wordcount = $algorithm->countwordsinpdf($file);

                        // Calculate the time estimate based on word count and average words per minute
                        $timeestimate = $wordcount / $averagewordsperminute;
                    }

                    $files[] = [
                        'fileurl' => $url->out(),
                        'filename' => $filename,
                        'iconurl' => $iconurl,
                        'timeestimate' => $timeestimate,
                    ];
                }
            }

            $tmp['files'] = $files;
            $tmp['materials'] = $materialrecords;
            $tmp['completions'] = $homeworkcompletionrecords;

            array_push($data, $tmp);
        }

        // Render the content using a template and pass the homework data to it.
        $this->content->text = $OUTPUT->render_from_template('block_homework/data', ['data' => $data]);
        // Include JavaScript functionality for scrolling behavior in the block.
        $this->page->requires->js_call_amd('block_homework/scroll', 'init');
        $this->page->requires->js_call_amd('block_homework/sort', 'init');
        $this->page->requires->js_call_amd('block_homework/homework_injector', 'init', [$homeworks]);
        $this->page->requires->js_call_amd('block_homework/map_link_injector', 'init');
        $this->page->requires->js_call_amd('block_homework/filter', 'init');
        $this->page->requires->js_call_amd('block_homework/clickInfo', 'init', [$USER->id, $stats['weightedreadingspeed']]);
        $this->page->requires->js_call_amd('block_homework/clickStats', 'init', [$stats]);

        return $this->content;
    }

    /**
     * Filter the URL
     * @param $url
     * @param $homeworks
     * @return array
     */
    public static function filter_homework_content($url, $homeworks): array {
        // Use a regex to remove everything but digits from the url.
        $courseid = preg_replace('/\D/', '', $url);
        $tmphomeworks = [];

        // Check each homework to see if the course matches the id.
        foreach ($homeworks as $homework) {
            if ($courseid == $homework->course) {
                array_push($tmphomeworks, $homework);
            }
        }
        return $tmphomeworks;
    }


    /**
     * Specifies where this block can be displayed in Moodle
     */
    public function applicable_formats(): array {
        return [
            'admin' => false,
            'site-index' => false,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        ];
    }

    /**
     * Get data from database and calculate user's reading speed, time spent on homework per day, and percent of homework completed.
     * @return float[]|int[] An assorted array of the stats that will be used for stat generation
     * @throws dml_exception
     */
    public function getstats() {
        global $DB, $USER;

        // The weight indicates the number of minutes after which the user's reading speed will be prioritized over the average.
        $weight = 180;
        // The global reading speed in minutes.
        $globalreadingspeed = 2;

        // Fetch courses user is enrolled in.
        $usercourses = enrol_get_users_courses($USER->id, true);

        $courseids = array_keys($usercourses); // Extract course IDs from the user's courses array.

        $placeholders = implode(',', array_fill(0, count($courseids), '?'));

        // Get records of all completions with the start page, end page, start time and end time of the material.
        $records = $DB->get_records_sql(
            "
            SELECT c.*, hm.startpage, hm.endpage
            FROM {completions} c
            INNER JOIN {homework_materials} hm ON c.material_id = hm.id
            WHERE c.usermodified = :userid",
            ['userid' => $USER->id]
        );

        $sql = "
            SELECT hm.*
            FROM {homework_materials} hm
            INNER JOIN {homework} hw ON hm.homework_id = hw.id
            INNER JOIN {course} co ON hw.course_id = co.id
            WHERE co.id IN ($placeholders)
        ";

        $availablematerials = $DB->get_records_sql($sql, $courseids);

        $totalminutes = 0;
        $totalreadingtime = 0;
        $totalpages = 0;
        $totaldays = 0;

        // Calculate total time spent both reading and in total.
        foreach ($records as $record) {
            // Timestamps are in seconds, so we get the day difference by dividing by seconds per day.
            // Use the time from the first homework completion as the start time for these stats.
            $totaldays = floor((time() - $record->timecreated) / 86400) + 1;

            $totalminutes += $record->timetaken;

            $startpage = $record->startpage;
            $endpage = $record->endpage;

            if ($startpage != null && $endpage != null) {
                $totalpages += $endpage - $startpage;
                $totalreadingtime += $record->timetaken;
            }
        }

        $weightedreadingspeed = $globalreadingspeed;

        // Calculate time per day.
        $timeperday = 0;
        if ($totaldays != 0) {
            $timeperday = $totalminutes / $totaldays;
        }

        // Calculate weighted reading speed.
        if ($totalpages != 0) {
            $readingspeed = $totalreadingtime / $totalpages;
            // The reading speed is weighted. When no pages have been read, it will be the global average a page per minute.
            // Once the number of minutes reaches the weight, the user's speed will be weighted more than the average.
            $weightedreadingspeed = $globalreadingspeed + ($readingspeed - $globalreadingspeed) *
                $totalminutes / ($totalminutes + $weight);
        }

        // Calculate percent completed.
        $percentcompleted = 0;
        if (count($records) && count($availablematerials)) {
            $percentcompleted = count($records) / count($availablematerials) * 100;
        }

        return ['timeperday' => $timeperday, 'percentcompleted' => $percentcompleted, 'weightedreadingspeed' => $weightedreadingspeed];
    }

}