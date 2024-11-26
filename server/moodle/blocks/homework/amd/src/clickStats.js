import $ from 'jquery';
import Ajax from 'core/ajax';
import MyModal from 'block_homework/modals';

/**
 * Homework/amd/src/clickStats.js
 *
 * @package
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */


/**
 * Fetches and initializes the completion modal for the homework module that was clicked on.
 * @param stats Array of stats info.
 * @returns {Promise<void>} A promise that, when fulfilled, opens the modal
 */
export const init = async(stats) => {
    // Create the modal using block_homework_get_stats_modal
    $(document).ready(function() {
        $('.stats-btn').on('click', () => {
            Ajax.call([{
                methodname: 'block_homework_get_stats_modal',
                args: {'stats': stats},
                done: async function(response) {
                    const modal = await MyModal.create({
                        // eslint-disable-next-line max-len
                        title: "Your homework stats",
                        body: `${response.html}`,
                        large: true,
                        removeOnClose: true,
                    });
                    // Show the modal.
                    await modal.show();

                    // Attach event listeners for buttons
                    modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                        e.preventDefault();
                        modal.destroy();
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