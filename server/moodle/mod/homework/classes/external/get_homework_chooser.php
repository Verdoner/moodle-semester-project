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
                </form>
            </div>
            <script>
            const startPageInput = document.getElementById("startPage");
            const endPageInput = document.getElementById("endPage");
    
            // Add event listener to validate the input fields
            startPageInput.addEventListener("input", validatePageRange);
            endPageInput.addEventListener("input", validatePageRange);
    
            function validatePageRange() {
                const startPage = parseInt(startPageInput.value, 10);
                const endPage = parseInt(endPageInput.value, 10);
    
                if (endPageInput.value !== "" && startPageInput.value !== "") {
                    if (endPage < startPage) {
                        endPageInput.setCustomValidity("End Page must be greater than or equal to Start Page");
                    } else {
                        endPageInput.setCustomValidity(""); // Clear the error message if valid
                    }
                } else {
                    endPageInput.setCustomValidity(""); // Clear any error if either field is empty
                }
            }
            // Get all radio buttons with the name "option"
            const radioButtons = document.querySelectorAll("input[name=\'option\']");
            const testTextarea = document.getElementById("page-range-input");
    
            // Add event listener to each radio button
            radioButtons.forEach(radio => {
                radio.addEventListener("change", function() {
                    if (document.getElementById("option1").checked) {
                        testTextarea.style.display = "block";
                    } else {
                        testTextarea.style.display = "none";
                    }
                });
            });
        </script>
        ';

        return ['html' => $html];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML for the homework chooser modal')
        ]);
    }
}
