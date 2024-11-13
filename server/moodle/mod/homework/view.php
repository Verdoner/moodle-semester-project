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
$viewobj->editurl = new moodle_url('/mod/homework/edit.php', ['id' => $cm->id]);

// Add the actual page content here.
echo html_writer::tag('div', 'This is the homework view page', ['class' => 'content']);
$record = $DB->get_record('homework', ['id' => $cm->instance], '*', MUST_EXIST);

echo $record->name . '<br>';
echo $record->duedate . '<br>';
echo $record->description . '<br>';

$homeworkmaterials = $DB->get_records('homework_materials', ['homework_id' => $cm->instance]);
?>
<?php
/**
 * Loop through each item in the homework literature and display it.
 *
 * @var object $literature Literature item with description, startpage, and endpage properties.
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
foreach ($homeworkmaterials as $material) : ?>
    <div class="material"">
        <p><?php echo htmlspecialchars($material->description) ?></p>
        <?php if ($material->startpage != null) : ?>
        <p><?php echo "Pages: " . htmlspecialchars($material->startpage) . " - " . htmlspecialchars($material->endpage) ?></p>
        <?php elseif ($material->link != null): ?>
        <p><?php echo "Link: " . htmlspecialchars($material->link)?></p>
        <?php elseif ($material->starttime != null): ?>
        <p><?php
            // Create a string showing the start time as HH:MM:SS or MM:SS if no hours.
            // Sina turde ikke en funktion fordi hun ikke fatter php så hun skrev det bare imperativt :(.
            $startseconds = $material->starttime;
            $startminutes = floor($startseconds / 60);
            $starthours = floor($startminutes / 60);
            $startseconds = $startseconds % 60;
            $starttime =
                ($starthours != 0 ? ($starthours < 10 ? "0" . $starthours : $starthours) . ":" : "")
                . ($startminutes < 10 ? "0" . $startminutes : $startminutes) . ":"
                . ($startseconds < 10 ? "0" . $startseconds : $startseconds);
            // Create a string showing the end time as HH:MM:SS or MM:SS if no hours.
            $endseconds = $material->endtime;
            $endminutes = floor($endseconds / 60);
            $endhours = floor($endminutes / 60);
            $endseconds = $endseconds % 60;
            $endtime =
                ($endhours != 0 ? ($endhours < 10 ? "0" . $endhours : $endhours) . ":" : "")
                . ($endminutes < 10 ? "0" . $endminutes : $endminutes) . ":"
                . ($endseconds < 10 ? "0" . $endseconds : $endseconds);
            echo "Watch: " . htmlspecialchars($starttime) . " - " . htmlspecialchars($endtime) ?></p>
            <?php if ($material->file_id != null):
                $fs = get_file_storage();
                $url = null;
                $haspermission = has_capability('mod/homework:managefiles', $context);

                if ($haspermission) {
                    // Debugging: Check file details
                    echo 'File ID: ' . $material->file_id . '<br>';
                    $filerecord = $DB->get_record('files', ['id' => $material->file_id]);

                    if ($filerecord) {
                        echo 'File found: ' . $filerecord->filename . '<br>';

                        // Fetch file using get_file_storage
                        $video = $fs->get_file(
                        $filerecord->contextid,
                        $filerecord->component,
                        $filerecord->filearea,
                        $filerecord->itemid,
                        $filerecord->filepath,
                        $filerecord->filename
                        );

                        if ($video && !$video->is_directory()) {
                            // Debugging: Check if the video is retrieved
                            echo 'Video file found, generating URL...<br>';

                            // Generate a URL for the file.
                            $url = moodle_url::make_pluginfile_url(
                                $video->get_contextid(),
                                $video->get_component(),
                                $video->get_filearea(),
                                $video->get_itemid(),
                                $video->get_filepath(),
                                $video->get_filename(),
                                false
                            );

                            echo 'Generated URL: ' . $url->out() . '<br>';

                            // Output video player
                            if ($url) {
                                    echo '<video controls width="640" height="360">';
                                    echo '<source src="' . $url->out() . '" type="video/mp4">';
                                    echo 'Your browser does not support the video tag.';
                                    echo '</video>';
                            } else {
                                echo 'Error: URL not generated.<br>';
                            }
                        } else {
                            echo 'Error: Video file not found or is a directory.<br>';
                        }
                    } else {
                        echo 'File not found in DB.<br>';
                    }
                } else {
                    echo 'You do not have permission to manage files in this homework activity.<br>';
                }
                ?>

            <?php endif; ?>
        <?php endif; ?>
    </div>
        <?php endforeach; ?>
<?php
/**
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if ($viewobj->canedit && !$viewobj->hashomework) {
    echo html_writer::link($viewobj->editurl, get_string('addhomework', 'homework'), ['class' => 'btn btn-secondary']);
}

// Output the footer - REQUIRED.
echo $OUTPUT->footer();
