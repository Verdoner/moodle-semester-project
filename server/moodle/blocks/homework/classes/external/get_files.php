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
 * Block definition class for the block_homework plugin.
 *
 * @package   block_homework
 * @copyright Year, You Name <your@email.address>
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_homework\external;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_value;
use external_single_structure;

class get_files extends \external_api {

    /*
    public static function execute_parameters(): external_function_parameters{
        return new external_function_parameters([
            'files' => new external_value(PARAM_TEXT, 'the files to be gotten'),
        ]);
    }
    */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'The section ID from which to retrieve files'),
            'courseid' => new external_value(PARAM_INT, 'The course ID to validate context'),
        ]);
    }

    /*
    public static function execute($files) {
        $car = array("brand" => "Ford", "model" => "Mustang", "year" => 1964);

        return ["homework" => json_encode($car, JSON_THROW_ON_ERROR)];
    }
*/

    public static function execute($sectionid, $courseid) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/filelib.php');
        require_login();

        $context = \context_course::instance($courseid);
        require_capability('moodle/course:view', $context);

        $sql = "SELECT * FROM {files}
                WHERE contextid = :contextid
                  AND component = 'course'
                  AND filearea = 'section'
                  AND itemid = :sectionid
                  AND filename <> '.'
                  AND filesize > 0";

        $params = ['contextid' => $context->id, 'sectionid' => $sectionid];
        $files = $DB->get_records_sql($sql, $params);

        if (!$files) {
            throw new \moodle_exception('nofiles', 'error');
        }

        $fs = get_file_storage();
        $filepaths = [];
        foreach ($files as $file) {
            $stored_file = $fs->get_file(
                $file->contextid,
                $file->component,
                $file->filearea,
                $file->itemid,
                $file->filepath,
                $file->filename
            );
            if ($stored_file) {
                $filepaths[] = $stored_file->get_content_file_location();
            }
        }

        // Generate ZIP
        $zipfilename = tempnam(sys_get_temp_dir(), 'section_files_') . '.zip';
        $zipper = new \zip_packer();
        if ($zipper->archive_to_pathname($filepaths, $zipfilename) !== true) {
            throw new \moodle_exception('nozip', 'error');
        }

        return [
            'homework' => json_encode([
                'zipfile' => $zipfilename,
                'sectionid' => $sectionid,
                'courseid' => $courseid
            ], JSON_THROW_ON_ERROR)
        ];
    }

    /*
    public static function execute_returns() {
        return new external_single_structure([
            'homework' => new external_value(PARAM_TEXT, 'Data  of homework')
        ]);
    }*/

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'homework' => new external_value(PARAM_TEXT, 'Data of the homework, including the ZIP file path'),
        ]);
    }
}
