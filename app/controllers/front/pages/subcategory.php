<?php
require_once __DIR__ . "/../../../views/front/partials/header.php";
$meta = resolveMetaRecord($pageContext);
$subCat = $meta['subcategory'];
$products = $meta['products'];
$hasMedia = $subCat->id===6 ? false : true;
if (isset($meta["products"])) { ?>
<main class="w-full grid grid-cols-12 px-5 gap-y-4 md:gap-x-7 lg:px-0 mb-16">
    <div class="col-span-12 lg:col-span-10 lg:col-start-2 grid grid-cols-1 sm:grid-cols-2 gap-y-10">
<?php
    $i = 0;
    if (($products->num_rows ?? 0) > 0) {
        while ($p = $products->fetch_object()) {
            $img = img_stem($p->imagepath);
            $padding = $i % 2 ? "sm:pl-3" : "sm:pr-3";
                if ($hasMedia) { ?>	
                <section id="item_<?= $p->id ?>" class="related-items flex flex-col gap-y-4 <?= $padding ?> opacity-0 transition-[opacity,transform] duration-700 ease-out" data-item-section> 
                <?php } else { ?>              
                <section class="related-items flex flex-col gap-y-4 <?= $padding ?> opacity-0 transition-[opacity,transform] duration-700 ease-out" data-item-section>
                <?php } ?>
                    <div class="img-box md:aspect-[4/3] md:max-h-[200px] <?= $hasMedia ? 'cursor-pointer': '';?>" 
                     data-trigger 
                     data-itemid="item_<?= $p->id ?>" 
                     data-catid="<?= $p->id ?>" 
                     data-prodid="<?= $p->id ?>">

                    <?php if ($img ?? '') { ?>                  
                        <img 
                            class="w-full h-full object-cover shadow"
                            src="<?= $img['webp'] ?>.webp"
                            alt="<?= $p->title ?>"
                            data-title="<?= $p->title ?>"
                            data-cycle-desc="<?= $p->slug ?>">
                    <?php } ?> 
                    </div>
                        <div class="box item meta flex flex-col items-start gap-y-4 lg:gap-y-3">
                            <h2 class="text-4xl md:text-2xl xl:text-6xl fon-bold"><?= $p->title ?? '' ?></h2>
                            <?= $p->summary ?? '' ?>
                            <?= ($hasMedia) ? '<button class="watch bg-cyan-600 py-1 px-3 cursor-pointer hover:opacity-80 transition-opacity" data-trigger data-itemid="item_<?= $p->id; ?>" data-catid="'.$p->id.'" data-prodid="'.$p->id.'">More</button>'
                            :'';?> 
                        </div>
                    </section>
            <?php 
            $i++;
        }
        if($hasMedia) {
?>
        <div
          id="film-viewer"
          class="fixed inset-0 z-[2000] bg-black/90 hidden opacity-0 transition-opacity duration-300 grid grid-cols-12 items-center justify-center"
          data-viewer
        >
        <div data-viewer-container class="col-span-12 lg:col-span-10 bg-black lg:col-start-2 p-10 relative max-w-6xl mx-auto px-6 scale-x-50 scale-y-100 transition-transform duration-200 ease-out origin-center">

            <div class="flex flex-col gap-y-6">
              <div data-h2-container class="text-white"></div>
              <div data-video-container></div>
              <div data-desc-container class="text-white max-w-3xl gap-y-2"></div>
              <div class="w-full"><button class="cursor-pointer px-4 py-2 bg-gray-800 text-white hover:bg-gray-700 transition-colors" data-close><i class="fas fa-times mr-2"></i>Close</button></div>
            </div>
          </div>
        </div>

<?php  
        }
    } else {
        echo '<section class="grid"><div class="col-span-1 sm:col-span-2 sm:col-start-2 p-2"><h2>Sorry nothing found</h2></div></section>';
    }
}
?> 
    </div>
</main>
<?php require_once __DIR__ . "/../../../views/front/partials/footer.php";?>
