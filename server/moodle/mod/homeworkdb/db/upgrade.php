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
 *
 * @package     mod_homeworkdb
 * @category    upgrade
 * @copyright   2024 PV 
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute mod_homeworkdb upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_homeworkdb_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

	if ($oldversion < 2024100303) {
		// Define table homeworkdb to be created.
		$table = new xmldb_table('homeworkdb');

		// Adding fields to table homeworkdb.
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
		$table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
		$table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
		$table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');

		// Adding keys to table homeworkdb.
		$table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
		$table->add_key('fk_course', XMLDB_KEY_FOREIGN, ['course'], 'course', ['id']);

		// Conditionally launch create table for homeworkdb.
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}

		// Homeworkdb savepoint reached.
		upgrade_mod_savepoint(true, 2024100303, 'homeworkdb');

		// Define table homework to be created.
		$table = new xmldb_table('homework');

		// Adding fields to table homework.
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
		$table->add_field('duedate', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
		$table->add_field('eventid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

		// Adding keys to table homework.
		$table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
		$table->add_key('f_key', XMLDB_KEY_FOREIGN_UNIQUE, ['eventid'], 'event', ['id']);

		// Conditionally launch create table for homework.
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}

		// Homeworkdb savepoint reached.
		upgrade_mod_savepoint(true, 2024100303, 'homeworkdb');

	}




    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.

    return true;
}
