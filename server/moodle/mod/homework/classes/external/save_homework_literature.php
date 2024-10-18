<?php

/**
 * homework/classes/external/save_homework_literature.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_homework\external;

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

class save_homework_literature extends \external_api
{

    /**
     * @return external_function_parameters Define the parameters expected by this function.
     */
    public static function execute_parameters()
    {
        return new external_function_parameters([
            'inputfield' => new external_value(PARAM_TEXT, 'Input field value'),
            'startpage' => new external_value(PARAM_INT, 'startPage field value'),
            'endpage' => new external_value(PARAM_INT, 'endPage field value')
        ]);
    }

    /**
     * The main function to handle the request.
     * @param $inputfield
     * @param $startpage
     * @param $endpage
     * @return string[]
     * @throws \dml_exception
     */
    public static function execute($inputfield, $startpage, $endpage)
    {
        global $DB, $USER;

        // Handle the input field value here.
        // For example, save to a database.
        $record = new \stdClass();
        $record->userid = $USER->id;
        $record->description = $inputfield;
        $record->startpage = $startpage;
        $record->endpage = $endpage;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('homework_literature', $record);

        // Return a success response.
        return ['status' => 'success', 'message' => 'Data saved successfully'];
    }

    /**
     * @return external_single_structure Define the return values.
     */
    public static function execute_returns()
    {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status of the request'),
            'message' => new external_value(PARAM_TEXT, 'Message with details about the request status')
        ]);
    }
}