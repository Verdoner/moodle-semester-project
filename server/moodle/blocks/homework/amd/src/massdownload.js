import Ajax from 'core/ajax';

export const init = async() => {
    if (document.querySelector(".course-section-header")) {
        console.log("Script loaded: monitoring for course-section-header elements");

        // Function to inject the button into each course-section-header
        function injectDownloadButtons() {
            document.querySelectorAll(".course-section-header").forEach(function (header) {
                // Avoid adding the button multiple times
                if (header.querySelector(".massdownload-btn")) return;

                // Create the "Download all files" button
                const button = document.createElement("button");
                button.textContent = "Download all files";
                button.classList.add("massdownload-btn", "btn", "btn-primary");
                button.style.marginRight = "10px";
                button.style.padding = "5px 10px";
                button.style.cursor = "pointer";


                // Add click event to the button
                button.addEventListener("click", function () {
                    console.log("hello");
                    Ajax.call([{
                        methodname: 'block_homework_get_files',
                        args: {
                            files: "hello",
                        },
                        done: function (response) {
                            let homework = JSON.parse(response.homework);
                            console.log(homework);
                        },
                        fail: function (error) {
                            console.log(error);
                            throw new Error(`ERROR: ${error}`);
                        }
                    }]);



                /*
                *  const section = header.closest(".section");
                 const sectionId = section ? section.getAttribute("data-id") : null;
                 const courseId = M.cfg.courseid; // Assuming course ID is available globally

                 console.log("Download button clicked!");
                 console.log("Section ID:", sectionId);
                 console.log("Course ID:", courseId);

                 if (sectionId && courseId) {
                     // Directly trigger download by navigating to download.php
                     window.location.href = `${M.cfg.wwwroot}/local/massdownload/download.php?sectionid=${sectionId}&courseid=${courseId}`;

                 } else {
                     console.error("Missing sectionId or courseId");
                 }*/
                });

                // Inject the button into the header
                header.prepend(button);
            });
        }

        // Use MutationObserver
        const observer = new MutationObserver(injectDownloadButtons);
        observer.observe(document.body, {childList: true, subtree: true});

        // Initial injection attempt in case elements are already loaded
        injectDownloadButtons();
    }
};
