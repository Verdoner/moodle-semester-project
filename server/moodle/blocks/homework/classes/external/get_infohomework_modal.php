<?php
namespace block_homework\external;

defined('MOODLE_INTERNAL') || die();


global $CFG;
require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_value;
use external_single_structure;

class get_infohomework_modal extends \external_api {
    /**
     * hej
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * The logic making the custom html for modal client-side
     * @return string[] - The html to be shown client-side
     */
    public static function execute() {
        global $DB;

        // Custom HTML for the homework chooser modal.
        $html = '
            <div id="info-homework-modal">
                <h1>Mark homework completed</h1>
            </div>
        ';

        return ['html' => $html];
    }

    /**
     *
     * @return external_single_structure - Is a definition of the functions return type and a description of it
     */
    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal'),
        ]);
    }
}
