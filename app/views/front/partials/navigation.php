<div 
    id="mobile-menu" 
    class="absolute inset-0 bg-black/50 z-40 md:hidden opacity-0 pointer-events-none transition-opacity duration-300">
</div>

<nav class="relative col-span-12 lg:col-span-10 lg:col-start-2 grid grid-cols-12 h-full items-center justify-between">
    <h1 class="font-serif col-span-11 md:col-span-4 uppercase tracking-[0.1rem] md:text-md text-lg items-start flex">
        <a class="flex gap-x-1 flex-none" href="<?= BASE_URL ?>">      
            <span>Theopi</span><span>Skarlatos</span>
        </a>
    </h1>    
    <button 
        id="mobile-menu-toggle" 
        class="md:hidden z-50 p-2 text-white"
        aria-label="Toggle menu">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path class="menu-icon" d="M6 12c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm6 0c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm6 0c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z"/>
            <path class="close-icon hidden" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <?php require_once __DIR__ .'/ul.php'; ?>   
    <?php require_once __DIR__ .'/ulMobile.php'; ?>   
</nav>