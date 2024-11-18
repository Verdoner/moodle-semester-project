import $ from 'jquery';
import Ajax from 'core/ajax';
/**
 * Homework/amd/src/filter.js
 *
 * @package
 * @copyright 2024, cs-24-sw-5-13 <cs-24-sw-5-13@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

export const init = async() => {
    let courses;
    Ajax.call([{
        methodname: 'block_homework_get_courses',
        args: {},
        done: async function(response) {
            courses = JSON.parse(response.courses);
            for (const course in courses) {
                $('#filter').append('<option value="' + courses[course].fullname + '">' + courses[course].fullname + '</option>');
            }
        },
        fail: (error) => {
            throw new Error(`Failed to find courses: ${error}`);
        }
    }]);
    $('#filter').on('change', () => {
        Ajax.call([{
            methodname: 'block_homework_filter_homework',
            args: {filter: $('#filter').val()},
            done: async function(response) {
                let homeworks = JSON.parse(response.homework);
                document.getElementById("outer-box").innerHTML = "";
                homeworks.forEach((homework) => {
                    console.log(homework);
                    let box = document.createElement("div");
                    box.classList.add("infobox");

                    let h22 = document.createElement("h2");
                    h22.innerHTML = `${homework.course}`;
                    box.appendChild(h22);

                    let h2 = document.createElement("h2");
                    h2.innerHTML = `${homework.name}`;
                    box.appendChild(h2);

                    let h3 = document.createElement("h3");
                    h3.innerHTML = homework.duedate;
                    box.appendChild(h3);

                    let p = document.createElement("p");
                    p.innerHTML = `${homework.intro}`;
                    box.appendChild(p);

                    let button = document.createElement("button");
                    button.classList.add("Timebutton");
                    button.innerHTML = "Time";
                    box.appendChild(button);

                    document.getElementById("outer-box").appendChild(box);
                });
            },
            fail: (error) => {
                console.log(error);
                throw new Error(`Failed to find filtered homework: ${error}`);
            }
        }]);
    });
};
