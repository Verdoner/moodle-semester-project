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
 * Class that inserts testing data into the db for behat testing
 * heavily inspired by https://moodledev.io/general/development/tools/behat/writing#writing-new-acceptance-test-step-definitions
 *
 * @copyright group 11
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_homework_generator extends component_generator_base {
    public function create_homework($homework) {
        global $DB;
        $DB->insert_record('homework', $homework);
    }
}
