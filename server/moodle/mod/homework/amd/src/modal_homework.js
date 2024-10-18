import Modal from 'core/modal';

/**
 * homework/amd/src/modal_homework.js
 *
 * @package   mod_homework
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * Customized modal for homework plugin
 */
export default class MyModal extends Modal {
    static TYPE = "mod_homework/modaltemplate";
    static TEMPLATE = "mod_homework/modaltemplate";
}