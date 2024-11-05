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
 * @package   blocks_homework
 * @copyright 2024, cs-24-sw-5-13 <cs-24-sw-5-13@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_homework_get_homework' => [
        'classname'   => 'block_homework\external\get_homework',
        'methodname'  => 'execute',
        'classpath'   => 'blocks/homework/classes/external/get_homework.php',
        'description' => 'Get the sorted homework',
        'type'        => 'read',
        'ajax'        => true,
    ],
];

$services = [
    'block_homework_services' => [
        'functions' => [
            'block_homework_get_homework',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
