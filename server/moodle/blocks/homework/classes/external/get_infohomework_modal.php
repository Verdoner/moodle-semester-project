<?php

class get_infohomework_modal extends \external_api {
    /**
     *
     * @return external_function_parameters Is a definition of the functions parameter type and a description of it.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course Module ID'),
        ]);
    }

    /**
     * The logic making the custom html for modal client-side
     * @param $cmid - The current modules id
     * @return string[] - The html to be shown client-side
     */
    public static function execute($cmid) {
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
