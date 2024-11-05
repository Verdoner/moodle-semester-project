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
 * Initializes the Homework Chooser Modal.
 * @param {int} cmid
 * @param {string} title
 * @param {int} currentHomework
 * @returns {Promise<void>}
 */
export const init = async(cmid, title, currentHomework) => {
    $('#open-homework-chooser').on('click', () => {
        Ajax.call([{
            methodname: 'mod_homework_get_homework_chooser',
            args: {cmid: cmid},
            done: async function(response) {
                const modal = await MyModal.create({
                    title: title,
                    body: `${response.html}`,
                    large: true,
                    removeOnClose: true
                });


                // Initialize elements once the modal content is rendered.
                modal.getRoot().on(ModalEvents.shown, () => {
                    // Initialize the elements after modal is displayed.
                    const startPageInput = modal.getRoot().find('#startPage')[0];
                    const endPageInput = modal.getRoot().find('#endPage')[0];
                    const radioButtons = modal.getRoot().find('input[name="option"]');
                    const testTextarea = modal.getRoot().find('#page-range-input')[0];
                    const testLink = modal.getRoot().find('#linkDiv')[0];

                    // Attach event listeners for page input validation.
                    startPageInput.addEventListener('input', validatePageRange);
                    endPageInput.addEventListener('input', validatePageRange);

                    // Attach event listeners for radio buttons.
                    radioButtons.each((_, radio) => {
                        radio.addEventListener('change', toggleInputs);
                    });

                    // Function to validate page range.
                    /**
                     *
                     */
                    function validatePageRange() {
                        const startPage = parseInt(startPageInput.value, 10);
                        const endPage = parseInt(endPageInput.value, 10);

                        if (endPageInput.value !== "" && startPageInput.value !== "") {
                            if (endPage < startPage) {
                                endPageInput.setCustomValidity("End Page must be greater than or equal to Start Page");
                            } else {
                                endPageInput.setCustomValidity(""); // Clear error message if valid
                            }
                        } else {
                            endPageInput.setCustomValidity(""); // Clear error if either field is empty
                        }
                    }

                    // Function to toggle between text and link inputs.
                    /**
                     *
                     */
                    function toggleInputs() {
                        if (document.getElementById("option1").checked) {
                            testTextarea.style.display = "block";
                            testLink.style.display = "none";
                        } else if (document.getElementById("option2").checked) {
                            testTextarea.style.display = "none";
                            testLink.style.display = "block";
                        }
                    }
                });

                // Show the modal.
                await modal.show();

                // Attach event listeners for buttons
                modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                    e.preventDefault();
                    handleFormSubmit(modal, currentHomework);
                });

                modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                    e.preventDefault();
                    modal.destroy();
                });
            },
            fail: (error) => {
                throw new Error(`Failed to load homework chooser content: ${error}`);
            }
        }]);
    });
};

/**
 * Handles form submission inside the modal.
 *
 * @param {Modal} modal - The instance of the modal containing the form.
 * @param currentHomework - The id of the homework which is being edited.
 */
const handleFormSubmit = (modal, currentHomework) => {
    let inputField = modal.getRoot().find('#inputField')[0];

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
                inputfield: inputField.value,
                startpage: startPage,
                endpage: endPage,
                homework: currentHomework,
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
                inputfield: inputField.value,
                link: link,
                homework: currentHomework,
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
