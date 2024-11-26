import Modal from 'core/modal';
import Ajax from "../../../../lib/amd/src/ajax";

/**
 * Creates the event linker modal.
 *
 * @param {int} cmid
 * @param {int} homeworkid
 */
export const init = async(cmid, homeworkid) => {
    // Add an eventlistner to the open event linker button.
    document.querySelector('#open-event-linker').addEventListener('click', async()=>{

        // Call the server to get all avalible events.
        Ajax.call([{
            methodname: 'mod_homework_get_events_for_homework',
            args: {homeworkid: homeworkid},
            done: async function(response) {
                     // Create a modal for the user to link events and homework.
                    const modal = await Modal.create({
                        title: 'Homework event linker',
                        body: response.events,
                        footer: '<button type="button" class="btn btn-primary" data-action="submit">Submit</button>\n' +
                                '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>',
                        show: true,
                        removeOnClose: true,
                    });

                    modal.show();
                    // If there is nothing to link then hide submit and cancel buttons.
                    if (response.events.includes("There are no available courses to link")) {
                        modal.hideFooter();
                    }

                    // Attach event listeners for buttons
                    modal.getRoot().on('click', '[data-action="submit"]', (e) => {
                        e.preventDefault();
                        submitEventLink(modal, homeworkid, cmid);
                    });
                    modal.getRoot().on('click', '[data-action="cancel"]', (e) => {
                        e.preventDefault();
                        modal.destroy();
                        location.reload();
                    });
            },
            fail: (error) => {
                console.error("Fail:", error);
            },
        }]);


    });


};

/**
 * Sumbits the event and homework to link.
 *
 * @param {modal} modal
 * @param {int} homeworkid
 * @param {int} cmid
 */
function submitEventLink(modal, homeworkid, cmid) {

    // Get the selected event
    let form = document.getElementById("evntlinkerform");
    let selectedEvent = form.querySelector('input[name="eventtolink"]:checked');

    // If there are non click then complain

    if (!selectedEvent) {
        alert("Please select an event!");
    } else {
        // Submit the event to link
        Ajax.call([{
            methodname: 'mod_homework_homework_event_link',
            args: {homeworkid: homeworkid, course_module_id: cmid, eventid: selectedEvent.value},
            done: async function(response) {
                modal.destroy();
                location.reload();
            },
            fail: (error) =>{
                console.log("Error: ", error);
            }
        }]);
    }


}