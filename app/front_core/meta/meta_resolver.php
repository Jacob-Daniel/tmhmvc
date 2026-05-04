<?php
require_once __DIR__ . "/../../bootstrap/app.php";
function resolveMetaRecord(array $context): array
{
    $page = getRecord("pages", "slug", $context["page"]);
    if (!$page) {
        return [
            "page" => null,
            "category" => null,
            "subcategory" => null,
            "products" => null,
            "categories" => null,
        ];
    }

    $category = null;
    $subcategory = null;
    $products = null;
    $categories = null;

    // Page → parent category
    if ($page->cat_id) {
        $category = getRecord("categories", "id", $page->cat_id);
    }

    // SUBCATEGORY PAGE (/journalism/tv)
    if ($category && $context["subcategory"]) {
        $subcategory = getRecord("categories", "slug", $context["subcategory"]);

        if ($subcategory) {
            $products = getListWhere(
                "products",
                "WHERE cat_id = ? ORDER BY sequence",
                "i",
                [$subcategory->id]
            );
        }
    }

    // PARENT CATEGORY PAGE (/journalism, /filmmaking)
    if ($category && !$subcategory) {
        $categories = getListWhere(
            "categories",
            "WHERE parent_id = ? ORDER BY sequence",
            "i",
            [$category->id]
        );

        // Special case: filmmaking → films → products
        if ($category->slug === "filmmaking") {
            $filmsCat = getRecord("categories", "parent_id", $category->id);

            if ($filmsCat) {
                $products = getListWhere(
                    "products",
                    "WHERE cat_id = ? ORDER BY sequence",
                    "i",
                    [$filmsCat->id]
                );
            }
        }
    }
        // print_r($products);
    return [
        "page" => $page,
        "category" => $category,
        "subcategory" => $subcategory,
        "products" => $products,
        "categories" => $categories,
    ];
}

