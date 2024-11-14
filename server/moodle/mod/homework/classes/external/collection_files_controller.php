<?php

namespace mod_homework\external;

use core\session\exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');


class collection_files_controller extends \external_api {

    /**
     *The get_collection_file_ids method gets all previously used homework resources for a course and returns their ids
     *
     * @param
     * @return int[]
     * @throws \dml_exception
     */
    public static function get_choices(): string {
        global $DB, $COURSE;

        $homeworks = $DB->get_records('homework', ['course_id'=>$COURSE->id]);

        $materials = array();
        foreach ($homeworks as $homework) {
            $material = $DB->get_record('homework_materials', ['homework_id'=>$homework->id]);
            $materials = array_merge($materials, $material);
        }

        $choices = '';
        if ($materials != null) {
            foreach ($materials as $material) {
                $choices .= '<option value="'.$material.'"></option>\n';
            }
        }
        return $choices;
    }
}