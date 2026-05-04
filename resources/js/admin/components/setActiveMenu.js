export function setActiveMenu(action) {
    const items = document.querySelectorAll("#leftmenu .nav-item");
    items.forEach((item) => {
        item.classList.remove("font-semibold");

        if (item.dataset.route === action) {
            item.classList.add("font-semibold");
        }
    });
}
