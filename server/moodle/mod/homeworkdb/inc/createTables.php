<?php
global $DB;
$tablePrefix = $DB->get_prefix();
$sql = "CREATE TABLE IF NOT EXISTS ".$tablePrefix."`homework` (
`id` int(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
`description` longtext,
`duedate` datetime,
`eventid` bigint(10) unsigned NOT NULL
REFERENCES ".$tablePrefix."`event(id)` ,
";

$DB->execute($sql);

$table = new xmldb_table('homework');
$table->add_key('id', XMLDB_KEY_PRIMARY);
$DB->get_manager()->create_table();
