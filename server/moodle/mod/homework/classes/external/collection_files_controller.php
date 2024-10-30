<?php

namespace mod_homework\external;

use core\session\exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');


/**
 *
 */
class collection_files_controller extends \external_api {

    /**
     *The get_collection_file_ids method gets all previously used homework resources for a course and returns their ids
     *
     * @param $courseid
     * @return int[]
     * @throws \dml_exception
     */
    public static function get_collection_file_ids($courseid): array {
        global $DB;

        $sql = "SELECT id FROM homework WHERE course = ".$courseid;
        $results = $DB->get_records_sql($sql);


        $files = array();
        foreach ($results as $result) {
            $sql = "SELECT files_id FROM files_homework WHERE homework_id = ".$result->id;
            $temp = $DB->get_records_sql($sql);
            $files = array_merge($files, $temp);
        }

        return $files;
    }
}