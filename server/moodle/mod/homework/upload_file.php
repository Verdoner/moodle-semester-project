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

/**
 * Upload file to Moodle
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
global $USER, $CFG;
require_once($CFG->libdir . '/filelib.php');
require_login();

// Set up file storage.
$context = context_user::instance($USER->id);
$fs = get_file_storage();

// Define file options.
$fileoptions = [
    'contextid' => $context->id,
    'component' => 'mod_homework',
    'filearea'  => 'content',
    'itemid'    => 0,
    'filepath'  => '/',
    'filename'  => $_FILES['file']['name'],
];

// Delete existing file if needed.
if (
    $fs->file_exists(
        $fileoptions['contextid'],
        $fileoptions['component'],
        $fileoptions['filearea'],
        $fileoptions['itemid'],
        $fileoptions['filepath'],
        $fileoptions['filename']
    )
) {
    $existingfile = $fs->get_file(
        $fileoptions['contextid'],
        $fileoptions['component'],
        $fileoptions['filearea'],
        $fileoptions['itemid'],
        $fileoptions['filepath'],
        $fileoptions['filename']
    );
}

// Save new file.
$file = $fs->create_file_from_pathname($fileoptions, $_FILES['file']['tmp_name']);

if ($file) {
    echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully', 'fileid' => $file->get_id()]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
}
