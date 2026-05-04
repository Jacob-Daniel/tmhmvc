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

function renderMenu($parentId, $tree, $level = 0, $parentPath = '') {

    global $currentPageSlug, $currentSubcategorySlug;

    if (!isset($tree[$parentId])) return;

    if ($level === 0) {
        echo '<ul class="font-sans font-light hidden md:flex col-span-8 md:flex-row justify-end md:text-right bg-black lg:h-[18px] xl:h-[17px]">';
    } else {
        echo '<ul class="absolute left-0 md:left-2 md:p-3 mt-0 top-[18px] hidden w-max md:w-full bg-black py-0 md:pb-2 group-hover:flex flex-col items-center gap-y-2">';
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
                ? 'text-xs lg:text-sm pt-1'
                : 'text-sm lg:text-md')
            : '';

        $liPadding = ($level === 0) ? 'ps-4' : '';

        echo '<li class="relative nav-item ' . $slug . ' ' .
            ($hasChildren ? 'group dropdown' : '') . ' ' .
            $liPadding . ' ' . $textSize . '">';

        echo '<a class="gradient-link journalism-links nav-link uppercase tracking-[0.1rem] ' .
            $activeClass . '" href="' . BASE_URL . '/' . $path . '">';
        echo ucfirst($slug);
        echo '</a>';

        if ($hasChildren) {
            renderMenu($item->id, $tree, $level + 1, $path);
        }

        echo '</li>';
    }

    echo '</ul>';
}
?>

<?php renderMenu(0, $tree); ?>