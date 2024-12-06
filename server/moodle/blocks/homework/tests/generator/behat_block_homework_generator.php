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
 * Class that defines how homework testing data should be described
 * heavily inspired by https://moodledev.io/general/development/tools/behat/writing#writing-new-acceptance-test-step-definitions
 *
 * @copyright group 11
 * @package block_homework
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_homework_generator extends behat_generator_base {
    protected function get_creatable_entities(): array {
        return [
            'homework' => [
                'datagenerator' => 'homework',
                'required' => ['id', 'course', 'intro', 'duedate', 'name'] //switch to appropiate keys when db has been updated
            ],
        ];
    }
}