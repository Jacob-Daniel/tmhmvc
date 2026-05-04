<?php
require_once __DIR__ . '/../../../bootstrap/app.php';
require_once __DIR__ . "/header.php";
$meta = resolveMetaRecord($pageContext);

$pages = getList(
    "navigation",
    "WHERE menu_group = 'main' AND active = 1 ORDER BY sequence"
);

$tree = [];
while ($item = $pages->fetch_object()) {
    $tree[$item->parent_id ?? 0][] = $item;
}

$currentPageSlug = $meta['page']->slug ?? null;
$currentSubcategorySlug = $meta['subcategory']->slug ?? null;

function renderMobMenu($parentId, $tree, $level = 0, $parentPath = '') {

    global $currentPageSlug, $currentSubcategorySlug;

    if (!isset($tree[$parentId])) return;

    if ($level === 0) {
        echo '<ul class="fixed md:hidden inset-0 left-0 min-h h-full w-[280px] z-50 flex flex-col gap-y-2 pt-20 px-4 -translate-x-full transition-transform duration-300 bg-black" id="mobile-nav">';
    } else {
        echo '<ul class="relative left-0 mt-4 bg-black py-0 hidden group-hover:flex flex-col items-start w-full">';
    }

    foreach ($tree[$parentId] as $item) {

        $slug = $item->slug;
        $hasChildren = isset($tree[$item->id]);

        $path = $parentPath
            ? $parentPath . '/' . $slug
            : $slug;

        $isActive =
            ($level === 0 && $currentPageSlug === $slug) ||
            ($level === 1 && $currentSubcategorySlug === $slug);

        $activeClass = $isActive
            ? "active text-hover"
            : "text-white hover:text-hover";

        $textSize = ($level === 0)
            ? (in_array($slug, ['about','contact'])
                ? 'text-md lg:text-lg'
                : 'text-lg lg:text-xl')
            : '';

        $liPadding = ($level === 1) ? 'ps-4' : '';

        echo '<li class="relative nav-item border-t border-white/20 py-3 ' . $slug . ' ' .
            ($hasChildren ? 'group dropdown' : '') . ' ' . $textSize . ' ' . $liPadding . '">';

        echo '<a class="gradient-link journalism-links nav-link uppercase tracking-[0.1rem] ' .
            $activeClass . '" href="' . BASE_URL . '/' . $path . '">';
        echo ucfirst($slug);
        echo '</a>';

        if ($hasChildren) {
            renderMobMenu($item->id, $tree, $level + 1, $path);
        }

        echo '</li>';
    }

    echo '</ul>';
}
?>

<?php renderMobMenu(0, $tree); ?>