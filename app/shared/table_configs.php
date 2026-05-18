<?php
return [
    'products' => [
        'table' => 'products',
        'form'    => 'prodform',
        'list'    => 'prodlist',
        'fields'  => ['slug', 'title', 'cat_id'],
        'headers' => ['Row', 'Title', 'Slug', 'Active', 'View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'counter'],
            ['type' => 'editfield', 'field' => 'title', 'mode' => 'pn', 'width' => '200px'],
            ['type' => 'text',      'field' => 'slug'],
            ['type' => 'flip',      'field' => 'active',  'center' => true],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ],
    'events' => [
        'table' => 'events',
        'form'    => 'eventform',
        'list'    => 'eventlist',
        'fields'  => ['slug', 'title', 'cat_id'],
        'headers' => ['Row', 'Title', 'Slug', 'Active', 'View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'counter'],
            ['type' => 'editfield', 'field' => 'title', 'mode' => 'pn', 'width' => '200px'],
            ['type' => 'text',      'field' => 'slug'],
            ['type' => 'flip',      'field' => 'active',  'center' => true],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ],
];