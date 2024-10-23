<?php
/**
 * Block definition class for the block_homework plugin.
 *
 * @package   block_homework
 * @copyright Year, You Name <your@email.address>
 * @author    group 11
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$capabilities = [
    'block/homework:myaddinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ],
    'block/homework:addinstance' => [
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ],
];