<?php
$meta = resolveMetaRecord($pageContext);
if ($meta['categories']) { ?>
<main class="flex-1 opacity-0 transition-opacity duration-700 ease-out" data-journalism-main>
  <div class="flex items-center w-full h-full">
		<div id="switch" class="relative aspect-video w-full h-full self-center text-center m-0 p-0">
<?php
			$titles = [];
			$i = 0;
			foreach($meta['categories'] as $cat) {
			$title = $cat['title'];	
			$slug = $cat['slug'];
			$titles[$slug . "_" . $cat['id']] = $title;
			$image = $cat['imagepath']?? "";
			$img = img_stem($image);
			$active = $i == 0 ? "active" : "hidden";
?>
		<div id="<?= $slug; ?>" class="absolute inset-0 p-0 h-full transition-opacity duration-500 ease-in-out <?= $active ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">        
				<picture>
				    <source srcset="<?= $img['webp'] ?>.webp" type="image/webp">
						<img class="w-full h-full object-cover" src="<?= $img['src'] ?>.jpg" data-src="<?= $img['src'] ?>.jpg" alt="<?= $title ?>" loading="eager">'; 
				</picture>
      </div>       
<?php 
			$i++;
}
?>
		</div>
	</div>
	<div id="journalism-titles" class="flex items-center w-full absolute inset-0 z-20 opacity-0 translate-y-6 transition-all duration-700 ease-out" data-journalism-titles>
	    <div class="w-full self-center text-center flex flex-col gap-y-1 md:gap-y-0">
<?php
			$i = 0;
			foreach ($titles as $k => $v) {
				$parts = explode("_", $k);
?>
					<h2 class="font-titles text-12xl tracking-tight sm:text-12xl md:text-13xl lg:text-banner-sm opacity-0 translate-y-4 transition-all duration-500 ease-out" data-journalism-title data-delay="<?php echo $i; ?>">
						<a data-id="<?php echo $parts[0]; ?>" class="gradient-link journalism-links" href="<?php echo BASE_URL."/journalism/".$parts[0]; ?>">
<?php 
								echo $v;
?>							
						</a>
					</h2>
<?php $i++;
}
?>
		</div>
	</div>
</main>
<?php 
}
require_once __DIR__ . "/../../../views/front/partials/footer.php";?>
