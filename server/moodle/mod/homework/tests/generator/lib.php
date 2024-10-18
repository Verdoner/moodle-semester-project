<?php
// File: homework/tests/generator/lib.php

defined('MOODLE_INTERNAL') || die();

/**
 * Homework module data generator class.
 */
class mod_homework_generator extends testing_module_generator {

    /**
     * Create an instance of the homework module.
     *
     * @param array|stdClass $record The test data for the homework instance.
     * @return stdClass The homework instance.
     */
    public function create_instance($record = null, array $options = null) {
        global $DB;

        // Merge incoming data with defaults.
        $record = (object)(array)$record;
        $record->course = isset($record->course) ? $record->course : $this->get_course()->id;
        $record->name = isset($record->name) ? $record->name : 'Test Homework';
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
}
