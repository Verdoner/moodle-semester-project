import Modal from 'core/modal';
import Ajax from "../../../../lib/amd/src/ajax";

export const init = async(cmid, homeworkid) => {

    console.log("hello");
    console.log(homeworkid);

    document.querySelector('#open-event-linker').addEventListener('click', async()=>{


        Ajax.call([{
            methodname: 'mod_homework_get_events_for_homework',
            args: {homeworkid: homeworkid},
            done: async function(response) {
                    const modal = await Modal.create({
                        title: 'Homework event linker',
                        body: response.events,
                        footer: '<button type="button" class="btn btn-primary" data-action="submit">Submit</button>\n' +
                                '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>',
                        show: true,
                        removeOnClose: true,
                    });

                    modal.show();

                    if (response.events.includes("There are no available courses to link")){
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

function submitEventLink(modal, homeworkid, cmid){

    // Get the selected event
    let form = document.getElementById("evntlinkerform");
    let selectedEvent = form.querySelector('input[name="eventtolink"]:checked');

    // If there are non click then complain

    if(!selectedEvent){
        alert("Please select an event!");
    }else{
        // submit the event to link
        console.log("homework id = " + homeworkid + "cmid = " + cmid + "selected eventid = " +selectedEvent.value);
        Ajax.call([{
            methodname: 'mod_homework_homework_event_link',
            args: {homeworkid: homeworkid, cmid: cmid, eventid: selectedEvent.value},
            done: async function(response) {
                console.log(response.status + response.message);
                modal.destroy();
                location.reload();
            },
            fail:(error) =>{
                console.log("Error: ", error);
            }
        }]);
    }





}