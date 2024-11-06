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
function xmldb_homework_upgrade($oldversion): bool
{
    global $DB;

    $dbman = $DB->get_manager();

    // Upgrade step for creating the 'homework' table.
    if ($oldversion < 3024090700) { // Match this version with the latest in your XMLDB definition.
        // Define table 'homework' to be created.
        $table = new xmldb_table('homework');

        // Adding fields to table homework.
        $table->add_field(
            'id',
            XMLDB_TYPE_INTEGER,
            '10',
            null,
            XMLDB_NOTNULL,
            XMLDB_SEQUENCE,
            null,
            'Standard Moodle primary key'
        );
        $table->add_field(
            'name',
            XMLDB_TYPE_CHAR,
            '255',
            null,
            XMLDB_NOTNULL,
            null,
            null,
            'Name of the homework'
        );
        $table->add_field(
            'intro',
            XMLDB_TYPE_TEXT,
            null,
            null,
            null,
            null,
            null,
            'Introduction text for the homework'
        );
        $table->add_field(
            'introformat',
            XMLDB_TYPE_INTEGER,
            '4',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'Format of the introduction text'
        );
        $table->add_field(
            'timecreated',
            XMLDB_TYPE_INTEGER,
            '10',
            null,
            XMLDB_NOTNULL,
            null,
            null,
            'Timestamp when the record was created'
        );
        $table->add_field(
            'timemodified',
            XMLDB_TYPE_INTEGER,
            '10',
            null,
            XMLDB_NOTNULL,
            null,
            null,
            'Timestamp when the record was last modified'
        );
        $table->add_field(
            'duedate',
            XMLDB_TYPE_INTEGER,
            '10',
            null,
            null,
            null,
            null
        );

        // Adding keys to table homework.
        $table->add_key(
            'primary',
            XMLDB_KEY_PRIMARY,
            ['id']
        );

        // Conditionally launch create table for homework.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Define table files_homework to be created.
        $table = new xmldb_table('files_homework');

        // Adding fields to table files_homework.
        $table->add_field('files_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('homework_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table files_homework.
        $table->add_key('f_key_files_id', XMLDB_KEY_FOREIGN, ['files_id'], 'files', ['id']);
        $table->add_key('f_key_homework_id', XMLDB_KEY_FOREIGN, ['homework_id'], 'homework', ['id']);

        // Conditionally launch create table for files_homework.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Upgrade step for creating the 'homework_literature' table.
        // Define table homework_literature to be created.
    if ($oldversion < 2024102802) {
        // Literature
        // Define field homework to be added to homework_literature.
        $table = new xmldb_table('homework_literature');
        $field = new xmldb_field('homework', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');
        $key = new xmldb_key('homework', XMLDB_KEY_FOREIGN, ['homework'], 'homework', ['id']);

        // Adding fields to table homework_literature.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('startpage', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('endpage', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('homework_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table homework_literature.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('f_key_homework_id', XMLDB_KEY_FOREIGN, ['homework_id'], 'homework', ['id']);

        // Conditionally launch create table for homework_literature.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Conditionally launch add field homework.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_key($table, $key);
            $dbman->drop_field($table, $field);

        }
        $dbman->add_field($table, $field);
        $dbman->add_key($table, $key);

        // Links
        // Define field id to be added to homework_links.
        $table = new xmldb_table('homework_links');
        $field = new xmldb_field('homework', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');
        $key = new xmldb_key('homework', XMLDB_KEY_FOREIGN, ['homework'], 'homework', ['id']);

        // Conditionally launch add field id.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_key($table, $key);
            $dbman->drop_field($table, $field);
        }
        $dbman->add_field($table, $field);
        $dbman->add_key($table, $key);

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024090700, 'homework');
    }

    if ($oldversion < 3024091000) {
        // Define field homework_id to be added to homework_literature.
        $table = new xmldb_table('homework_literature');
        $field = new xmldb_field('homework_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'timemodified');

        // Conditionally launch add field homework_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091000, 'homework');
    }

    if ($oldversion < 3024091100) {
        // Define key f_key_homework_id (foreign) to be added to homework_literature.
        $table = new xmldb_table('homework_literature');
        $key = new xmldb_key('f_key_homework_id', XMLDB_KEY_FOREIGN, ['homework_id'], 'homework', ['id']);

        // Launch add key f_key_homework_id.
        $dbman->add_key($table, $key);

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091100, 'homework');
    }

    if ($oldversion < 3024091200) {
        // Define field homework_id to be added to homework_links.
        $table = new xmldb_table('homework_links');
        $field = new xmldb_field('homework_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'link');

        // Conditionally launch add field homework_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091200, 'homework');
    }

    if ($oldversion < 3024091300) {
        // Define key f_key_homework_id (foreign) to be added to homework_links.
        $table = new xmldb_table('homework_links');
        $key = new xmldb_key('f_key_homework_id', XMLDB_KEY_FOREIGN, ['homework_id'], 'homework', ['id']);

        // Launch add key f_key_homework_id.
        $dbman->add_key($table, $key);

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091300, 'homework');
    }

    if ($oldversion < 3024091309) {
        // Define table completions to be created.
        $table = new xmldb_table('completions');

        // Adding fields to table completions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('literature_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('time_taken', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('link_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table completions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('f_key_user_id', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id']);
        $table->add_key('f_key_literature_id', XMLDB_KEY_FOREIGN, ['literature_id'], 'homework_literature', ['id']);
        $table->add_key('f_key_link_id', XMLDB_KEY_FOREIGN, ['link_id'], 'homework_links', ['id']);

        // Conditionally launch create table for completions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091409, 'homework');
    }

    if ($oldversion < 3024091409) {
        // Define table homework_video to be created.
        $table = new xmldb_table('homework_video');

        // Adding fields to table homework_video.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('homework_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table homework_video.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('f_key_fileid', XMLDB_KEY_FOREIGN, ['fileid'], 'files', ['id']);
        $table->add_key('f_key_homework_id', XMLDB_KEY_FOREIGN, ['homework_id'], 'homework', ['id']);

        // Conditionally launch create table for homework_video.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091409, 'homework');
    }

    if ($oldversion < 3024091409) {
        // Define field video_id to be added to completions.
        $table = new xmldb_table('completions');
        $field = new xmldb_field('video_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'link_id');

        // Conditionally launch add field video_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091409, 'homework');
    }

    if ($oldversion < 3024091409) {
        // Define key f_key_video_id (foreign) to be added to completions.
        $table = new xmldb_table('completions');
        $key = new xmldb_key('f_key_video_id', XMLDB_KEY_FOREIGN, ['video_id'], 'homework_video', ['id']);

        // Launch add key f_key_video_id.
        $dbman->add_key($table, $key);

        // Homework savepoint reached.
        upgrade_mod_savepoint(true, 3024091409, 'homework');
    }



    return true;
}
