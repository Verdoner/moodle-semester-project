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
 * homework/tests/generator/lib.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * Homework module data generator class.
 */
class mod_homework_generator extends testing_module_generator {
    /**
     * Create an instance of the homework module.
     *
     * @param array|null $record The test data for the homework instance.
     * @param array|null $options
     * @return stdClass The homework instance.
     * @throws \dml_exception
     */
    public function create_instance($record = null, ?array $options = null) {
        global $DB;

        // Merge incoming data with defaults.
        $record = (object)(array)$record;
        $record->course = $record->course ?? $this->get_course()->id;
        $record->name = $record->name ?? 'Test Homework';
        $record->timecreated = time();
        $record->timemodified = time();

        // Insert record into the database.
        $record->id = $DB->insert_record('homework', $record);

        // Now create the course module instance (the link between the course and the module).
        $coursemodule = new stdClass();
        $coursemodule->course = $record->course;
        $coursemodule->module = $DB->get_field('modules', 'id', ['name' => 'homework']);
        $coursemodule->instance = $record->id;
        $coursemodule->section = 0; // Default section 0.
        $coursemodule->visible = 1;
        $coursemodule->groupmode = 0;

        // Add the course module to the database.
        $coursemodule->coursemodule = add_course_module($coursemodule);

        // Add the course module to the course section.
        course_add_cm_to_section($coursemodule->course, $coursemodule->coursemodule, $coursemodule->section);

        // Return the updated homework instance.
        return $DB->get_record('course_modules', ['id' => $coursemodule->coursemodule]);
    }

    /**
     *
     * @param $material
     * @return void
     * @throws dml_exception
     */
    public function create_material(array $material) {
        global $DB;
        $DB->insert_record('homework_materials', (object) $material);
    }
}
