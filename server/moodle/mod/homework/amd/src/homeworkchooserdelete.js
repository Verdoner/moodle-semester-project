// homeworkchooserdelete.js (Updated Version)

import $ from 'jquery';
import Ajax from 'core/ajax';
import home from "../../../../lib/amd/src/key_codes";

/**
 * Initializes the Homework Delete Modal.
 *
 * @param {int} cmid
 * @param {array} homeworkids
 * @returns {Promise<void>}
 */
export const init = async (cmid, homeworkids) => {
    console.log(homeworkids);
    Object.values(homeworkids).forEach(homeworkid => {
        $('#delete-homework-chooser-' + homeworkid.id).on('click', () => {
            // Show a confirmation alert
            const confirmDeletion = confirm("Are you sure you want to delete this homework record? This action cannot be undone.");

            // Proceed only if the user confirms
            if (confirmDeletion) {
                Ajax.call([{
                    methodname: 'mod_homework_delete_homework_material',
                    args: {
                        id: homeworkid.id,
                        fileid: (homeworkid.file_id) ? homeworkid.file_id : null,
                    },
                    done: async function (response) {
                        // Handle successful deletion here, if needed
                        console.log("Homework record deleted successfully.");
                        location.reload();
                    },
                    fail: (error) => {
                        console.error("Failed to delete homework record:", error);
                    }
                }]);
            }
        });
    });
};
