<?php
require_once __DIR__ . "/../../../views/front/partials/header.php";
$meta = resolveMetaRecord($pageContext);
			$image = $meta['page']->imagepath ?? "";
			$title = $meta['page']->title ?? "";
			$content = $meta['page']->content ?? "";
			$img = img_stem($image);

?>
<main class="relative w-full grid grid-cols-12 pt-00 opacity-0 translate-y-4 transition-all duration-700 ease-out" data-page-main>
	<div class="grid grid-cols-12 col-span-12 px-5 lg:px-0 lg:col-start-2 lg:col-span-10 md:gap-x-7 gap-y-5">
<?php
	if ($meta) {
?>
			<div class="col-span-12 md:col-span-4 2xl:col-span-2 opacity-0 -translate-x-4" data-fade-slide="left">
				<img class="max-w-full min-w-full" width="240" height="320" src="<?= $img['webp'] ?>.webp" alt="<?= $title ?? "theopi skarlatos"; ?>">
			</div>
<?php
}
		if ($content) { ?>			
				<div class="col-span-12 md:col-span-8 2xl:col-span-6 opacity-0 translate-x-4" data-fade-slide="right">
				<h2 class="text-white w-full font-bold text-lg md:text-xl xl:text-2xl mb-3"><?= $title ?></h2>
					<?= $content; ?>
				</div>
<?php 
}
?>
	</div>
</main>
<?php require_once __DIR__ . "/../../../views/front/partials/footer.php";?>
