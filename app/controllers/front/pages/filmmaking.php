<?php
require_once __DIR__ . "/../../../views/front/partials/header.php";
$meta = resolveMetaRecord($pageContext);
?>
<main class="flex-1 w-full relative grid grid-cols-12 mb-20">
    <div class="col-span-12 flex flex-col gap-y-10 md:gap-y-16 lg:col-start-2 lg:col-span-10">
<?php
$i = 0;
if (($meta["products"]->num_rows ?? 0) > 0) {
    while ($p = $meta["products"]->fetch_object()) {
?>
        <section 
            id="item_<?php echo $p->id; ?>" 
            class="relative grid grid-cols-12 w-full gap-y-5 md:gap-y-7 md:gap-y-10 opacity-0 translate-y-0 transition-all duration-700 ease-out"
            data-item-section
        >
            <div class="order-2 md:order-0 col-span-12 md:col-span-4 lg:col-span-6 xl:col-span-4 md:bg-white/3 md:text-white sm:p-3 md:p-5 flex flex-col gap-y-4 md:gap-y-3 lg:mx-0 justify-center items-start px-7">
                <h2 class="text-3xl lg:text-4xl xl:text-6xl font-bold"><?= $p->title ?? ''; ?></h2>
                <?= $p->summary ?? ''; ?>
                <button 
                    class="watch bg-cyan-600 py-1 px-3 cursor-pointer hover:opacity-80 transition-opacity" 
                    data-trigger
                    data-itemid="item_<?= $p->id; ?>" 
                    data-catid="<?= $p->id; ?>" 
                    data-prodid="<?= $p->id; ?>"
                >
                    Watch
                </button>
            </div>
            <div 
                class="order-0 md:order-2 col-span-12 md:col-span-8 lg:col-span-6 xl:col-span-8 img-box featured cursor-pointer transition-opacity duration-300" 
                data-trigger
                data-itemid="item_<?= $p->id ?>" 
                data-catid="<?= $p->id ?>" 
                data-prodid="<?= $p->id ?>"
            >
<?php 
            if (!empty($p->imagepath)) {
            $img = img_stem($p->imagepath);
?>
                <img 
                    class="shadow max-h-item aspect-video md:aspect-[10/8] xl:aspect-video 2xl:aspect-auto object-cover object-center lg:object-right lg:max-h-[400]" 
                    src="<?= $img['webp'] ?>.webp" 
                    alt="<?= $p->title; ?>"  
                    loading="lazy"
                    width="1400"
                    height="450"
                    sizes="(max-width: 768px) 80vw, 100vw"

                >
<?php } ?>
            </div>

        </section>

<?php 
        $i++;
    }
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
} else {
    echo '<section class="row"><div class="col-span-12 sm:col-span-8 sm:mx-auto p-2"><h2>Sorry nothing found</h2></div></section>';
}
?>
    </div>
</main>
<?php require_once __DIR__ . "/../../../views/front/partials/footer.php";?>
