<?php

/**
 * Code for viewing each homework module for details (not done)
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $OUTPUT, $PAGE, $DB, $CFG;
require_once(__DIR__ . '/../../config.php');

use mod_homework\view_page;

$id = required_param('id', PARAM_INT); // Course module ID
[$course, $cm] = get_course_and_cm_from_cmid($id, 'homework');
$instance = $DB->get_record('homework', ['id'=> $cm->instance], '*', MUST_EXIST);

$context = context_module::instance($cm->id);
require_login($course, true, $cm);

$PAGE->set_url('/mod/homework/view.php', array('id' => $id));
$PAGE->set_title(get_string('modulename', 'homework'));
$PAGE->set_heading(get_string('modulename', 'homework'));

// Adding secondary navigation links
if ($PAGE->has_secondary_navigation()) {
    // Create a new navigation node for 'Submissions'
    $submissionsnode = navigation_node::create(
        get_string('viewsubmissions', 'mod_homework'), // The label
        new moodle_url('/mod/homework/submissions.php', array('id' => $cm->id)),
        navigation_node::TYPE_CUSTOM, // Type of node (custom link)
        null, // Icon or image (null by default)
        'submissionsnav' // Unique key for the navigation node
    );

    // Add the submissions node to the secondary navigation
    $PAGE->secondarynav->add_node($submissionsnode);

    // Example: Add another node, e.g., 'Edit Homework'
    $editnode = navigation_node::create(
        get_string('edit', 'moodle'),
        new moodle_url('/mod/homework/edit.php', array('id' => $cm->id)),
        navigation_node::TYPE_CUSTOM,
        null,
        'editnav'
    );
    $PAGE->secondarynav->add_node($editnode);
}

// Output the header - REQUIRED
echo $OUTPUT->header();

$viewobj = new view_page();
$viewobj->canedit = true;
$viewobj->editurl = new moodle_url('/mod/homework/edit.php', ['cmid' => $cm->id]);

// Add the actual page content here
echo html_writer::tag(
    'div',
    'This is the homework view page',
    array('class' => 'content')
);
$records = $DB->get_records('homework');

// Iterate and display the records
foreach ($records as $record) {
    echo 'Homework ID: ' . $record->id . '<br>';
    echo 'Homework Name: ' . $record->name . '<br>';
    // Add any other fields you'd like to display
}

if($viewobj->canedit && !$viewobj->hashomework) {
    echo html_writer::link(
        $viewobj->editurl,
        get_string('addhomework', 'homework'),
        ['class' => 'btn btn-secondary']
    );
}

// Output the footer - REQUIRED
echo $OUTPUT->footer();