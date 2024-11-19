import $ from 'jquery';
import Ajax from 'core/ajax';

/**
 * homework/amd/src/sort.js
 *
 * @package
 * @copyright 2024, cs-24-sw-5-13 <cs-24-sw-5-13@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

export const init = async() => {
    $('#sort').on('change', () => {
        Ajax.call([{
            methodname: 'block_homework_get_homework',
            args: {sort: $('#sort').val()},
            done: async function(response) {
                let homework = JSON.parse(response.homework);
                document.getElementById("outer-box").innerHTML = "";
                homework.forEach((homework) => {
                    let box = document.createElement("div");
                    box.classList.add("infobox");

                    let h2 = document.createElement("h2");
                    h2.innerHTML = `name: ${homework.name}`;
                    box.appendChild(h2);

                    let p = document.createElement("p");
                    p.innerHTML = "Intro: ";
                    box.appendChild(p);

                    document.getElementById("outer-box").appendChild(box);
                });
            },
            fail: (error) => {
                console.log(error);
                throw new Error(`Failed to sort homework: ${error}`);
            }
        }]);
    });
};
