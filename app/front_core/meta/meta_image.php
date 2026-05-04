<?php
function resolveMetaImage($imagepath): ?array
{
	global $db;

	if (!$imagepath) {
		return null;
	}

	$filename = str_replace(" ", "_", $imagepath);
	$path = $_SERVER["DOCUMENT_ROOT"] . "/images/600/" . $filename;

	if (!file_exists($path)) {
		return null;
	}

	$size = getimagesize($path);

	return [
		"url" => "/images/600/" . $filename,
		"width" => $size[0],
		"height" => $size[1],
	];
}
