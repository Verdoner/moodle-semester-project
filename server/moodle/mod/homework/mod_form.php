<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;
/**
 * Activity creation/editing form for the mod_homework plugin.
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Homework Form Class used to allow teachers to add homework
 */
class mod_homework_mod_form extends moodleform_mod {
    /**
     *
     * @return void
     * @throws coding_exception
     */
    public function definition(): void {
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;

        // Section for input of Name for the course, and its description.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), ['size' => '48']);
        $mform->addHelpButton('name', 'name', 'homework');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();

        // Section for input of duedate.
        $mform->addElement('header', 'duedate', get_string('duedate', 'homework'));
        $mform->addElement('date_time_selector', 'duedateselector', get_string('dueto', 'homework'), [
            'optional'  => true,
        ]);


        // MUST MOODLE.
        $this->standard_coursemodule_elements();

        // -------------------------------------------------------
        $this->add_action_buttons();
    }

    /**
     *
     * @param $data
     * @param $files
     * @return array
     * @throws coding_exception
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        // Validate the optional due date field.
        if (isset($data['duedateselector'])) {
            // If the field is optional and the value is not 0 (which means a date was selected), perform validation.
            if ($data['duedateselector'] != 0) {
                // Check if the selected due date is in the past.
                if ($data['duedateselector'] < time()) {
                    $errors['duedateselector'] = get_string('duedate_in_past', 'homework');
                }
            }
        }

        return $errors;
    }

    /**
     *
     * @param $defaultvalues
     * @return void
     */
    public function data_preprocessing(&$defaultvalues) {
    }
}
