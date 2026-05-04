<?php
require_once __DIR__ . "/meta/page_meta.php";

$title = $meta['title'] ? ' : '. ucfirst($meta['title']) : '';

?>
<!DOCTYPE html>
<html lang="en" class="<?= $pageContext['page']!=='home' ? 'overflow-y-scroll':'';?>">
<head>
<?php require_once __DIR__ . "/meta_tags.php"; ?>
<title><?= COMPANY_NAME ?? "" ?><?= $title ?></title>
<?php require_once __DIR__ . "/scripts.php"; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:wght@400;700;900&display=swap">
</head>
<body class="h-full font-sans <?= $pageContext['page']==='home' ? 'flex flex-col items-start':'';?> <?= $meta["body_classes"] ?? "";?>">
  <div class="w-full h-full flex flex-col max-w-[1680px] mx-auto">
