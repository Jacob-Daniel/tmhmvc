<?php
function getPageContext(): array
{
    return [
        "page" => $_GET["slug"] ?? "home",
        "subcategory" => $_GET["subcategory"] ?? null,
    ];
}
$pageContext = getPageContext();

