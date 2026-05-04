<?php
declare(strict_types=1);

// --------------------
// Fetch ALL navigation items
// --------------------
$result = getList('navigation', 'ORDER BY menu_group, sequence, id');

$all = [];
while ($row = $result->fetch_object()) {
    $row->children = [];
    $all[$row->id] = $row;
}

// --------------------
// Build tree
// --------------------
$tree = [];

foreach ($all as $id => $nav) {
    if (empty($nav->parent_id) || (int)$nav->parent_id === 0) {
        $tree[$id] = $nav;
    } else {
        if (isset($all[$nav->parent_id])) {
            $all[$nav->parent_id]->children[] = $nav;
        }
    }
}

// --------------------
// Render
// --------------------
render('navlist', [
    'navigation'    => $tree,
    'deleteMessage' => $deleteMessage ?? null
]);