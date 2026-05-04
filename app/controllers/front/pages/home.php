<?php
$imgUrlMobile = BASE_URL . '/uploads/webp/theopi_film_journalism_mob.webp';
$imgUrlDesktop = BASE_URL . '/uploads/webp/theopi_film_journalism.webp';
?>
<main class="relative w-full h-screen overflow-hidden">
    <picture class="absolute inset-0 w-full h-full">
        <source media="(min-width: 768px)" srcset="<?= $imgUrlDesktop ?>">
        <img 
            fetchpriority="high"     
            decoding="async"               
            src="<?= $imgUrlMobile ?>" 
            alt="Film journalism background" 
            class="w-full h-full object-cover object-left-top md:object-right-top bg-granulate-in"
        >
    </picture>
    <div class="relative flex w-full h-full justify-center items-center gap-y-5 md:gap-y-5 xl:gap-y-7 z-10">
        <h2 class="font-titles flex flex-col uppercase text-12xl tracking-tight sm:text-12xl md:text-13xl lg:text-banner-sm" id="main-links">
            <a class="gradient-link text-white" href="<?= BASE_URL ?>/filmmaking">Filmmaking</a>
            <a class="gradient-link text-white" href="<?= BASE_URL ?>/journalism">Journalism</a>
        </h2>
    </div>
</main>
<?php require_once __DIR__ . "/../../../views/front/partials/footer.php";?>
