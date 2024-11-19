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
    $homeworkdata->course_id = $homeworkdata->course;

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
 * Serve the files from the homework file areas.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 * @return bool False if the file is not found, true if it is served.
 */
function mod_homework_pluginfile(
    $course,
    $cm,
    $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {
    require_login($course, true, $cm);

    // Only allow specific file areas, e.g., 'content'. Adjust as necessary.
    if ($filearea !== 'content') {
        return false;
    }

    // Extract itemid and filename from the $args array.
    $itemid = array_shift($args); // The first argument in $args array, often used for item ID.
    $filename = array_pop($args); // The last item in $args array, the filename.
    $filepath = empty($args) ? '/' : '/' . implode('/', $args) . '/'; // Construct the filepath from the remaining args.

    // Retrieve the file from Moodle's file storage.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_homework', $filearea, $itemid, $filepath, $filename);

    // If the file is not found or is a directory, return false.
    if (!$file || $file->is_directory()) {
        return false;
    }

    // Serve the file with caching (1 day) and without forcing download (for inline preview).
    send_stored_file($file, 86400, 0, $forcedownload, $options);
    return true;
}
