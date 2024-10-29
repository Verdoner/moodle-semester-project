import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'mod_homework/modal_homework';
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
 * Initializes the Info Homework Modal
 */

export const init = async(cmid, title) => {
    $('infobox').on('click', () => {
        Ajax.call([{
            methodname: 'block_homework_get_info-homework',
            args: {cmid: cmid},
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
                /*modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                    e.preventDefault();
                    handleFormSubmit(modal);
                });

                modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                    e.preventDefault();
                    modal.destroy();
                });*/
            },
            fail: (error) => {
                throw new Error(`Failed to load info homework content: ${error}`);
            }
        }]);
    });
};

/**
 * Handles form submission inside the modal.
 *
 * @param {Modal} modal - The instance of the modal containing the form.
 */

const handleFormSubmit = (modal) => {
    let inputField = modal.getRoot().find('#inputField').val();

    if (inputField.value === "") {
        inputField.setCustomValidity("Please fill in the input field.");
        inputField.reportValidity(); // Shows the custom message
        event.preventDefault(); // Prevents form submission
    } else {
        inputField.setCustomValidity(""); // Clear the custom message
    }

    if (modal.getRoot().find('#option1').is(':checked')) {
        let startPage = modal.getRoot().find('#startPage').val();
        let endPage = modal.getRoot().find('#endPage').val();

        // AJAX call to send data to the server.
        Ajax.call([{
            methodname: 'mod_homework_save_homework_literature',
            args: {
                inputfield: inputField,
                startpage: startPage,
                endpage: endPage,
            },
            done: function() {
                // Close the modal after successful submission.
                modal.hide();
            },
            fail: function(error) {
                throw new Error(`Failed to save data: ${error}`);
            }
        }]);
    } else if (modal.getRoot().find('#option2').is(':checked')) {
        let link = modal.getRoot().find('#link').val();

        // AJAX call to send data to the server.
        Ajax.call([{
            methodname: 'mod_homework_save_homework_link',
            args: {
                inputfield: inputField,
                link: link,
            },
            done: function() {
                // Close the modal after successful submission.
                modal.hide();
            },
            fail: function(error) {
                throw new Error(`Failed to save data: ${error}`);
            }
        }]);
    }
};



