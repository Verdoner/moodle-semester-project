<?php

/**
 * File for page of the editor
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

global $OUTPUT, $PAGE, $DB, $CFG;
require_once('../../config.php');
// use mod_homework\edit_page;

$id = required_param('id', PARAM_INT); // Course module ID
[$course, $cm] = get_course_and_cm_from_cmid($id, 'homework');
$instance = $DB->get_record('homework', ['id'=> $cm->instance], '*', MUST_EXIST);

$context = context_module::instance($cm->id);
require_login($course, true, $cm);

$PAGE->set_url('/mod/homework/edit.php', array('id' => $id));
$PAGE->set_title(get_string('modulename', 'homework'));
$PAGE->set_heading(get_string('modulename', 'homework'));

// Adding secondary navigation links
if ($PAGE->has_secondary_navigation()) {
    // Create a new navigation node for 'Submissions'
    $submissionsnode = navigation_node::create(
        get_string('viewsubmissions', 'mod_homework'), // The label
        new moodle_url('/mod/homework/submissions.php', array('id' => $cm->id)), // URL for the link
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

echo html_writer::tag('div', 'This is the homework edit page', array('class' => 'content'));
$records = $DB->get_records('homework');

// Add the button for opening the homework chooser modal
echo html_writer::tag('button', get_string('openhomeworkchooser', 'mod_homework'), array(
    'type' => 'button',
    'id' => 'open-homework-chooser',
    'class' => 'btn btn-primary',
));

// Add a container for the modal if needed
echo html_writer::tag('div', '', array('id' => 'homework-chooser-container'));

$records = $DB->get_records('homework');

echo html_writer::start_tag('div', ['class' => 'mod-quiz-edit-content']);

// Include the AMD module
$PAGE->requires->js_call_amd('mod_homework/homeworkchooser', 'init', array($cm->id,
    get_string('homeworkchooser', 'mod_homework')));

// Output the footer - REQUIRED
echo $OUTPUT->footer();