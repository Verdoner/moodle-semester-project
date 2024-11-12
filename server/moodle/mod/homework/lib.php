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
 * Library of interface functions and constants.
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */


/**
 *
 * @param $homeworkdata - Contains the data from homework to be added to the db
 * @return bool|int - Returns Homework id
 * @throws dml_exception - Throws error if database save fails
 */
function homework_add_instance($homeworkdata) {
    global $DB;

    $homeworkdata->timecreated = time();
    $homeworkdata->timemodified = time();


    // Save the due date if it's not empty.
    if (!empty($homeworkdata->duedateselector)) {
        $homeworkdata->duedate = $homeworkdata->duedateselector;  // Store the due date as a UNIX timestamp.
    } else {
        $homeworkdata->duedate = null;  // If no due date is set, store null in the database.
    }

    $homeworkdata->id = $DB->insert_record('homework', $homeworkdata);

    return $homeworkdata->id;
}

/**
 *
 * @param $homeworkdata
 * @return bool
 * @throws dml_exception
 */
function homework_update_instance($homeworkdata) {
    global $DB;

    $homeworkdata->timemodified = time();
    $homeworkdata->id = $homeworkdata->instance;

    $homeworkdata->duedate = $homeworkdata->duedateselector;

    $DB->update_record('homework', $homeworkdata);

    return true;
}

/**
 *
 * @param $id
 * @return bool
 * @throws dml_exception
 */
function homework_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('homework', ['id' => $id]);
    if (!$exists) {
        return false;
    }

    $DB->delete_records('homework', ['id' => $id]);

    return true;
}

/**
 * Inspiration taken from https://moodledev.io/docs/4.5/apis/subsystems/files
 * Serve the files from the myplugin file areas.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function homework_pluginfile(
    $course,
    $cm,
    $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {
    // Make sure the user is logged in and has access to the module.
    require_login($course, true);

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (empty($args)) {
        // ... $args is empty => the path is '/'.
        $filepath = '/';
    } else {
        // ... $args contains the remaining elements of the filepath.
        $filepath = '/' . implode('/', $args) . '/';
    }

    $itemid = null;

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'homework', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        // The file does not exist.
        //error_log() is forbidden, changed to debuggin
        debugging("File not found: Context ID - $context->id, File area - $filearea, Item ID - $itemid,
            Path - $filepath, Filename - $filename");
        return false;
    }

    // Send file to browser with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
    return true;

}