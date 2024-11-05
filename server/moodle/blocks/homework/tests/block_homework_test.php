<?php // File: mod/myplugin/tests/sample_test.php

namespace block_homework\tests;

use block_homework;
use block_homework\external\get_homework;
use stdClass;

class block_homework_test extends \basic_testcase {
    public function test_course_homeworkfilter(){

        //Create test data
        $tmpArray =[];
        $homeworks = [];

        $testhomework1 = new stdClass();
            $testhomework1->id = 1;
            $testhomework1->course = 2;
            $testhomework1->name = 'test 1';
            $testhomework1->timecreated = time();
            $testhomework1->timemodified = 0;
            $testhomework1->intro = '<p> test 1 </p>';
            $testhomework1->introformat = 1;
            $testhomework1->description = '';
            $testhomework1->eventid = 0;
			$testhomework1->duedate = time() + 86400000 * 2; // 2 days from now
	        $testhomework1->time = 10000;

        $testhomework2 = new stdClass();
            $testhomework2->id = 2;
            $testhomework2->course = 3;
            $testhomework2->name = 'test 1';
            $testhomework2->timecreated = time();
            $testhomework2->timemodified = 0;
            $testhomework2->intro = '<p> test 2 </p>';
            $testhomework2->introformat = 1;
            $testhomework2->description = '';
            $testhomework2->eventid = 0;
			$testhomework2->duedate = time() + 86400000; // 1 day from now
	        $testhomework2->time = 20000;


        //Assert that a course belonging to the correct course is returned
        array_push($homeworks, $testhomework1);
        $tmpArray = block_homework::filter_homework_content('http://localhost/course/view.php?id=3',$homeworks);
        $this->assertEquals($tmpArray, $homeworks);

        //Assert that homework can be removed if the ids don't match
        array_push($homeworks, $testhomework2);
        $tmpArray = block_homework::filter_homework_content('http://localhost/course/view.php?id=3',$homeworks);
        $this->assertNotContains($testhomework2, $tmpArray);

		$this->assertEquals($homeworks[0], $testhomework1);
		$this->assertEquals($homeworks[1], $testhomework2);
	    $homeworks = get_homework::sortDueDate($homeworks);

		$this->assertEquals($homeworks[1], $testhomework1);
	    $this->assertEquals($homeworks[0], $testhomework2);

	    $homeworks = get_homework::sortTime($homeworks);
	    $this->assertEquals($homeworks[0], $testhomework1);
	    $this->assertEquals($homeworks[1], $testhomework2);
    }


}