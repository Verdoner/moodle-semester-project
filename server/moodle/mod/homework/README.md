# Homework #

This plugin adds a new activity ressource as an option to add to a course,
as well as displaying such homework on the dashboard.

The plugin expands on the database with tables that allow teachers to create
homework for courses and upload materials needed for the homework. These tables
include; 'homework', 'homework_materials', and 'completions'. These tables are 
defined in server/moodle/mod/homework/db/install.xml, and are described in the ER diagram.

The activity module adds a new activity option when adding activities to a
course under the course page. This activity option is homework, which the user
can choose to edit to include a description, start time, due date, and material 
for the homework.

Material can be created while creating homework, such that it is added to the
database, and can be reused in homework later, e.g. a course book that is reused across
multiple homeworks. The types of material that can be created are litterature, 
links, and videos. Videos are embedded in Moodle, and can be viewed directly on the page.

Homework that has been added as an activity to the course is displayed on the
dashboard, as well as in the calendar. Homework being displayed in the calendar
is what the block module handles.

Homework can be filtered on the dashboard based on courses, e.g. the user only 
wishes to see homework from the 'Database Systems' course, and filtered to shown future, 
past or all homework. Additionally, displayed homework can be sorted based on certain 
parameters, such as due date. Students can also download all materials assigned to a
homework activity as a single ZIP file.

The activity module can be found in:
server/moodle/mod/homework

The block module can be found in:
server/moodle/blocks/homework

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/homework

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2024 PV 

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
