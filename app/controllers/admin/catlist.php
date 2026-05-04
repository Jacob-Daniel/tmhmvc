<?php

// --------------------
// Fetch ALL categories
// --------------------
$result = getList('categories', 'ORDER BY parent_id, sequence');

$all = [];
while ($row = $result->fetch_object()) {
    $row->children = [];
    $all[$row->id] = $row;
}

// --------------------
// Build tree
// --------------------
$tree = [];

foreach ($all as $id => $cat) {

    if ((int)$cat->parent_id === 0) {
        $tree[$id] = $cat;
    } else {
        if (isset($all[$cat->parent_id])) {
            $all[$cat->parent_id]->children[] = $cat;
        }
    }
}

// --------------------
// Render
// --------------------
render('catlist', [
    'categories'    => $tree,
    'deleteMessage' => $deleteMessage ?? null
]);