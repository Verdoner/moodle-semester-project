// homeworkchooser.js (Updated Version)

import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'mod_homework/modal_homework';
import ModalEvents from 'core/modal_events';
import Dropzone from 'core/dropzone';

/**
 * Initializes the Homework Chooser Modal.
 *
 * @param {int} cmid - Course Module ID
 * @param {string} title - Title for the modal
 * @param {int} cminstance
 */
export const init = async (cmid, title, cminstance) => {
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
                    removeOnClose: true,
                });

                // Show the modal
                modal.show();

                // Initialize elements once the modal content is rendered
                modal.getRoot().on(ModalEvents.shown, () => {
                    // Initialize the elements after modal is displayed
                    const startPageInput = modal.getRoot().find('#startPage')[0];
                    const endPageInput = modal.getRoot().find('#endPage')[0];
                    const radioButtons = modal.getRoot().find('input[name="option"]');
                    const testTextarea = modal.getRoot().find('#page-range-input')[0];
                    const testLink = modal.getRoot().find('#linkDiv')[0];
                    const dropzoneContainer = modal.getRoot().find('#dropzone-container')[0];

                    // Attach event listeners for page input validation
                    startPageInput.addEventListener('input', validatePageRange);
                    endPageInput.addEventListener('input', validatePageRange);

                    // Attach event listeners for radio buttons
                    radioButtons.each((_, radio) => {
                        radio.addEventListener('change', toggleInputs);
                    });

                    initializeDropzone(dropzoneContainer);

                    // Function to validate page range
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

                    // Function to toggle between text and link inputs
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

                // Attach an event listener to handle the modal hidden event
                modal.getRoot().on(ModalEvents.hidden, () => {
                    console.log('Modal closed!');
                });

                // Attach event listeners for buttons
                modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                    e.preventDefault();
                    handleFormSubmit(cminstance,modal);
                });

                modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                    e.preventDefault();
                    modal.destroy();
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
 * @param {int} instance
 * @param {Modal} modal - The instance of the modal containing the form.
 */
const handleFormSubmit = (cminstance,modal) => {
    let inputField = modal.getRoot().find('#inputField').val();

    if (modal.getRoot().find('#option1').is(':checked')) {

        let startPage = modal.getRoot().find('#startPage').val();
        let endPage = modal.getRoot().find('#endPage').val();

        // AJAX call to send data to the server
        Ajax.call([{
            methodname: 'mod_homework_save_homework_literature',  // Your PHP function that will handle the data
            args: {
                inputfield: inputField,
                startpage: startPage,
                endpage: endPage,
                instance: cminstance,
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

    } else if (modal.getRoot().find('#option2').is(':checked')) {

        let link = modal.getRoot().find('#link').val();

        // AJAX call to send data to the server
        Ajax.call([{
            methodname: 'mod_homework_save_homework_link',  // Your PHP function that will handle the data
            args: {
                inputfield: inputField,
                link: link,
                instance: cminstance,
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
    }
};

const initializeDropzone = (container) => {
    const dropZone = new Dropzone(container, "application/pdf", async (files) => {
        for (let file of files) {
            if (file.type === "application/pdf") { // Validate file type
                try {
                    const formData = new FormData();
                    formData.append("file", file);

                    const response = await fetch("/mod/homework/save_homework_file.php", {
                        method: "POST",
                        body: formData
                    });

                    if (response.ok) {
                        console.log("File uploaded successfully:", file.name);
                    } else {
                        console.error("Failed to upload file:", file.name);
                    }
                } catch (error) {
                    console.error("Error uploading file:", file.name, error);
                }
            } else {
                console.warn("Invalid file type:", file.type);
            }
        }
    });

    dropZone.setLabel("(Optional) Drop PDF files here");
    dropZone.init();
};