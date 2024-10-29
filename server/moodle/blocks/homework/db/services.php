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

defined('MOODLE_INTERNAL') || die();


$functions = [
    'block_homework_get_info-homework' => [
    'classname'   => 'block_homework\external\get_infohomework_modal',
    'methodname'  => 'execute',
    'classpath'   => 'blocks/homework/classes/external/get_infohomework_modal.php',
    'description' => 'Get the homework info content',
    'type'        => 'read',
    'ajax'        => true,
    ],
];

$services = [
    'block_homework_services' => [
        'functions' => [
            'block_homework_get_info-homework',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
