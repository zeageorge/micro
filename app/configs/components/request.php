<?php

// Request manager
return [
    'request' => [
        'class' => '\Micro\web\Request',
        'routes' => [
            '/login' => '/default/login',
            '/logout' => '/default/logout',
            '/login/<num:\d+>/<type:\w+>/<arr:\d{3}>' => '/default/login',
            '/blog/post/index/<page:\d+>' => '/blog/post',
            '/blog/post/<id:\d+>' => '/blog/post/view'
        ]
    ]
];