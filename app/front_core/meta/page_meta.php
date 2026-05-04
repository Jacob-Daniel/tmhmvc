<?php
require_once __DIR__ . "/meta_context.php";
require_once __DIR__ . "/meta_resolver.php";
require_once __DIR__ . "/meta_image.php";
require_once __DIR__ . "/body_context.php";

$meta = [
	"title" => "",
	"description" => "",
	"keywords" => "",
	"image" => null,
	"width" => null,
	"height" => null,
];

$pageContext = getPageContext();
$record = resolveMetaRecord($pageContext);

if ($record) {
	$meta["title"] = $record["page"]->slug ?? "";
	$meta["description"] = $record["page"]->metad ?? "";
	$meta["keywords"] = $record["page"]->metak ?? "";
	$meta["image"] = resolveMetaImage($record["page"]->imagepath ?? null);
	$prods = $record["products"] ?? null;
	$meta["body_classes"] = getBodyClasses($pageContext, $record) ?? "";
}
