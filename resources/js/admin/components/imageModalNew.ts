// ============================================================
// imageModalNew.ts
// ============================================================

let currentPage = 1;
let currentSearch = "";

let currentFieldId: string | null = null;
let currentBoxId: string | null = null;
let currentImgId: string | null = null;
let currentType: string = "single";

let imageModalInitialised = false;
/* --------------------------------------------------
   INITIALISE MODAL
-------------------------------------------------- */
export function initImageModal() {
    if (imageModalInitialised) return; // prevent double binding
    imageModalInitialised = true;

    const modal = document.getElementById("image-modal");
    const header = document.getElementById("header");
    if (!modal) return;

    const backdrop = document.getElementById("image-modal-backdrop");
    const closeBtn = document.getElementById("image-modal-close");
    const searchInput = document.getElementById(
        "modal-search",
    ) as HTMLInputElement;

    // -------------------------
    // Open modal via buttons
    // -------------------------
    document.addEventListener("click", (e) => {
        const btn = (e.target as HTMLElement).closest("[data-open-images]");
        if (!btn) return;

        currentFieldId = btn.getAttribute("data-field-id");
        currentBoxId = btn.getAttribute("data-box-id");
        currentImgId = btn.getAttribute("data-img-id");
        currentType = btn.getAttribute("data-type") ?? "single";

        openImageModal();
    });

    // -------------------------
    // Close modal
    // -------------------------
    closeBtn?.addEventListener("click", closeImageModal);
    backdrop?.addEventListener("click", closeImageModal);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeImageModal();
    });

    // -------------------------
    // Image selection inside modal
    // -------------------------
    modal.addEventListener("click", (e) => {
        const card = (e.target as HTMLElement).closest("[data-path]");
        if (!card) return;

        const filePath = card.getAttribute("data-path");
        if (!filePath) return;

        handleImageSelect(filePath);
    });

    if (searchInput) {
        searchInput.setSelectionRange(
            searchInput.value.length,
            searchInput.value.length,
        );
        searchInput.focus();
        searchInput?.addEventListener("input", () => {
            const val = searchInput.value.trim();
            if (val === "") {
                currentSearch = "";
                currentPage = 1;
                fetchModalImages();
            } else if (val.length >= 3) {
                currentSearch = val;
                currentPage = 1;
                fetchModalImages();
            }
        });
    }
    // -------------------------
    header?.addEventListener("click", (e) => {
        console.log("click");
        const target = e.target as HTMLElement;

        // check page buttons
        const pageBtn = target.closest<HTMLElement>(".pager-btn");
        if (pageBtn && pageBtn.dataset.page) {
            e.preventDefault();
            goToPage(Number(pageBtn.dataset.page));
            return;
        }

        // check image cards
        const card = target.closest<HTMLElement>("[data-path]");
        if (card) {
            const filePath = card.dataset.path;
            if (filePath) handleImageSelect(filePath);
        }
    });
}

/* --------------------------------------------------
   OPEN / CLOSE MODAL
-------------------------------------------------- */
export function openImageModal() {
    const modal = document.getElementById("image-modal");
    if (!modal) return;

    currentPage = 1;
    currentSearch = "";

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("overflow-hidden");

    fetchModalImages();
}

export function closeImageModal() {
    const modal = document.getElementById("image-modal");
    if (!modal) return;

    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.classList.remove("overflow-hidden");

    currentFieldId = null;
    currentBoxId = null;
    currentImgId = null;
    currentType = "single";

    const restab = document.getElementById("restab");
    if (restab) restab.innerHTML = "";
}

/* --------------------------------------------------
   HANDLE IMAGE SELECTION
-------------------------------------------------- */
export function handleImageSelect(filePath: string) {
    console.log(filePath, "dasdfsdf");

    if (!currentFieldId) return;

    const input = document.getElementById(
        currentFieldId,
    ) as HTMLInputElement | null;
    if (!input) return;

    // Set value
    input.value = filePath;

    // Update preview if defined
    if (currentBoxId && currentImgId) {
        const box = document.getElementById(currentBoxId);
        let img = document.getElementById(
            currentImgId,
        ) as HTMLImageElement | null;

        const src = `/images/thumbs/200/${filePath}`;

        if (!img && box) {
            img = document.createElement("img");
            img.id = currentImgId;
            img.className = "rounded border max-w-[200px]";
            box.appendChild(img);
        }

        if (img) img.src = src;
    }

    closeImageModal();
}

/* --------------------------------------------------
   FETCH IMAGES (AJAX)
-------------------------------------------------- */
export function fetchModalImages() {
    const restab = document.getElementById("restab");
    if (!restab) return;

    const params = new URLSearchParams({
        page: String(currentPage),
        search: currentSearch,
    });

    fetch(`/admin/api/imageModal?${params}`)
        .then((r) => {
            if (!r.ok) throw new Error("Failed to fetch images");
            return r.text();
        })
        .then((html) => {
            restab.innerHTML = html;
            console.log("fetch okay");
        })
        .catch((err) => {
            restab.innerHTML =
                '<p class="text-red-500 col-span-full">Failed to load images</p>';
            console.error(err);
        });
}

/* --------------------------------------------------
   PAGINATION HANDLER
-------------------------------------------------- */
export function goToPage(page: number) {
    currentPage = page;
    fetchModalImages();
}
