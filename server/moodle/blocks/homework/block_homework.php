<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Block definition class for the block_homework plugin.
 *
 * @package   block_homework
 * @copyright Year, You Name <your@email.address>
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Checks for Moodle environment
defined('MOODLE_INTERNAL') || die();

class block_homework extends block_base {

    //constructor for the block
    public function init() {
        $this->title = get_string('homework', 'block_homework');
    }

    /**
     * Retrieves and prepares the content to be displayed by the block
     */
    public function get_content() {

        global $OUTPUT, $PAGE, $DB;

        $homeworks = $DB->get_records('homework');
        $data = [];

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();


        foreach($homeworks as $homework) {
            if ($PAGE->pagetype == 'course-view-topics') {
                if(preg_replace('/\D/', '',$PAGE->url) != $homework->course){
                    continue;
                }
            }
            $tmp = [];
            $tmp['name'] = $homework->name;
            $tmp['duedate'] = $homework->duedate;
            $tmp['intro'] = strip_tags($homework->intro);
            $tmp['courseTitle'] = $DB->get_field('course', 'fullname', ['id' => $homework->course]);

            $files = [];

            //Get ids of homeworkfiles
            $fileids = [];
            $homeworkfiles = $DB->get_records('files_homework', ['files_id'=>$homework->id]);
            foreach ($homeworkfiles as $homeworkfile) {
                array_push($fileids, $homeworkfile->files_id);
            }

            //Get file records
            if(!empty($fileids)) {
                $file_records = $DB->get_records_list('files', 'id', $fileids);
                foreach ($file_records as $file) {
                    $contextid = $file->contextid;
                    $component = $file->component;
                    $filearea = $file->filearea;
                    $itemid = $file->itemid;
                    $filepath = $file->filepath;
                    $filename = $file->filename;

                    //Generate url
                    $url = moodle_url::make_pluginfile_url(
                        $contextid,
                        $component,
                        $filearea,
                        $itemid,
                        $filepath,
                        $filename
                    );

                    //Get appropriate icon for file type
                    $iconurl = $OUTPUT->image_url(file_mimetype_icon($file->mimetype));

                    $files[] = [
                        'fileurl' => $url->out(),
                        'filename' => $filename,
                        'iconurl' => $iconurl
                    ];
                }
            }

            $tmp['files'] = $files;

            array_push($data, $tmp);
        }


        // Render the content using a template and pass the homework data to it
        $this->content->text = $OUTPUT->render_from_template('block_homework/data', ['data' => $data]);

        // Include JavaScript functionality for scrolling behavior in the block
        $PAGE->requires->js_call_amd('block_homework/scroll', 'init');

        return $this->content;
    }
    /**
     * Specifies where this block can be displayed in Moodle
     */
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => false,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        ];
    }

}