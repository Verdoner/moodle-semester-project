<?php
namespace mod_homework;

use advanced_testcase;
use dml_exception;
use mod_homework_generator;

final class get_events_for_homework_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true); // Reset the Moodle environment after each test.
    }

    //Test For getting events which are not linked at all
    public function test_Getting_Non_linked_events(): void {
        //Create a user
        $user = self::getDataGenerator()->create_user();
        self::setUser($user);
        //Create a course with a course module.
        $course = self::getDataGenerator()->create_course();
        //Create an event that is linked to the course
        $event = self::getDataGenerator()->create_event(array('course_id'=>$course->id));
        //Create a homework which is added to that course.
        $homework = $homework_generator = new mod_homework_generator();
        $homework_generator->create_instance();

        die($homework);




        self::getDataGenerator();


    }

    //Create a course with a course module.
    //Create a homework which is added to that course.
    //Create an event that is linked to the course


    //Test for getting other events when one is already linked

    //Test getting events when there are no available events



}