import Modal from 'core/modal';

/**
 * Homework/amd/src/modal_homework.js
 *
 * @package
 * @copyright 2024, cs-24-sw-5-01 <cs-24-sw-5-01@student.aau.dk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * Customized modal for homework plugin
 */
export default class MyModal extends Modal {
    static TYPE = "block_homework/modaltemplate";
    static TEMPLATE = "block_homework/modaltemplate";
}