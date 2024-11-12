define(function() {
    return {
        init: function() {
            if (!document.querySelector('[data-region="calendar"]')) {
                return;
            }

            function DisplayMapLink() {
                // Run a timeout of 500ms to make sure the modal and content are there.
                setTimeout(() => {
                    // Select the element containing location details.
                    const locationContent = document.querySelector('.location-content');

                    if (!locationContent) {
                        return;
                    }

                    /*
                    Define a regular expression to match and extract specific parts of the location text.
                    The regex is designed to capture:
                    1. The street address (e.g., "Alfred Nobels Vej 27").
                    2. The city name (e.g., "Aalborg").

                    Example of input:
                    S.B2.02 (Auditorium), Alfred Nobels Vej 27 - Novi 8 (Anv27), Aalborg
                    */
                    const regex = /,\s*(([a-zA-Zæøå 0-9]|-[^\s])+)\s*[^)]*\),\s*(.*)/;

                    const realLocation = locationContent.textContent
                        .split("\n") // Split the text into an array of lines
                        .map(str => {
                            const match = str.match(regex);
                            if (match) {
                                const location = match[1];
                                const city = match[3];
                                return `${location}, ${city}`;
                            }
                            return str;
                        }).join("\n");

                    const link = document.createElement('a');

                    // Set the Google Maps search URL as the hyperlink, using the formatted location details.
                    link.href = `https://www.google.com/maps/search/?api=1&query=${realLocation}`;

                    link.textContent = locationContent.textContent;

                    // Open the link in a new browser tab or window.
                    link.target = '_blank';

                    locationContent.innerHTML = '';
                    locationContent.appendChild(link);

                }, 500);
            }

            // Observer setup
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    // Determine if the modal is shown based on the "show" class

                    if (mutation.target.classList.contains('show')) {
                        DisplayMapLink();
                    }
                });
            });

            // Observe function called when the event button is clicked
            const observeBackdrop = () => {
                // Get the modal
                const modalBackdrop = document.querySelector('[data-region="modal-backdrop"]');

                // Determine if the modal backdrop is there
                if (modalBackdrop) {
                    // Start observing when the modal has appeared
                    observer.observe(modalBackdrop, {attributes: true, attributeFilter: ['class']});
                } else {
                    // If the modal was not found, try again every 100 ms
                    setTimeout(observeBackdrop, 100);
                }
            };

            // Perform an initial check for the modal
            DisplayMapLink();

            // Add an event listener to all event buttons
            document.querySelectorAll('[data-region]').forEach((element) => {
                element.addEventListener('click', addEventListenerToButtons);
            });

            function addEventListenerToButtons(event) {
                // Determine if the data region is an event button
                const target = event.target;
                if (target.classList.contains('eventname') || target.closest('.eventname')) {
                    DisplayMapLink();
                    observeBackdrop();
                    // Remove the event listener after modal has appeared due to the observer being better
                    removeEventListeners();
                }
            }

            function removeEventListeners() {
                document.querySelectorAll('[data-region]').forEach((element) => {
                    element.removeEventListener('click', addEventListenerToButtons);
                });
            }
        }
    };
});
