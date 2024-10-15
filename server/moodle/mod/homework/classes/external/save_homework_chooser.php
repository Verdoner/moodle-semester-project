<?php
// File: mod/homework/classes/external/save_homework_chooser.php

namespace mod_homework\external;

require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_value;
use external_single_structure;

class save_homework_chooser extends \external_api
{

    // Define the parameters expected by this function.
    public static function execute_parameters()
    {
        return new external_function_parameters([
            'inputfield' => new external_value(PARAM_TEXT, 'Input field value')
        ]);
    }

    // The main function to handle the request.
    public static function execute($inputfield)
    {
        global $DB, $USER;

        // Handle the input field value here.
        // For example, save to a database.
        $record = new \stdClass();
        $record->userid = $USER->id;
        $record->description = $inputfield;
        $record->timecreated = time();

        $DB->insert_record('homework_literature', $record);

        // Return a success response.
        return ['status' => 'success', 'message' => 'Data saved successfully'];
    }

    // Define the return values.
    public static function execute_returns()
    {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status of the request'),
            'message' => new external_value(PARAM_TEXT, 'Message with details about the request status')
        ]);
    }
}