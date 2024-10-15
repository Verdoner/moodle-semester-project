// homeworkchooser.js (Updated Version)

import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'mod_homework/modal_homework';
import ModalEvents from 'core/modal_events';
// import ModalCancel from 'core/modal_cancel';
/**
 * Initializes the Homework Chooser Modal.
 *
 * @param {int} cmid - Course Module ID
 * @param {string} title - Title for the modal
 */
export const init = async (cmid, title) => {
    $('#open-homework-chooser').on('click', () => {
        Ajax.call([{
            methodname: 'mod_homework_get_homework_chooser',
            args: {cmid: cmid},
            done: async function(response) {
                const modal = await MyModal.create({
                    title: title,
                    body: `${response.html}`,
                    // footer: 'An example footer content',
                    large: true,
                });

                // Show the modal
                modal.show();

                // Attach an event listener to handle the modal hidden event
                modal.getRoot().on(ModalEvents.hidden, () => {
                    console.log('Modal closed!');
                });

                // Attach event listeners for buttons
                modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                    e.preventDefault();
                    handleFormSubmit(modal);
                });

                modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                    e.preventDefault();
                    modal.hide();
                });
            },
            fail: (error) => {
                console.error("Failed to load homework chooser content:", error);
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
    const inputField = modal.getRoot().find('#inputField').val();

    if (inputField === "") {
        alert("Please fill in input field.");
        return;
    }
    // AJAX call to send data to the server
    Ajax.call([{
        methodname: 'mod_homework_save_homework_chooser',  // Your PHP function that will handle the data
        args: {
            inputfield: inputField,
        },
        done: function(response) {
            console.log("Data saved successfully:", response);
            // Close the modal after successful submission
            modal.hide();
        },
        fail: function(error) {
            console.error("Failed to save data:", error);
        }
    }]);
};