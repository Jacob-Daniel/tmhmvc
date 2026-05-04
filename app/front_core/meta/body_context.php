<?php
function getBodyClasses(array $context, array $record): string
{
    $classes = ["page"];

    // Page slug class
    if (!empty($context["page"])) {
        $classes[] = $context["page"];
    }

    // Subcategory class
    if (!empty($context["subcategory"])) {
        $classes[] = "subcategory";
        $classes[] = $context["subcategory"];
    }

    return implode(" ", array_map("sanitize_class", $classes));
}

function sanitize_class(string $class): string
{
    // Replace anything not a-z, 0-9, - with dash
    $class = preg_replace("/[^a-z0-9\-]+/i", "-", $class);
    return strtolower(trim($class, "-"));
}