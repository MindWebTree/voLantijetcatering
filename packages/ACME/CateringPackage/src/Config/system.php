<?php

return [
    [
        'key' => 'helloworld',  
        'name' => 'Home menu',
        'sort' => 1,
    ], [
        'key' => 'helloworld.settings',
        'name' => 'Custom Settings',
        'sort' => 1,
    ], [
        'key' => 'helloworld.settings.settings',
        'name' => 'Custom Groupings',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'Add Link',
                'title' => 'Add Link',
                'type' => 'text',
                'channel_based' => true,
              
            ]
        ]
    ]
];

