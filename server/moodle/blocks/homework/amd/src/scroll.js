// This file is part of Moodle Course Rollover Plugin
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
// @author    group 11

define(['jquery'], function($) {
    return {
        init: function() {
            // Select main scroll container and navigation buttons
            const outerbox = document.getElementById('outer-box');
            const prevbtn = document.getElementById('prevbtn');
            const nextbtn = document.getElementById('nextbtn');
            const todaybtn = document.getElementById('todaybtn');

            // Scroll left by 200px on "prev" button click
            prevbtn.addEventListener('click', function() {
                outerbox.scrollBy({ left: -200, behavior: 'smooth' }); //Scroll left
            });

            // Scroll right by 200px on "next" button click
            nextbtn.addEventListener('click', function() {
                outerbox.scrollBy({ left: 200, behavior: 'smooth' }); //Scroll right
            });

            // Reset scroll to the start (0,0) on today button click
            todaybtn.addEventListener('click', function(){
               outerbox.scrollTo(0,0);
            });
        }
    };
});