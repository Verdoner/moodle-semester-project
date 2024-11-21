<?php
global $CFG;
require_once(__DIR__ . '/config.php'); // Adjust the path as needed
require_once($CFG->libdir . '/filelib.php');

// Define file parameters (usually passed as URL parameters)
$contextid = required_param('contextid', PARAM_INT);
$component = required_param('component', PARAM_TEXT);
$filearea = required_param('filearea', PARAM_TEXT);
$itemid = required_param('itemid', PARAM_INT);
$filename = required_param('filename', PARAM_FILE);
$filepath = '/'; // Set to '/' for the root path or adjust as needed

// Set up file storage
$fs = get_file_storage();
$file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);

// Check if the file exists and is not a directory
if ($file && !$file->is_directory()) {
    // Force download of the file
    send_stored_file($file, 0, 0, true); // 'true' forces the download
} else {
    // Display an error message if the file is not found
    echo "File not found or you do not have permission to access it.";
}


require_once(__DIR__ . '/../../config.php'); // Adjust the path as needed
require_once($CFG->libdir . '/filelib.php');

// Define file parameters (usually passed as URL parameters)
$contextid = required_param('contextid', PARAM_INT);
$component = required_param('component', PARAM_TEXT);
$filearea = required_param('filearea', PARAM_TEXT);
$itemid = required_param('itemid', PARAM_INT);
$filename = required_param('filename', PARAM_FILE);
$filepath = '/'; // Set to '/' for the root path or adjust as needed

// Set up file storage
$fs = get_file_storage();
$file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);

// Check if the file exists and is not a directory
if ($file && !$file->is_directory()) {
    // Force download of the file
    send_stored_file($file, 0, 0, true); // 'true' forces the download
} else {
    // Display an error message if the file is not found
    echo "File not found or you do not have permission to access it.";
}

