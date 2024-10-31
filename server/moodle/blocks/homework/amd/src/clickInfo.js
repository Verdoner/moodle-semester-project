import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'block_homework/modals';
import ModalEvents from 'core/modal_events';

/**
 * Homework/amd/src/modal_homework.js
 *
 * @package
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */


/**
 *
 * @param {int} homework_id
 * @param title
 * @param data
 * @returns {Promise<void>}
 */
export const init = async(homework_id, title, data, user_id, completions) => {
    let homeworkid;
    let literaturelist = [];
    let linkslist = [];
    let videoslist =[];
    console.log(data);
    console.log("UserID: " + user_id);
    console.log(completions);

    const buttons = document.getElementsByClassName("timebutton");

    for(let i = 0; i < buttons.length; i++) {
        (function(index) {
            buttons[index].addEventListener("click", function(event) {
                homeworkid = event.target.id;
                literaturelist = [];
                linkslist = [];
                videoslist = [];
                for (let item of data) {
                    if(!(item.hasOwnProperty('id'))){
                        throw new Error("missing id in homework")
                    }
                    if (item.id !== homeworkid){
                        continue;
                    }
                        console.log(1)
                        if(!(item.hasOwnProperty('literature'))) {
                            throw new Error("missing id in homework")
                        }
                        for (let literature of Object.values(item.literature)) {
                            let foundLiterature =  Object.values(completions).some(entry => entry.literature_id === literature.id);
                            if (!foundLiterature) {
                                literaturelist.push(literature);
                            }
                        }
                        if(!(item.hasOwnProperty('links'))) {
                            throw new Error("missing id in homework")
                        }
                        for (let links of Object.values(item.links)) {
                        let foundLinks =  Object.values(completions).some(entry => entry.link_id === links.id);
                        if (!foundLinks) {
                        linkslist.push(links);
                        }
                    }
                        if(!item.hasOwnProperty('videos')) {
                            throw new Error("missing id in homework")
                        }
                    for (let videos of Object.values(item.videos)) {
                        let foundVideos =  Object.values(completions).some(entry => entry.video_id === videos.id);
                        if (!foundVideos) {
                        videoslist.push(videos);
                        }
                    }
                        console.log(literaturelist);

                }
            })
        })(i);
    }


    $(document).ready(function() {
        $('.timebutton').on('click', () => {
            Ajax.call([{
                methodname: 'block_homework_get_infohomework_modal',
                args:{
                    homework_id: homeworkid,
                    data1: literaturelist,
                    data2: linkslist,
                    data3: videoslist,
                },
                done: async function(response) {
                    const modal = await MyModal.create({
                        title: title,
                        body: `${response.html}`,
                        large: true,
                        removeOnClose: true,
                    });
                    // Show the modal.
                    await modal.show();

                    // Initialize elements once the modal content is rendered.
                    modal.getRoot().on(ModalEvents.shown, () => {
                        // Initialize the elements after modal is displayed.

                        // Attach event listeners for page input validation.



                    });

                    // Attach event listeners for buttons
                    modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                        e.preventDefault();
                        handleFormSubmit(user_id, modal);


                    });

                    modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                        e.preventDefault();
                        modal.destroy();
                    });
                },
                fail: (error) => {
                    throw new Error(`Failed to load info homework content: ${JSON.stringify(error)}`);
                }
            }]);
        });
    });
};


const handleFormSubmit = (user_id, modal) => {
    let literatureInputFields = document.querySelectorAll('.homework-time-literature');
    let linksInputFields = document.querySelectorAll('.homework-time-links');
    let videosInputFields = document.querySelectorAll('.homework-time-videos');
    let timeData1 = [];
    let timeData2 = [];
    let timeData3 = [];
    for (let inputField of literatureInputFields) {
        if(inputField.value !== "") {
            timeData1.push({
                id: inputField.id,
                time: inputField.value,
            })
        }
    }
    for (let inputField of linksInputFields) {
        if(inputField.value !== "") {
            timeData2.push({
                id: inputField.id,
                time: inputField.value,
            })
        }
    }
    for (let inputField of videosInputFields) {
        if(inputField.value !=="") {
            timeData3.push({
                id: inputField.id,
                time: inputField.value,
            })
        }
    }
    console.log(timeData1);
    console.log(timeData2);
    console.log(timeData3);

    if(!timeData1.length && !timeData2.length && !timeData3.length){
        modal.destroy();
        return;
    }
    Ajax.call([{
        methodname: 'block_homework_save_homeworktime',  // Your PHP function that will handle the data
        args: {
            user: user_id,
            timeCompletedLiterature: timeData1,
            timeCompletedLinks: timeData2,
            timeCompletedVideos: timeData3,
        },
        done: function(response) {
            console.log("Data saved successfully:", response);
            // Close the modal after successful submission
            modal.destroy();
            location.reload();
        },
        fail: function(error) {
            console.error("Failed to save data:", error);

        }
    }]);
}