<?php

namespace mod_homework\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

class get_events_for_homework extends external_api
{

    public static function execute_parameters() {
        return new external_function_parameters([
            'homeworkid' => new external_value(PARAM_INT, 'Homework ID'),
        ]);
    }
    public static function execute($homeworkid) {
        global $DB;

        $homework = $DB->get_record('homework', array('id' => $homeworkid));

        $html = '';

        if($homework->eventid != NULL){
            $homeworkEvent = $DB->get_record('event',array('id' => $homework->eventid));
            $html .= 'This homework is already linked to Event: Name: '. $homeworkEvent->name . '<br> Time of the event: ' .date("Y-m-d H:i:s",$homeworkEvent->timestart) . '<br>' ;
        }

        //Create an sql query that only gets events which are not already linked
        $sql = 'SELECT * FROM {event} e
                    WHERE e.courseid = ? 
                    AND e.id NOT IN (
                    SELECT h.eventid FROM {homework} h
                    WHERE h.eventid IS NOT NULL
                )';

        $events = $DB->get_records_sql($sql, array($homework->course_id));


        //Check if there are any events. If not then send an empty html string which is handled by the js
        if(count($events) > 0){
            $html = '<form id="evntlinkerform"">
                      <p>Chose an event to link</p>';
            foreach ($events as $event) {
                $html .= '<input type="radio" id="event'. $event->id . '" name="eventtolink" value="' . $event->id .'" required>
                      <label for="event'. $event->id . '">Name: '. $event->name . ' Time of the event: ' .date("Y-m-d H:i:s",$event->timestart) .'</label><br>';
            }
            $html .= '</form>';
        } else{
            $html .= "There are no available courses to link";
        }


        return ['events' => $html];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'events' => new external_value(PARAM_RAW, 'test'),
        ]);
    }

}