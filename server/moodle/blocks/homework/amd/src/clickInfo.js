import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'block_homework/modals';

/**
 * Homework/amd/src/modal_homework.js
 *
 * @package
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */


/**
 * Fetches and initializes the completion modal for the homework module that was clicked on.
 * @param userID ID of currently logged-in user.
 * @returns {Promise<void>} A promise that, when fulfilled, opens the modal
 */
export const init = async(userID) => {
    // Create the modal using block_homework_get_infohomework_modal
    $(document).ready(function() {
        $('.timebutton').on('click', (e) => {
            Ajax.call([{
                methodname: 'block_homework_get_infohomework_modal',
                args: {
                    homeworkID: e.target.id,
                },
                done: async function(response) {
                    const modal = await MyModal.create({
                        // eslint-disable-next-line max-len
                        title: "<a href='" + response.courseurl + "'>" + response.course + "</a>: <a href='" + response.homeworkurl + "'>" + response.title + "</a> | " + response.duedate,
                        body: `${response.html}`,
                        large: true,
                        removeOnClose: true,
                    });
                    // Show the modal.
                    await modal.show();

                    // Attach event listeners for buttons
                    modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                        e.preventDefault();
                        handleFormSubmit(userID, modal);
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

/**
 * Handle clicking the submit button of the form and updating the database with completion and times
 * @param userID ID of currently logged-in user
 * @param modal The modal that is being submitted
 */
const handleFormSubmit = (userID, modal) => {
    let literatureInputFields = document.querySelectorAll('.homework-time-literature');
    let linksInputFields = document.querySelectorAll('.homework-time-links');
    let videosInputFields = document.querySelectorAll('.homework-time-videos');
    let timeData1 = [];
    let timeData2 = [];
    let timeData3 = [];
    // Finds the data of all input fields, both literature, link and video, and adds the ID and time to an array.
    for (let inputField of literatureInputFields) {
        if (inputField.value !== "") {
            timeData1.push({
                id: inputField.id,
                time: inputField.value,
            });
        }
    }
    for (let inputField of linksInputFields) {
        if (inputField.value !== "") {
            timeData2.push({
                id: inputField.id,
                time: inputField.value,
            });
        }
    }
    for (let inputField of videosInputFields) {
        if (inputField.value !== "") {
            timeData3.push({
                id: inputField.id,
                time: inputField.value,
            });
        }
    }

    // If no data has been filled, do nothing.
    if (!timeData1.length && !timeData2.length && !timeData3.length) {
        modal.destroy();
        return;
    }

    // If data has been filled, call block_homework_save_homeworktime with the user ID and data
    Ajax.call([{
        methodname: 'block_homework_save_homeworktime', // Your PHP function that will handle the data
        args: {
            user: userID,
            timeCompletedLiterature: timeData1,
            timeCompletedLinks: timeData2,
            timeCompletedVideos: timeData3,
        },
        done: function() {
            // Close the modal after successful submission
            modal.destroy();
            location.reload();
        },
        fail: function(error) {
            console.error("Failed to save data:", error);
        }
    }]);
};
