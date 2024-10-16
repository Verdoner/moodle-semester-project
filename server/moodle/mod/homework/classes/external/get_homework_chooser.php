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

        // Custom HTML for the homework chooser modal
        $html = '
            <div id="homework-chooser-modal">
                <form>
                    <label for="inputField">Input Field:</label><br>
                    <textarea type="text" id="inputField" name="inputField"></textarea><br><br>
    
                    <label>Choose one:</label><br>
                    <input checked type="radio" id="option1" name="option" value="option1">
                    <label for="option1">Literature</label><br>
                    
                    <input type="radio" id="option2" name="option" value="option2">
                    <label for="option2">Link</label><br><br>
                  
                     <div id="page-range-input">
                        <label for="startPage">Page Range:</label><br>
                        <input type="number" id="startPage" name="startPage" min="1" placeholder="Start Page" style="width: 50px;">
                        <span>-</span>
                        <label for="endPage"></label>
                        <input type="number" id="endPage" name="endPage" min="1" placeholder="End Page" style="width: 50px;">
                    </div>
                    <div id="linkDiv" style="display:none">
                        <label for="link">Link:</label><br>
                        <input name="link" id="link" type="url" placeholder="Enter URL">
                    </div>
                </form>
            </div>
        ';

        return ['html' => $html];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal')
        ]);
    }
}