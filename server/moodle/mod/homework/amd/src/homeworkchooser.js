define("mod_homework/homeworkchooser", ['jquery', 'core/ajax', 'core/modal_factory', 'core/modal_events'], function($, Ajax, ModalFactory, ModalEvents) {
    return {
        init: function(cmid) {
            $('#open-homework-chooser').on('click', function() {
                Ajax.call([{
                    methodname: 'mod_homework_get_homework_chooser',
                    args: { cmid: cmid },
                    done: function(response) {
                        ModalFactory.create({
                            title: M.util.get_string('homeworkchooser', 'mod_homework'),
                            body: response.html,
                        }).then(function(modal) {
                            modal.show();
                            modal.getRoot().on(ModalEvents.hidden, function() {
                                console.log('Modal closed!');
                            });
                        });
                    },
                    fail: function(error) {
                        console.error("Failed to load homework chooser content:", error);
                    }
                }]);
            });
        }
    };
});