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
    'mod_homework_save_homework_material' => [
        'classname'   => 'mod_homework\external\save_homework_material',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/save_homework_material.php',
        'description' => 'Saves the homework materials',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'mod_homework_edit_homework_material' => [
        'classname'   => 'mod_homework\external\edit_homework_material',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/edit_homework_material.php',
        'description' => 'Edits the homework materials',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'mod_homework_get_homework_chooser' => [
        'classname'   => 'mod_homework\external\get_homework_chooser',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/get_homework_chooser.php',
        'description' => 'Get the homework chooser content',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'mod_homework_delete_file' => [
        'classname'   => 'mod_homework\external\delete_file',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/delete_file.php',
        'description' => 'Delete a file associated with a homework entry',
        'type'        => 'write',
        'ajax'        => true,
    ],
];

$services = [
    'mod_homework_services' => [
        'functions' => [
            'mod_homework_save_homework_material',
            'mod_homework_edit_homework_material',
            'mod_homework_get_homework_chooser',
            'mod_homework_delete_file',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];




