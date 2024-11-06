// homeworkchooser.js (Updated Version)

import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'mod_homework/modal_homework';
import ModalEvents from 'core/modal_events';
import Dropzone from 'core/dropzone';

let dropZoneFiles = []; // Store files to upload later
let uploadedFileIds = []; // Store file IDs after successful upload

/**
 * Initializes the Homework Chooser Modal.
 *
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
                    // footer: 'An example footer content',
                    large: true,
                    removeOnClose: true
                });

                // Show the modal
                modal.show();

                // Initialize elements once the modal content is rendered
                modal.getRoot().on(ModalEvents.shown, () => {
                    // Initialize the elements after modal is displayed
                    const startPageInput = modal.getRoot().find('#startPage')[0];
                    const endPageInput = modal.getRoot().find('#endPage')[0];
                    const startTimeInput = modal.getRoot().find('#startTime')[0];
                    const endTimeInput = modal.getRoot().find('#endTime')[0];
                    const radioButtons = modal.getRoot().find('input[name="option"]');
                    const pageRangeInput = modal.getRoot().find('#page-range-input')[0];
                    const videoTimeInput = modal.getRoot().find('#video-time-input')[0];
                    const linkDiv = modal.getRoot().find('#linkDiv')[0];
                    const dropzonePdfContainer = modal.getRoot().find('#dropzone-pdf-container')[0];
                    const dropzoneVideoContainer = modal.getRoot().find('#dropzone-video-container')[0];

                    // Attach event listeners for page input validation
                    startPageInput.addEventListener('input', validatePageRange);
                    endPageInput.addEventListener('input', validatePageRange);

                    // Attach event listeners for time input validation
                    startTimeInput.addEventListener('input', validateTimeRange);
                    endTimeInput.addEventListener('input', validateTimeRange);

                    // Attach event listeners for radio buttons
                    radioButtons.each((_, radio) => {
                        radio.addEventListener('change', toggleInputs);
                    });

                    initializePDFDropzone(dropzonePdfContainer);
                    initializeVideoDropzone(dropzoneVideoContainer)

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

                    function validateTimeRange() {
                        const startTime = parseInt(startTimeInput.value, 10);
                        const endTime = parseInt(endTimeInput.value, 10);

                        if (endTimeInput.value !== "" && startTimeInput.value !== "") {
                            if (endTime < startTime) {
                                endTimeInput.setCustomValidity("End Time must be greater than or equal to Start Page");
                            } else {
                                endTimeInput.setCustomValidity(""); // Clear error message if valid
                            }
                        } else {
                            endTimeInput.setCustomValidity(""); // Clear error if either field is empty
                        }
                    }

                    // Function to toggle between text and link inputs
                    /**
                     *
                     */
                    function toggleInputs() {
                        if (document.getElementById("option1").checked) {
                            pageRangeInput.style.display = "block";
                            videoTimeInput.style.display = "none";
                            linkDiv.style.display = "none";
                            dropzonePdfContainer.style.display = "block";
                            dropzoneVideoContainer.style.display = "none";

                            dropZoneFiles = [];
                            uploadedFileIds = [];
                        } else if (document.getElementById("option2").checked) {
                            pageRangeInput.style.display = "none";
                            videoTimeInput.style.display = "none";
                            linkDiv.style.display = "block";
                            dropzonePdfContainer.style.display = "none";
                            dropzoneVideoContainer.style.display = "none";

                            dropZoneFiles = [];
                            uploadedFileIds = [];
                        } else if (document.getElementById("option3").checked) {
                            pageRangeInput.style.display = "none";
                            videoTimeInput.style.display = "block";
                            linkDiv.style.display = "none";
                            dropzonePdfContainer.style.display = "none";
                            dropzoneVideoContainer.style.display = "block";

                            dropZoneFiles = [];
                            uploadedFileIds = [];
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

                    handleFormSubmit(modal, currentHomework);
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

const initializePDFDropzone = (container) => {
    const dropZone = new Dropzone(container, "application/pdf", (files) => {
        for (let file of files) {
            if (file.type === "application/pdf") {
                dropZoneFiles.push(file); // Store file for later upload
            } else {
                console.warn("Invalid file type:", file.type);
            }
        }
    });

    dropZone.setLabel("Drop PDF files here (Optional)");
    dropZone.init();
};

const initializeVideoDropzone = (container) => {
    const dropZone = new Dropzone(container, "video/*", (files) => {
        for (let file of files) {
            if (file.type.startsWith("video/")) {
                dropZoneFiles.push(file); // Store file for later upload
            } else {
                console.warn("Invalid file type:", file.type);
            }
        }
    });

    dropZone.setLabel("Drop video files here (Optional)");
    dropZone.init();
};

const uploadDropzoneFiles = async () => {
    for (let file of dropZoneFiles) {
        try {
            const formData = new FormData();
            formData.append("file", file);

            const response = await fetch("/mod/homework/upload_file.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                console.log("File uploaded successfully:", file.name);
                console.log(result);
                uploadedFileIds.push(result.fileid); // Store the file ID
            } else {
                console.error("Failed to upload file:", file.name);
            }
        } catch (error) {
            console.error("Error uploading file:", file.name, error);
        }
    }
    dropZoneFiles = []; // Clear stored files after upload
};

/**
 * Handles form submission inside the modal.
 * @param {int} cminstance
 * @param {Modal} modal - The instance of the modal containing the form.
 * @param currentHomework - The id of the homework which is being edited.
 */
const handleFormSubmit = async (modal, currentHomework) => {
    let inputField = modal.getRoot().find('#inputField')[0];

    if (modal.getRoot().find('#option1').is(':checked')) {

        let startPage = modal.getRoot().find('#startPage').val();
        let endPage = modal.getRoot().find('#endPage').val();

        await uploadDropzoneFiles();

        // AJAX call to send data to the server
        Ajax.call([{
            methodname: 'mod_homework_save_homework_literature',  // Your PHP function that will handle the data
            args: {
                inputfield: inputField.value,
                startpage: startPage,
                endpage: endPage,
                homework: currentHomework,
                fileid: uploadedFileIds.length ? uploadedFileIds[0] : null
            },
            done: function(response) {
                console.log("Data saved successfully:", response);
                dropZoneFiles = [];
                uploadedFileIds = [];
                modal.destroy();
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
                inputfield: inputField.value,
                link: link,
                homework: currentHomework,
            },
            done: function(response) {
                console.log("Data saved successfully:", response);
                modal.destroy();
            },
            fail: function(error) {
                console.error("Failed to save data:", error);
            }
        }]);
    } else if (modal.getRoot().find('#option3').is(':checked')) {
        let startTime = modal.getRoot().find('#startTime').val();
        let endTime = modal.getRoot().find('#endTime').val();

        await uploadDropzoneFiles();

        Ajax.call([{
            methodname: 'mod_homework_save_homework_video',
            args: {
                inputfield: inputField,
                starttime: startTime,
                endtime: endTime,
                instance: cminstance,
                fileid: uploadedFileIds.length ? uploadedFileIds[0] : null
            },
            done: function(response) {
                console.log("Data saved successfully:", response);
                modal.destroy();
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