define(function() {
    return {
        init: function(homework) {
            // Check that the current page contains a calendar to stop the code on other pages with homework block.
            if (document.querySelector('[data-region="calendar"]') && JSON.stringify(homework).length > 1) {

                // Function that shows the content in the moodle modal.
                const addContentToModal = () => {
                    // Run a timeout of 500ms to make sure the modal and content are there.
                    setTimeout(() => {
                        // Get the modal and determine if is there
                        const modalContent = document.querySelector('.modal-content');
                        if (modalContent) {
                            // Get the inlaying container and determine if it's there.
                            const summaryContainer = modalContent.querySelector('.summary-modal-container');
                            if (summaryContainer) {
                                // Get the eventid from the container
                                let eventid = summaryContainer.getAttribute("data-event-id");

                                // Get the container that contains text data
                                const containerFluid = summaryContainer.querySelector('.container-fluid');
                                // Loop through all links in the text area to see if one contains a link to a course and if so what is the course id
                                let foundCourseLink = false;
                                let Courseid;
                                containerFluid.querySelectorAll('a').forEach((element) => {
                                    if (element.href.includes('/course')) {
                                        const regex = /course\/view\.php\?id=(\d+)/;
                                        const match = element.href.match(regex);
                                        if (match) {
                                            Courseid = match[1]; // Extract the ID
                                        }
                                        foundCourseLink = true;
                                        return false; // Break out of the forEach loop
                                    }
                                });


                                // Get the correct homework which has the correct eventid otherwise cancel
                                let filteredHomework = Object.values(homework).filter((work) => {
                                    return work.eventid === eventid;
                                });
                                if (filteredHomework.length === 0) {
                                    return;
                                }


                                // Determine if the modal is in the dashboard, a course link and a there isn't already a homeworkrow
                                if (foundCourseLink && window.location.href.includes("/my") && !document.getElementById("homeworkRow")) {
                                    // Set the div up accoring to the moodle standard
                                    const homeworkDiv = document.createElement('div');
                                    homeworkDiv.className = 'row mt-1';
                                    homeworkDiv.id = 'homeworkRow';

                                    const homeworkIconDiv = document.createElement('div');
                                    homeworkIconDiv.className = 'col-1';
                                    homeworkIconDiv.innerHTML = '<i class="icon fa fa-align-left fa-fw" title="Description" role="img" aria-label="Description"></i>';

                                    const homeworkLinkDiv = document.createElement('div');
                                    homeworkLinkDiv.className = 'description-content col-11';
                                    // Create the new string with all homework info for that course
                                    let newString = '';
                                    Object.values(filteredHomework).forEach((homeworkInfo) => {
                                        let convertedTime = new Date(homeworkInfo.duedate * 1000);
                                        newString += `<p>HomeWork name = ${homeworkInfo.name} <br>
                                                         Intro = ${homeworkInfo.intro ? homeworkInfo.intro : "No intro"} <br>
                                                         link = <a href="${window.location.href.replace("my/", "mod/homework/view.php?id=")}${homeworkInfo.cmid}">link to homework</a></p>
                                                         dueDate = ${homeworkInfo.duedate ? `Duedate is ${convertedTime}` : 'No duedate'}</p>`;
                                    });
                                    homeworkLinkDiv.innerHTML = newString;

                                    // Add the divs to each other
                                    homeworkDiv.appendChild(homeworkIconDiv);
                                    homeworkDiv.appendChild(homeworkLinkDiv);

                                    containerFluid.appendChild(homeworkDiv);
                                }
                            }
                        }
                    }, 500);
                };

                // Observer setup
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        // Determine if the modal is shown based on the show class
                        if (mutation.target.classList.contains('show')) {
                            addContentToModal();
                        }
                    });
                });

                // Observe function called when the event button is clicked
                const observeBackdrop = () => {
                    // Get the modal
                    const modalBackdrop = document.querySelector('[data-region="modal-backdrop"]');
                    // Determine if the modalback is there
                    if (modalBackdrop) {
                        // Start observing when the modal has appeared
                        observer.observe(modalBackdrop, {attributes: true, attributeFilter: ['class']});
                    } else {
                        // If the modal was not found then try agian every 100 ms
                        setTimeout(observeBackdrop, 100);
                    }
                };

                // Perform an initial check for the modal
                addContentToModal();

                // Add a eventlisnter to all event buttons
                document.querySelectorAll('[data-region]').forEach(element => {
                    element.addEventListener('click', addEventListenerToButtons);
                });

                /**
                 *
                 * @param event
                 */
                function addEventListenerToButtons(event) {
                    // Determine if the data region is an event button
                    const target = event.target;
                    if (target.classList.contains('eventname') || target.closest('.eventname')) {
                        addContentToModal();
                        observeBackdrop();
                        // Remove the eventlistner after moodle has appeared due to the observer being better
                        removeEventListeners();
                    }
                }

                /**
                 *
                 */
                function removeEventListeners() {
                    document.querySelectorAll('[data-region]').forEach(element => {
                        element.removeEventListener('click', addEventListenerToButtons);
                    });
                }
            }
        }
    };
});
