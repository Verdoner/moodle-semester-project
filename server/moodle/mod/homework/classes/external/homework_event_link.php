<?php

namespace mod_homework\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

class homework_event_link extends external_api
{
    public static function execute_parameters() {
        return new external_function_parameters([
            'homeworkid' => new external_value(PARAM_INT, 'homework ID'),
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
            'eventid' => new external_value(PARAM_INT, 'Event ID'),
        ]);
    }
    public static function execute($homeworkid, $cmid, $eventid) {

        $params = self::validate_parameters(self::execute_parameters(), [
            'homeworkid' => $homeworkid,
            'cmid' => $cmid,
            'eventid' => $eventid,
        ]);

        global $DB;

        $homework = $DB->get_record('homework', array('id' => $params['homeworkid']));
        $homework->cmid = $params['cmid'];
        $homework->eventid = $params['eventid'];

        try {
            $DB->update_record('homework', $homework);
        } catch (\dml_exception $e) {
            return ['status' => 'error', 'message' => 'Failed to add homework event link due to ' . $e];
        }

        return ['status' => 'success', 'message' => 'Success'];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status of the request'),
            'message' => new external_value(PARAM_TEXT, 'Message with details about the request status'),
        ]);
    }
}