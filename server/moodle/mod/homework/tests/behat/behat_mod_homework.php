<?php
namespace mod_homework\behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use behat_base;
use moodle_url;

/**
 * Behat context class for mod_homework.
 *
 * This file contains Behat step definitions to test the homework module in Moodle.
 *
 * @package   mod_homework
 * @category  test
 */
class behat_mod_homework extends behat_base {

    /**
     * Resolve page URL based on the page name.
     *
     * @param string $page The name of the page.
     * @return moodle_url The corresponding URL.
     * @throws Exception If the specified page cannot be found.
     */
    protected function resolve_page_url(string $page): moodle_url {
        switch (strtolower($page)) {
            case 'view':
                return new moodle_url('/mod/homework/view.php', ['id' => $this->get_cm_by_homework_name($page)->id]);
            default:
                throw new Exception('Unrecognised homework page type "' . $page . '."');
        }
    }

    /**
     * Get a course module ID by homework name.
     *
     * @param string $name Homework name.
     * @return stdClass The corresponding course module.
     */
    protected function get_cm_by_homework_name(string $name): stdClass {
        global $DB;
        $homework = $DB->get_record('homework', ['name' => $name], '*', MUST_EXIST);
        return get_coursemodule_from_instance('homework', $homework->id, $homework->course);
    }

    /**
     * Add a literature item to the homework activity.
     *
     * @When /^I add a "(?P<item_type>(?:[^"]|\\")*)" item to the "(?P<homework_name>(?:[^"]|\\")*)" homework with:$/
     * @param string $item_type The type of item to add (e.g., Literature).
     * @param string $homework_name The name of the homework activity.
     * @param TableNode $data The data table with item details.
     */
    public function i_add_item_to_homework_with($item_type, $homework_name, TableNode $data) {
        $this->execute("behat_general::i_click_on", ["{$homework_name}", "link"]);

        // Assuming fields like "Name", "Description", and "Link" in the TableNode.
        $item_data = $data->getRowsHash();
        $this->execute("behat_general::i_set_the_field", ["Name", $item_data['Literature name']]);
        $this->execute("behat_general::i_set_the_field", ["Description", $item_data['Description']]);
        $this->execute("behat_general::i_set_the_field", ["Link", $item_data['Link']]);

        $this->execute("behat_general::i_click_on", ["Save", "button"]);
    }

    /**
     * Go to a specific page of the homework activity as a specific user
     * @When /^I am on the "(?P<activity_name>(?:[^"]|\\")*)" homework activity page logged in as "(?P<username>(?:[^"]|\\")*)"$/
     * @param string $activity_name The name of the homework activity.
     * @param string $username The username to log in as.
     */
    public function i_am_on_homework_activity_page_logged_in_as($activity_name, $username) {
        global $DB;

        // Log in as the specified user.
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
        // $this->set_user($user);

        // Get the course module ID for the homework activity.
        $cm = $DB->get_record_sql("
        SELECT cm.id
        FROM {course_modules} cm
        JOIN {modules} m ON m.id = cm.module
        JOIN {homework} h ON h.id = cm.instance
        WHERE m.name = 'homework' AND h.name = :activity_name
    ", ['activity_name' => $activity_name], MUST_EXIST);

        // Navigate to the homework activity page.
        $url = new moodle_url('/mod/homework/view.php', ['id' => $cm->id]);
        $this->getSession()->visit($url->out());
    }


    /**
     * Assert that specific text is visible in the homework activity.
     *
     * @Then /^I should see "(?P<text>(?:[^"]|\\")*)" in the homework activity$/
     * @param string $text The text to check for.
     * @throws ExpectationException If the text is not found.
     */
    public function i_should_see_text_in_homework($text) {
        $this->assert_page_contains_text($text);
    }

    /**
     * Submit a homework as a student.
     *
     * @Given /^user "([^"]*)" has submitted homework "([^"]*)" with:$/
     * @param string $username The username of the student.
     * @param string $homeworkname The name of the homework activity.
     * @param TableNode $submissiondata The data for the homework submission.
     */
    public function user_has_submitted_homework($username, $homeworkname, TableNode $submissiondata) {
        global $DB;

        // Get the homework and user records.
        $homework = $DB->get_record('homework', ['name' => $homeworkname], '*', MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
        // $this->set_user($user);

        // Submission logic goes here, depending on the module's structure.
        $submission_data = $submissiondata->getRowsHash();
        $this->execute("behat_general::i_set_the_field", ["Submission Text", $submission_data['submission_text']]);
        $this->execute("behat_general::i_click_on", ["Submit", "button"]);

        // $this->set_user(); // Reset the user after the action.
    }

    /**
     * Check that specific feedback text appears after submission.
     *
     * @Then /^I should see feedback "(?P<text>(?:[^"]|\\")*)" in the homework activity$/
     * @param string $text The feedback text to check for.
     * @throws ExpectationException If the feedback is not found.
     */
    public function i_should_see_feedback_in_homework($text) {
        $this->assert_page_contains_text($text);
    }

    /**
     * Ensure a student sees the grade after submission.
     *
     * @Then /^user "([^"]*)" should see grade "([^"]*)" for homework "([^"]*)"$/
     * @param string $username The username of the student.
     * @param string $grade The expected grade.
     * @param string $homeworkname The name of the homework activity.
     * @throws ExpectationException If the grade is not found.
     */
    public function user_should_see_grade_for_homework($username, $grade, $homeworkname) {
        global $DB;

        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
        // $this->set_user($user);

        $this->execute("behat_navigation::i_am_on_page_instance", [$homeworkname, "mod_homework > view"]);
        $this->assert_page_contains_text("Grade: $grade");

        // $this->set_user(); // Reset the user after checking.
    }
}
