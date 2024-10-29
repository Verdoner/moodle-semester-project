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
 * Initializes the Info Homework Modal
 */

export const init = async(title) => {
    $('button').on('click', () => {
        Ajax.call([{
            methodname: 'block_homework_get_infohomework_modal',
            done: async function(response) {
                const modal = await MyModal.create({
                    title: title,
                    body: `${response.html}`,
                    large: true,
                    removeOnClose: true,
                });
                console.log("ffff")
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
                throw new Error(`Failed to load info homework content: ${JSON.stringify(error)}`);
            }
        }]);
    });
};




