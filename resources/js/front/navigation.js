export function toggleMobileMenu() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const mobileNav = document.getElementById('mobile-nav');
    const overlay = document.getElementById('mobile-menu');
    const menuIcon = document.querySelector('.menu-icon');
    const closeIcon = document.querySelector('.close-icon');

    toggle?.addEventListener('click', () => {
        mobileNav.classList.toggle('-translate-x-full');
        overlay.classList.toggle('opacity-0');
        overlay.classList.toggle('pointer-events-none');
        menuIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });

    overlay?.addEventListener('click', () => toggle.click());
}

export function toggleSubmenus(selector) {
    document.querySelectorAll(selector).forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const submenu = btn.closest('li').querySelector('.journalism-submenu');
            const svg = btn.querySelector('svg');
            
            submenu.style.maxHeight = submenu.style.maxHeight ? null : submenu.scrollHeight + 'px';
            svg.classList.toggle('rotate-180');
        });
    });
}
