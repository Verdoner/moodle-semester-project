// homeworkchooseredit.js (Updated Version)

import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'mod_homework/modal_homework';
import ModalEvents from 'core/modal_events';
import Dropzone from 'core/dropzone';

let dropZoneFiles = []; // Store files to upload later
let uploadedFileIds = []; // Store file IDs after successful upload

/**
 * Initializes the Homework Edit Modal.
 *
 * @param {int} cmid
 * @param {string} title
 * @param {int} currentHomework
 * @param {array} homeworkids
 * @returns {Promise<void>}
 */
export const init = async(cmid, title, currentHomework, homeworkids) => {
    Object.values(homeworkids).forEach(homeworkid => {
        $('#edit-homework-chooser-' + homeworkid.id).on('click', () => {
            Ajax.call([{
                methodname: 'mod_homework_get_homework_chooser',
                args: {cmid: cmid},
                done: async function (response) {
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
                        const dropzoneContainer = modal.getRoot().find('#dropzone-container')[0];

                        modal.getRoot().find('#inputField')[0].value = homeworkid.description;
                        modal.getRoot().find('#link')[0].value = homeworkid.link;
                        modal.getRoot().find('#startPage')[0].value = homeworkid.startpage;
                        modal.getRoot().find('#endPage')[0].value = homeworkid.endpage;
                        modal.getRoot().find('#startTime')[0].value = homeworkid.starttime;
                        modal.getRoot().find('#endTime')[0].value = homeworkid.endtime;

                        if (homeworkid.file_id) {
                            uploadedFileIds.push(homeworkid.file_id);
                        }

                        initializeDropzone(dropzoneContainer);
                        displayUploadedFile(homeworkid);
                    });

                    // Attach an event listener to handle the modal hidden event.
                    modal.getRoot().on(ModalEvents.hidden, () => {
                        console.log('Modal closed!');
                    });

                    // Attach event listeners for buttons
                    modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                        e.preventDefault();

                        handleFormSubmit(modal, currentHomework, homeworkid["id"]);
                    });

                    modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                        e.preventDefault();
                        modal.destroy();

                        location.reload();
                    });
                },
                fail: (error) => {
                    console.error("Failed to load homework chooser content:", error);
                }
            }]);
        });
    })
};

const initializeDropzone = (container) => {
    const dropZone = new Dropzone(container, "*/*", (files) => {
        dropZoneFiles.push(files[0]); // Store file for later upload

        displayUploadedFile(files[0]);
    });

    dropZone.setLabel("Drop file here (Optional)");
    dropZone.init();
};

// Function to display the uploaded file with a delete button
const displayUploadedFile = (file) => {
    const previewContainer = document.getElementById("file-content"); // Container for preview

    // Clear previous preview
    previewContainer.innerHTML = "";

    if (file.name || file.filename) {
        // Create wrapper div for the preview and delete button
        const fileWrapper = document.createElement("div");
        fileWrapper.style.position = "relative";
        fileWrapper.style.display = "ruby";

        // Add the file preview
        const paragraph = document.createElement("p");
        if (file.name) {
            paragraph.textContent = `${file.name}`;
        } else if (file.filename) {
            paragraph.textContent = `${file.filename}`;
        }
        fileWrapper.appendChild(paragraph);

        // Create the delete "X" button
        const deleteButton = document.createElement("span");
        deleteButton.textContent = "X";
        deleteButton.style.cursor = "pointer";
        deleteButton.style.background = "red";
        deleteButton.style.color = "white";
        deleteButton.style.padding = "2px 5px";
        deleteButton.style.fontWeight = "bold";
        deleteButton.style.marginLeft = "5px";

        // Handle file deletion when "X" is clicked
        deleteButton.addEventListener("click", () => {
            if (confirm("Are you sure you want to delete this file?")) {
                // AJAX call to delete the file on the server
                Ajax.call([{
                    methodname: 'mod_homework_delete_file',
                    args: {
                        id: file.id,
                        file_id: file.file_id
                    }, // Pass the correct file ID
                    done: function(response) {
                        console.log("File deleted successfully");
                        dropZoneFiles = []; // Clear the files array
                        previewContainer.innerHTML = ""; // Remove the preview
                        uploadedFileIds = []; // Clear the files array
                    },
                    fail: function(error) {
                        console.error("Failed to delete file:", error);
                    }
                }]);
            }
        });

        fileWrapper.appendChild(deleteButton);
        previewContainer.appendChild(fileWrapper);
    }
};


