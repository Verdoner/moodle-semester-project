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
 * Code for viewing each homework module for details (not done)
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');
global $OUTPUT, $PAGE, $DB, $CFG;
use mod_homework\view_page;

$id = required_param('id', PARAM_INT);// Course module ID.
[$course, $cm] = get_course_and_cm_from_cmid($id, 'homework');
$instance = $DB->get_record('homework', ['id' => $cm->instance], '*', MUST_EXIST);

$context = context_module::instance($cm->id);
require_login($course, true, $cm);

try {
    $PAGE->set_url('/mod/homework/view.php', ['id' => $id]);
} catch (coding_exception $e) {
    debugging($e->getMessage(), DEBUG_DEVELOPER);
}
try {
    $PAGE->set_title(get_string('modulename', 'homework'));
} catch (coding_exception $e) {
    debugging($e->getMessage(), DEBUG_DEVELOPER);
}
$PAGE->set_heading(get_string('modulename', 'homework'));

// Adding secondary navigation links.
if ($PAGE->has_secondary_navigation()) {
    // Create a new navigation node for 'Submissions'.
    $submissionsnode = navigation_node::create(
        get_string('viewsubmissions', 'mod_homework'), // The label.
        new moodle_url('/mod/homework/submissions.php', ['id' => $cm->id]), // URL for the link.
        navigation_node::TYPE_CUSTOM, // Type of node (custom link).
        null, // Icon or image (null by default).
        'submissionsnav' // Unique key for the navigation node.
    );

    // Add the submissions node to the secondary navigation.
    $PAGE->secondarynav->add_node($submissionsnode);

    // Example: Add another node, e.g., 'Edit Homework'.
    try {
        $editnode = navigation_node::create(
            get_string('edit', 'moodle'),
            new moodle_url('/mod/homework/edit.php', ['id' => $cm->id]),
            navigation_node::TYPE_CUSTOM,
            null,
            'editnav'
        );
        $PAGE->secondarynav->add_node($editnode);
    } catch (coding_exception | \core\exception\moodle_exception $e) {
        debugging($e->getMessage(), DEBUG_DEVELOPER);
    }
}

// Output the header - REQUIRED.
echo $OUTPUT->header();

$viewobj = new view_page();
$viewobj->canedit = true;
$viewobj->editurl = new moodle_url('/mod/homework/edit.php', ['cmid' => $cm->id]);

// Add the actual page content here.
echo html_writer::tag('div', 'This is the homework view page', ['class' => 'content']);
$records = $DB->get_records('homework');

// Iterate and display the records.
foreach ($records as $record) {
    echo 'Homework ID: ' . $record->id . '<br>';
    echo 'Homework Name: ' . $record->name . '<br>';
    // Add any other fields you'd like to display.
}
if ($viewobj->canedit && !$viewobj->hashomework) {
    echo html_writer::link($viewobj->editurl, get_string('addhomework', 'homework'), ['class' => 'btn btn-secondary']);
}

// Output the footer - REQUIRED.
echo $OUTPUT->footer();
