<?php

// File: mod/homework/db/services.php

$functions = [
    'mod_homework_save_homework_chooser' => [
        'classname'   => 'mod_homework\external\save_homework_chooser',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/save_homework_chooser.php',
        'description' => 'Saves the homework chooser input field value',
        'type'        => 'write',
        'ajax'        => true
    ],
    'mod_homework_get_homework_chooser' => [
        'classname'   => 'mod_homework\external\get_homework_chooser',
        'methodname'  => 'execute',
        'classpath'   => 'mod/homework/classes/external/get_homework_chooser.php',
        'description' => 'Get the homework chooser content',
        'type'        => 'read',
        'ajax'        => true
    ]
];

$services = [
    'mod_homework_services' => [
        'functions' => [
            'mod_homework_save_homework_chooser',
            'mod_homework_get_homework_chooser',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ]
];