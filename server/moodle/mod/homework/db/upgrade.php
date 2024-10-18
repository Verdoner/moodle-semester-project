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
 * homework/db/upgrade.php
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_homework_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Upgrade step for creating the 'homework' table.
    if ($oldversion < 2024090500) { // Match this version with the latest in your XMLDB definition.
        // Define table 'homework' to be created.
        $table = new xmldb_table('homework');

        // Adding fields to table homework.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, 'Standard Moodle primary key');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'Name of the homework');
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'Introduction text for the homework');
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'Format of the introduction text');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'Timestamp when the record was created');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'Timestamp when the record was last modified');
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table homework.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for homework.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Upgrade step for creating the 'homework_literature' table.
        $table = new xmldb_table('homework_literature');

        // Adding fields to table homework_literature.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, 'Standard Moodle primary key');
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'Description of the homework literature');
        $table->add_field('startpage', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'Start page number');
        $table->add_field('endpage', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'End page number');
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'Format of the introduction text');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'Timestamp when the record was created');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'Timestamp when the record was last modified');

        // Adding keys to table homework_literature.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for homework_literature.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table homework_links to be created.
        $table = new xmldb_table('homework_links');

        // Adding fields to table homework_links.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('link', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table homework_links.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for homework_links.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // homework savepoint reached.
        upgrade_mod_savepoint(true, 2024090500, 'homework');
    }

    return true;
}