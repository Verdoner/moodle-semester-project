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
 * Plugin upgrade steps are defined here.
 * homework/db/upgrade.php
 *
 * @package     mod_homework
 * @category    upgrade
 * @copyright   2024 PV
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_homework_upgrade($oldversion): bool {

    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2024111201) { // Match this version with the latest in your XMLDB definition.
        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 2024111201, 'homework');
    }
    if ($oldversion < 2024111202) {
        // Define field starttime to be added to homework_materials.
        $table = new xmldb_table('homework_materials');
        $field = new xmldb_field('starttime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'endpage');

        // Conditionally launch add field starttime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 2024111202, 'homework');
    }
    if ($oldversion < 2024111203) {
        // Define field endtime to be added to homework_materials.
        $table = new xmldb_table('homework_materials');
        $field = new xmldb_field('endtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'starttime');

        // Conditionally launch add field endtime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 2024111203, 'homework');
    }
    if ($oldversion < 2024111204) {
        // Changing the default of field course_id on table homework to 0.
        $table = new xmldb_table('homework');
        $field = new xmldb_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');

        // Launch change of default for field course_id.
        $dbman->change_field_default($table, $field);

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 2024111204, 'homework');
    }

    return true;
}
