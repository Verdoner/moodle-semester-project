<?php
require('../mod/homeworkdb/lang/en/homeworkdb.php');
//Replace placeholder with path for file with functions

//This tests getting and setting data in the database for the homework feed plugin
test();

function test(){
    //calls all test functions
    test_db_set_homework();
    test_db_get_homework();

    test_db_set_file();
    test_db_get_file();

    test_db_get_course_section();
    test_db_set_course_section();
}

function test_db_get_homework(){

    //funtion to test, replace with implemented function
    $homework = hwfeed_get_homework();

    $expected = "expected";//placeholder
    //Checks if the test succeeded
    if ($homework == $expected){
        echo "Test test_db_get_homework Succeeded";
        return true;
    }
    echo "Test test_db_get_homework Failed, Expected: ".$expected."got: ".$homework;
    return false;
}

function test_db_set_homework(){

    //funtion to test, replace with implemented function
    $homework = "some homework";
    $resultcode = hwfeed_set_homework($homework);

    //Checks if the test succeeded
    if ($resultcode == 1){
        echo "Test test_db_set_homework Succeeded";
        return true;
    }
    echo "Test test_db_set_homework Failed, error code ".$resultcode;
    return false;
}

function test_db_get_file(){

    //funtion to test, replace with implemented function
    $file = hwfeed_get_file();

    $expected = "expected";//placeholder
    //Checks if the test succeeded
    if ($file == $expected){
        echo "Test test_db_get_file Succeeded";
        return true;
    }
    echo "Test test_db_get_file Failed, Expected: ".$expected."got: ".$file;
    return false;
}

function test_db_set_file(){

    //funtion to test, replace with implemented function
    $file = "some file";
    $resultcode = hwfeed_set_file($file);

    //Checks if the test succeeded
    if ($resultcode == 1){
        echo "Test test_db_set_file Succeeded";
        return true;
    }
    echo "Test test_db_set_file Failed, error code ".$resultcode;
    return false;
}

function test_db_get_course_section(){

    //funtion to test, replace with implemented function
    $course_section = hwfeed_get_course_section();

    $expected = "expected";//placeholder
    //Checks if the test succeeded
    if ($course_section == $expected){
        echo "Test test_db_get_course_section Succeeded";
        return true;
    }
    echo "Test test_db_get_course_section Failed, Expected: ".$expected."got: ".$course_section;
    return false;
}

function test_db_set_course_section(){

    //funtion to test, replace with implemented function
    $course_section = "some course section :)";
    $resultcode = hwfeed_set_coursesection($course_section);

    //Checks if the test succeeded
    if ($resultcode == 1){
        echo "Test test_db_set_course_section Succeeded";
        return true;
    }
    echo "Test test_db_set_course_section Failed, error code ".$resultcode;
    return false;
}
