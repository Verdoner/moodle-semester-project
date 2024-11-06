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
 * homework/db/services.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_homework_save_homework_literature' => [
        'classname'   => 'mod_homework\external\save_homework_literature',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/save_homework_literature.php',
        'description' => 'Saves the homework chooser input field value',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'mod_homework_save_homework_link' => [
        'classname'   => 'mod_homework\external\save_homework_link',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/save_homework_link.php',
        'description' => 'Saves the homework chooser link field value',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'mod_homework_get_homework_chooser' => [
        'classname'   => 'mod_homework\external\get_homework_',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/get_homework_chooser.php',
        'description' => 'Get the homework chooser content',
        'type'        => 'read',
        'ajax'        => true,
    ],
];

$services = [
    'mod_homework_services' => [
        'functions' => [
            'mod_homework_save_homework_literature',
            'mod_homework_save_homework_link',
            'mod_homework_get_homework_chooser',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
