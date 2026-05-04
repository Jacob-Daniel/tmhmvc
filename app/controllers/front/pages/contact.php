<?php 
$meta = resolveMetaRecord($pageContext);
			$title = $meta['page']->title ?? "";
			$content = $meta['page']->content ?? "";

?>
<main class="relative w-full h-full lg:h-auto grid lg:grid-cols-12 flex items-center justify-center opacity-0 translate-y-4 transition-all duration-700 ease-out" data-page-main>
	<div class="px-5 lg:px-0 lg:col-start-2 md:col-span-6 lg:col-span-4 flex flex-col gap-y-2">
		<h2 class="text-white font-bold text-lg md:text-xl leading-none xl:text-2xl opacity-0 -translate-y-2" data-contact-heading><?= $title; ?></h2>
<?php
			if (($meta ?? '') && $meta['page'] ) {
?>
				<div class="pb-1 opacity-0 translate-y-2" data-contact-intro>
					<?= $content ?>
				</div>
<?php
			}
			require __DIR__ . '/../../../views/front/components/form.php';
?>
		</div>
</main>

<?php require_once __DIR__ . "/../../../views/front/partials/footer.php";?>
