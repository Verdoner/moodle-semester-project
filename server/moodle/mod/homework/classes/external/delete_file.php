<?php
namespace mod_homework\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use core\exception\moodle_exception;
use external_api;
use external_function_parameters;
use external_value;
use context_system;

/**
 * Class to handle file deletion for mod_homework.
 */
class delete_file extends external_api {

    /**
     * Define the parameters for delete_file.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'The ID of the homework to update'),
                'file_id' => new external_value(PARAM_INT, 'The ID of the file to delete'),
            )
        );
    }

    /**
     * Deletes the specified file.
     *
     * @param int $id The ID of the homework to update.
     * @param int $file_id The ID of the file to delete.
     * @return bool True if the file was successfully deleted, false otherwise.
     */
    public static function execute($id, $file_id) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), array(
            'id' => $id,
            'file_id' => $file_id,
        ));

        // Ensure the user is logged in and validate context.
        require_login();
        $context = context_system::instance();
        self::validate_context($context);

        // Include the library function to delete the file.
        require_once(__DIR__ . '/../../lib.php');
        $success = mod_homework_delete_file(
            $params['id'],
            $params['file_id']
        );

        if (!$success) {
            throw new moodle_exception('deleteerror', 'mod_homework', '', null, 'File deletion failed.');
        }

        return true;
    }

    /**
     * Define the return type for delete_file.
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_value(PARAM_BOOL, 'True if file was deleted successfully');
    }
}
