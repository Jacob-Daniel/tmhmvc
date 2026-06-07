<?php
return [
    'config' => [
        'table'      => 'config',
        'form'       => 'configform',
        'list'       => null,
        'word'       => 'Settings',
        'saveAction' => '/admin/api/saveform',
        'fields' => [
            // Main column
            ['type' => 'text', 'name' => 'comp_name',    'label' => 'Heading'],
            ['type' => 'text', 'name' => 'site_tagline',  'label' => 'Tagline'],
            ['type' => 'text', 'name' => 'domain',        'label' => 'Domain'],
            ['type' => 'section', 'label' => 'Address', 'fields' => [
                ['type' => 'text', 'name' => 'address',   'label' => 'Address'],
                ['type' => 'text', 'name' => 'address_line_2', 'label' => 'Address'],
                ['type' => 'text', 'name' => 'postcode',   'label' => 'Postcode'],
                ['type' => 'text', 'name' => 'town',   'label' => 'Town'],
                ['type' => 'text', 'name' => 'tel',   'label' => 'Tel'],
                ['type' => 'text', 'name' => 'tel2',   'label' => 'Tel'],
                ['type' => 'text', 'name' => 'mobile',   'label' => 'Mobile'],
            ]],            
            ['type' => 'section', 'label' => 'SEO', 'fields' => [
                ['type' => 'seo'],
            ]],
            // Sidebar
            ['type' => 'section', 'sidebar' => true, 'fields' => [
                ['type' => 'image', 'name' => 'imagepath', 'label' => 'Select Main Image','sidebar' => true],
            ]], 
            ['type' => 'section', 'label' => 'Donate', 'sidebar' => true, 'fields' => [
                ['type' => 'text', 'name' => 'donate_title',   'label' => 'Donate Title',  'sidebar' => true],
                ['type' => 'text', 'name' => 'donate_desc', 'label' => 'Donate Description', 'sidebar' => true],
                ['type' => 'text', 'name' => 'donate_amounts',   'label' => 'Donate Amounts',   'sidebar' => true],
            ]],
            ['type' => 'section', 'label' => 'Social', 'sidebar' => true, 'fields' => [
                ['type' => 'text', 'name' => 'fb_url',   'label' => 'Facebook URL',  'sidebar' => true],
                ['type' => 'text', 'name' => 'inst_url', 'label' => 'Instagram URL', 'sidebar' => true],
                ['type' => 'text', 'name' => 'tw_url',   'label' => 'Twitter URL',   'sidebar' => true],
            ]],
        ],
        'actions' => ['save', 'refresh'],
    ],            
    'config_email' => [
        'table'      => 'config_email', 
        'form'       => 'emailconfigform',
        'saveAction' => '/admin/api/saveform',
        'word'       => 'Email Settings',
        'fields' => [
            ['type' => 'text',     'name' => 'site_email',     'label' => 'Email Address'],
            ['type' => 'text',     'name' => 'pp_email',       'label' => 'PayPal Email'],
            ['type' => 'password', 'name' => 'email_password', 'label' => 'Email Password', 'placeholder' => 'Leave blank to keep current'],
            ['type' => 'text',     'name' => 'email_username', 'label' => 'Username'],
            ['type' => 'text',     'name' => 'email_host',     'label' => 'Host'],
            ['type' => 'text',     'name' => 'email_port',     'label' => 'Port'],
            ['type' => 'text',     'name' => 'email_smtp',     'label' => 'SMTP'],
        ],
        'actions' => ['save'],
    ],
    'categories' => [
        'table' => 'categories',
        'form'    => 'catform',
        'list'    => 'catlist',
        'fields'  => ['slug', 'title'],
        'headers' => ['Row', 'Category', 'Sequence', 'Active', 'View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'counter'],
            ['type' => 'editfield', 'field' => 'title', 'mode' => 'pn', 'width' => '200px'],
            ['type' => 'editfield',      'field' => 'sequence'], 
            ['type' => 'flip',      'field' => 'active',  'center' => true],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ],
    'events' => [
        'table' => 'events',
        'form'    => 'eventform',
        'list'    => 'eventlist',
        'fields'  => ['slug', 'title', 'cat_id','is_canonical','active'],
        'headers' => ['Row', 'Title', 'Slug', 'Active', 'View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'counter'],
            ['type' => 'editfield', 'field' => 'title', 'mode' => 'pn', 'width' => '200px'],
            ['type' => 'text',      'field' => 'slug', 'width' => '150px'], 
            ['type' => 'flip',      'field' => 'active',  'center' => true],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ],
    'eventform' => [
        'table'      => 'events',
        'form'       => 'eventform',
        'list'       => 'eventlist',
        'word'       => 'Event Item',
        'saveAction' => '/admin/api/saveeventform',
        'fields' => [
            // Main column
            ['type' => 'text',     'name' => 'title',   'label' => 'Title','required'=>true],
            ['type' => 'text',     'name' => 'slug',    'label' => 'URL Slug'],
            ['type' => 'textarea', 'name' => 'summary', 'label' => 'Summary', 'rows' => 2],
            ['type' => 'richtext', 'name' => 'content', 'label' => 'Content'],
            ['type' => 'section', 'label' => 'SEO', 'fields' => [
                ['type' => 'seo'],
            ]],
            // Sidebar
            ['type' => 'checkboxgroup', 'sidebar' => true, 'fields' => [
                ['name' => 'active',   'label' => 'Active'],
                ['name' => 'featured', 'label' => 'Featured'],
            ]],
            ['type' => 'select',   'name' => 'cat_id',    'label' => 'Primary Category', 'source' => 'categories', 'optionLabel' => 'slug', 'sidebar' => true, 'required'=> true],
            ['type' => 'number',   'name' => 'price',     'label' => 'Price', 'required'=>true,'sidebar' => true],
            ['type' => 'image',    'name' => 'imagepath', 'label' => 'Select Main Image','sidebar' => true],
            ['type' => 'group',    'label' => 'Calendar', 'sidebar' => true, 'fields' => [
                ['type' => 'date', 'name' => 'start_date', 'required'=>true, 'label' => 'Start Date'],
                ['type' => 'date', 'name' => 'end_date', 'required'=>true,   'label' => 'End Date'],
                ['type' => 'time', 'name' => 'start_time', 'required'=>true, 'label' => 'Start Time'],
                ['type' => 'time', 'name' => 'end_time', 'required'=>true,   'label' => 'End Time'],
            ]],
            ['type' => 'recurring', 'sidebar' => true],   // handled as a special block
            ['type' => 'number',   'name' => 'sequence',  'label' => 'Sequence',         'sidebar' => true],
            ['type' => 'text',     'name' => 'iframe',    'label' => 'Iframe Embed URL', 'sidebar' => true],
            ['type' => 'textarea', 'name' => 'metak',     'label' => 'SEO: Meta Keywords',   'sidebar' => true],
            ['type' => 'textarea', 'name' => 'metad',     'label' => 'SEO: Meta Description', 'sidebar' => true],
        ],
        'actions' => ['save', 'back', 'new', 'refresh', 'delete'],
    ],    
    'pages' => [
        'table'   => 'pages',
        'form'    => 'pageform',
        'list'    => 'pagelist',
        'fields'  => ['title', 'slug'],
        'headers' => ['ID', 'Title', 'Slug', 'Active', 'View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'editfield', 'field' => 'title', 'mode' => 'pn', 'width' => '200px'],
            ['type' => 'text',      'field' => 'slug', 'width' => '150px'], 
            ['type' => 'flip',      'field' => 'active',  'center' => true],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ],
    'pageform' => [
        'table'  => 'pages',
        'form'   => 'pageform',
        'list'   => 'pagelist',
        'word'   => 'Page',
        'layout' => 'sidebar', // sidebar | full
        'fields' => [
            // main column
            ['type' => 'text',     'name' => 'title',   'label' => 'Title', 'required'=>true],
            ['type' => 'text',     'name' => 'slug',     'label' => 'Slug', 'hint' => 'SEO-friendly URL (e.g. /about-us)'],
            ['type' => 'richtext', 'name' => 'content',  'label' => 'Page Content'],
            ['type' => 'section', 'label' => 'SEO', 'fields' => [
                ['type' => 'seo'],
            ]],            
            // sidebar
            ['type' => 'checkbox', 'name' => 'active',   'label' => 'Active',           'sidebar' => true],
            ['type' => 'image',    'name' => 'imagepath', 'label' => 'Main Image',       'sidebar' => true],
            ['type' => 'textarea', 'name' => 'metak',    'label' => 'SEO: Meta Keywords','sidebar' => true],
            ['type' => 'textarea', 'name' => 'metad',    'label' => 'SEO: Meta Description', 'sidebar' => true],
        ],
        'actions' => ['save', 'back', 'new', 'refresh', 'delete'],
    ],    
    'navigation' => [
        'table'   => 'navigation',
        'form'    => 'navform',
        'list'    => 'navlist',
        'fields'  => ['label', 'slug'],
        'headers' => ['ID', 'Label', 'Slug', 'Active', 'View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'text', 'field' => 'label', 'width' => '200px'],
            ['type' => 'text',      'field' => 'slug', 'width' => '150px'], 
            ['type' => 'flip',      'field' => 'active',  'center' => true],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ], 
   'emails' => [
        'table'   => 'emails',
        'form'    => 'emailform',
        'list'    => 'emaillist',
        'fields'  => ['em_name', 'em_body'],
        'headers' => ['ID', 'Name','View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'text',      'field' => 'em_name', 'width' => '350px'],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ], 
   'subscribers' => [
        'table'   => 'subscribers',
        'form'    => 'subscriberform',
        'list'    => 'subscriberlist',
        'fields'  => ['email','group_id','unsub'], //allowed filter fields
        'headers' => ['ID', 'Email','Group ID','View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'text',      'field' => 'email', 'width' => '250px'],
            ['type' => 'text',      'field' => 'group_id'],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ],  
    'email_groups' => [
        'table'   => 'email_groups',
        'form'    => 'emailgroupform',
        'list'    => 'emailgrouplist',
        'fields'  => ['email','group_name'],
        'headers' => ['ID', 'Name','View/Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'text',      'field' => 'group_name', 'width' => '350px'],
            ['type' => 'action',    'target' => 'edit',   'center' => true],
            ['type' => 'action',    'target' => 'delete', 'center' => true],
        ],
    ], 
    'banners' => [
        'table'   => 'banners',
        'form'    => 'bannerform',
        'list'    => 'bannerlist',
        'headers' => ['ID', 'Page', 'Title', 'Seq', 'Active', 'Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'text',      'field' => 'page_id'],
            ['type' => 'editfield', 'field' => 'title',    'width' => '200px'],
            ['type' => 'editfield', 'field' => 'sequence'],
            ['type' => 'flip',      'field' => 'active',   'center' => true],
            ['type' => 'action',    'target' => 'edit',    'center' => true],
            ['type' => 'action',    'target' => 'delete',  'center' => true],
        ],
    ],

    'bannerform' => [
        'table'  => 'banners',
        'form'   => 'bannerform',
        'list'   => 'bannerlist',
        'word'   => 'Banner Image',
        'fields' => [
            // Main
            ['type' => 'text',    'name' => 'title', 'label' => 'Title','required'=>true],
            ['type' => 'text',    'name' => 'alt',   'label' => 'Alt Text'],
            ['type' => 'text',    'name' => 'link',  'label' => 'Link (optional)'],
            // Sidebar
            ['type' => 'select',  'name' => 'page_id',  'label' => 'Page',     'source' => 'pages',    'optionLabel' => 'title', 'sidebar' => true],
            ['type' => 'number',  'name' => 'sequence', 'label' => 'Sequence', 'sidebar' => true],
            ['type' => 'checkbox','name' => 'active',   'label' => 'Active',   'sidebar' => true],
            ['type' => 'image',   'name' => 'imagepath','label' => 'Image',    'sidebar' => true],
        ],
        'actions' => ['save', 'back', 'new', 'refresh', 'delete'],
    ],
    'intro_panels' => [
        'table'   => 'intro_panels',
        'form'    => 'intropanelform',
        'list'    => 'intropanellist',
        'headers' => ['ID', 'Title', 'Category', 'Seq', 'Active', 'Edit', 'Delete'],
        'columns' => [
            ['type' => 'text',      'field' => 'id'],
            ['type' => 'editfield', 'field' => 'title',    'width' => '200px'],
            ['type' => 'text',      'field' => 'cat_id'],
            ['type' => 'editfield', 'field' => 'sequence'],
            ['type' => 'flip',      'field' => 'active',   'center' => true],
            ['type' => 'action',    'target' => 'edit',    'center' => true],
            ['type' => 'action',    'target' => 'delete',  'center' => true],
        ],
    ],

    'intropanelform' => [
        'table'  => 'intro_panels',
        'form'   => 'intropanelform',
        'list'   => 'intropanellist',
        'word'   => 'Intro Panel',
        'fields' => [
            // Main
            ['type' => 'text',    'name' => 'title',   'label' => 'Title','required'=>true],
            ['type' => 'textarea','name' => 'content', 'label' => 'Description'],
            ['type' => 'text',    'name' => 'link',    'label' => 'Page Link'],
            // Sidebar
            ['type' => 'select',  'name' => 'cat_id',   'label' => 'Category', 'source' => 'categories', 'optionLabel' => 'title', 'sidebar' => true],
            ['type' => 'text',    'name' => 'bg',       'label' => 'Background Colour (hex)', 'sidebar' => true],
            ['type' => 'number',  'name' => 'sequence', 'label' => 'Sequence',  'sidebar' => true],
            ['type' => 'checkbox','name' => 'active',   'label' => 'Active',    'sidebar' => true],
            ['type' => 'image',   'name' => 'imagepath','label' => 'Image',     'sidebar' => true],
        ],
        'actions' => ['save', 'back', 'new', 'refresh', 'delete'],
    ],    
];