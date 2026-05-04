<?php
declare(strict_types=1);

// Get item ID if editing
$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);

// Pagination (optional, if you have listing page)
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$_SESSION['page'] = $page;

// Fetch all navigation items for dropdowns (e.g., parent)
$navItems = getList('navigation', ' ORDER BY sequence, id');

// Fetch pages for page_id dropdown (assuming a 'pages' table exists)
$pages = getList('pages', ' ORDER BY title');

// Initialize variables for new record
$rec = null;
$id = null;
$label = '';
$slug = '';
$page_id = '';
$parent_id = '';
$menu_group = 'main';
$sequence = 1;
$target = '_self';
$active = 1;

// Load existing record if editing
if ($itemId) {
    $rec = getRecord('navigation', 'id', $itemId);
    if ($rec) {
        $id         = $rec->id;
        $label      = $rec->label ?? '';
        $slug       = $rec->slug ?? '';
        $page_id    = $rec->page_id ?? '';
        $parent_id  = $rec->parent_id ?? '';
        $menu_group = $rec->menu_group ?? 'main';
        $sequence   = $rec->sequence ?? 1;
        $target     = $rec->target ?? '_self';
        $active     = ((int)$rec->active === 1); // boolean for checkbox
    }
}

// Generate preview URL for admin
$previewUrl = $slug ? "/{$slug}" : '';

// Render the form
render('navform', [
    'rec'        => $rec,
    'id'         => $id,
    'label'      => $label,
    'slug'       => $slug,
    'page_id'    => $page_id,
    'parent_id'  => $parent_id,
    'menu_group' => $menu_group,
    'sequence'   => $sequence,
    'target'     => $target,
    'active'     => $active,
    'navItems'   => $navItems,
    'pages'      => $pages,
    'previewUrl' => $previewUrl,
]);