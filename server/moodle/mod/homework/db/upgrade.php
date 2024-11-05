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
function xmldb_homework_upgrade ($oldversion): bool {
	global $DB;

	$dbman = $DB->get_manager ();

	if ($oldversion < 2024102802) {
		// Literature
		// Define field homework to be added to homework_literature.
		$table = new xmldb_table('homework_literature');
		$field = new xmldb_field('homework', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');
		$key = new xmldb_key('homework', XMLDB_KEY_FOREIGN, ['homework'], 'homework', ['id']);

		// Conditionally launch add field homework.
		if ($dbman->field_exists ($table, $field)) {
			$dbman->drop_key ($table, $key);
			$dbman->drop_field ($table, $field);
		}
		$dbman->add_field ($table, $field);
		$dbman->add_key ($table, $key);

		// Links
		// Define field id to be added to homework_links.
		$table = new xmldb_table('homework_links');
		$field = new xmldb_field('homework', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');
		$key = new xmldb_key('homework', XMLDB_KEY_FOREIGN, ['homework'], 'homework', ['id']);

		// Conditionally launch add field id.
		if ($dbman->field_exists ($table, $field)) {
			$dbman->drop_key ($table, $key);
			$dbman->drop_field ($table, $field);
		}
		$dbman->add_field ($table, $field);
		$dbman->add_key ($table, $key);

		// Homework savepoint reached.
		upgrade_mod_savepoint (true, 2024102802, 'homework');
	}

	return true;
}
