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
 * This class captures all the various information to render the front page of the homework activity.
 *
 * This class is not currently renderable or templatable, but it very nearly could be,
 * which is why it is in the output namespace. It is used to send data to the renderer.
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_homework;

class view_page {
    /** @var array $infomessages of messages with information to display about the quiz. */
    public $infomessages;

    /** @var bool $canreviewmine whether the current user has the capability to
     *       review their own attempts. */
    public $canreviewmine;

    /** @var bool $canedit whether the current user has the capability to edit the quiz. */
    public $canedit;
    /** @var moodle_url $editurl the URL for editing this quiz. */
    public $editurl;
    /** @var int $attemptcolumn contains the number of attempts done. */
    public $attemptcolumn;
    /** @var int $gradecolumn contains the grades of any attempts. */
    public $gradecolumn;
    /** @var int $markcolumn contains the marks of any attempt. */
    public $markcolumn;
    /** @var int $overallstats contains all marks for any attempt. */
    public $overallstats;
    /** @var string $feedbackcolumn contains any feedback for and attempt. */
    public $feedbackcolumn;
    /** @var string $timenow contains a timestamp in string format. */
    public $timenow;
    /** @var int $numattempts contains the total number of attempts. */
    public $numattempts;
    /** @var float $mygrade contains the user's final grade for a quiz. */
    public $mygrade;
    /** @var bool $moreattempts whether this user is allowed more attempts. */
    public $moreattempts;
    /** @var int $mygradeoverridden contains an overriden grade. */
    public $mygradeoverridden;
    /** @var string $gradebookfeedback contains any feedback for a gradebook. */
    public $gradebookfeedback;
    /** @var bool $unfinished contains 1 if an attempt is unfinished. */
    public $unfinished;
    /** @var stdClass $lastfinishedattempt the last attempt from the attempts array. */
    public $lastfinishedattempt;
    /** @var array $preventmessages of messages telling the user why they can't
     *       attempt the quiz now. */
    public $preventmessages;
    /** @var string $buttontext caption for the start attempt button. If this is null, show no
     *      button, or if it is '' show a back to the course button. */
    public $buttontext;
    /** @var moodle_url $startattempturl URL to start an attempt. */
    public $startattempturl;
    /** @var preflight_check_form|null $preflightcheckform confirmation form that must be
     *       submitted before an attempt is started, if required. */
    public $preflightcheckform;
    /** @var moodle_url $startattempturl URL for any Back to the course button. */
    public $backtocourseurl;
    /** @var bool $showbacktocourse should we show a back to the course button? */
    public $showbacktocourse;
    /** @var bool whether the attempt must take place in a popup window. */
    public $popuprequired;
    /** @var array options to use for the popup window, if required. */
    public $popupoptions;
    /** @var bool $hashomework whether the module has any homework. */
    public $hashomework;
}