const uploadDropzoneFile = async () => {
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
    dropZoneFiles = []; // Clear stored file after upload
};

/**
 * Handles form submission inside the modal.
 * @param {Modal} modal - The instance of the modal containing the form.
 * @param currentHomework - The id of the homework which is being edited.
 */
const handleFormSubmit = async (modal, currentHomework, homeworkid) => {
    let inputField = modal.getRoot().find('#inputField')[0];
    let linkField = modal.getRoot().find('#link')[0];
    let startPageInput = modal.getRoot().find('#startPage')[0];
    let endPageInput = modal.getRoot().find('#endPage')[0];
    let startTimeInput = modal.getRoot().find('#startTime')[0];
    let endTimeInput = modal.getRoot().find('#endTime')[0];

    // Set up a custom validity message if the field is empty
    if (inputField.value.trim() === "") {
        inputField.setCustomValidity("Input field must not be empty");
    } else {
        inputField.setCustomValidity(""); // Clear the custom message when input is valid
    }

    // Manually check the validity of the input field
    inputField.reportValidity();

    // If the field is invalid, stop the function execution
    if (!inputField.checkValidity()) {
        return; // Exit if input field is invalid
    }

    if (!validatePageRange(startPageInput, endPageInput)) {
        return;
    }

    if (!validateTimeRange(startTimeInput, endTimeInput)) {
        return;
    }

    await uploadDropzoneFile();

    Ajax.call([{
        methodname: 'mod_homework_edit_homework_material',
        args: {
            id: homeworkid,
            inputfield: inputField.value,
            link: linkField.value.trim() !== "" ? linkField.value.trim() : null,
            startpage: startPageInput.value.trim() !== "" ? startPageInput.value.trim() : null,
            endpage: endPageInput.value.trim() !== "" ? endPageInput.value.trim() : null,
            starttime: startTimeInput.value.trim() !== "" ? startTimeInput.value.trim() : null,
            endtime: endTimeInput.value.trim() !== "" ? endTimeInput.value.trim() : null,
            homeworkid: currentHomework,
            fileid: uploadedFileIds.length ? uploadedFileIds[0] : null
        },
        done: function(response) {
            console.log("Data saved successfully:", response);
            modal.destroy();

            location.reload();
        },
        fail: function(error) {
            console.error("Failed to save data:", error);
        }
    }]);

    function validatePageRange(startPageInput, endPageInput) {
        const startPage = parseInt(startPageInput.value, 10);
        const endPage = parseInt(endPageInput.value, 10);

        if (endPageInput.value !== "" && startPageInput.value !== "") {
            if (endPage < startPage) {
                endPageInput.setCustomValidity("End Page must be greater than or equal to Start Page");
                endPageInput.reportValidity();
                return false;
            } else {
                endPageInput.setCustomValidity(""); // Clear error message if valid
            }
        } else {
            endPageInput.setCustomValidity(""); // Clear error if either field is empty
        }

        endPageInput.reportValidity();
        return endPageInput.checkValidity(); // Return true if valid
    }

    function validateTimeRange(startTimeInput, endTimeInput) {
        const startTime = parseInt(startTimeInput.value, 10);
        const endTime = parseInt(endTimeInput.value, 10);

        if (endTimeInput.value !== "" && startTimeInput.value !== "") {
            if (endTime < startTime) {
                endTimeInput.setCustomValidity("End Time must be greater than or equal to Start Time");
                endTimeInput.reportValidity();
                return false;
            } else {
                endTimeInput.setCustomValidity(""); // Clear error message if valid
            }
        } else {
            endTimeInput.setCustomValidity(""); // Clear error if either field is empty
        }

        endTimeInput.reportValidity();
        return endTimeInput.checkValidity(); // Return true if valid
    }
};