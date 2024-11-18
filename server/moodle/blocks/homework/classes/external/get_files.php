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
 * Block definition class for the block_homework plugin.
 *
 * @package   block_homework
 * @copyright Year, You Name <your@email.address>
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_homework\external;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_value;
use external_single_structure;

class get_files extends \external_api {

    public static function execute_parameters(): external_function_parameters{
        return new external_function_parameters([
            'files' => new external_value(PARAM_TEXT, 'the files to be gotten'),
        ]);
    }

    public static function execute($files) {
        $car = array("brand" => "Ford", "model" => "Mustang", "year" => 1964);

        return ["homework" => json_encode($car, JSON_THROW_ON_ERROR)];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'homework' => new external_value(PARAM_TEXT, 'Data  of homework')
        ]);
    }
}
