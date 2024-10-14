<?php

namespace mod_homework\external;

require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_value;
use external_single_structure;

class get_homework_chooser extends \external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course Module ID'),
        ]);
    }

    public static function execute($cmid) {
        global $DB;

        // Generate or fetch the content for the modal (this could be HTML, or other content)
        $html = '<div>This is your custom Homework Chooser content.</div>';

        return ['html' => $html];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal')
        ]);
    }
}
