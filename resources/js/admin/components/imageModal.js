// ============================================================
// Image Modal
// ============================================================

let modalContent = null; // 'pageform' etc  — set by the open trigger
let modalType = null; // 'single' | 'multi' | 'thumb'
let modalBoxId = null;
let modalImgId = null;
let modalFieldId = null;

export function initImageModal() {
    const modal = document.getElementById("image-modal");
    const backdrop = document.getElementById("image-modal-backdrop");
    const closeBtn = document.getElementById("image-modal-close");
    const searchEl = document.getElementById("psch");

    if (!modal) return;

    document.getElementById("image-modal")?.addEventListener("click", (e) => {
        const pageBtn = e.target.closest(".pager-btn");
        if (!pageBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const page = pageBtn.dataset.page ?? 1;

        if (typeof window.getImages === "function") {
            window.getImages(page, modalContent, modalType);
        }
    });

    // ---- Close handlers ----------------------------------------
    closeBtn?.addEventListener("click", () => closeModal());
    backdrop?.addEventListener("click", () => closeModal());

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeModal();
    });

    // ---- Search ------------------------------------------------
    searchEl?.addEventListener("keyup", () => {
        if (modalContent && modalType) {
            getImages(1, modalContent, modalType);
        }
    });

    // ---- Add Image/s button ------------------------------------
    document.getElementById("img-gal")?.addEventListener("click", () => {
        if (typeof window.bgt === "function") {
            window.bgt(
                modalContent,
                modalType,
                modalBoxId,
                modalImgId,
                modalFieldId,
            );
            if (modalType !== "multi") closeModal();
        }
    });
}

// ---- Open the modal -------------------------------------------
// Call this from whatever button triggers the image picker
// e.g. openImageModal('pageform', 'single')

export function openImageModal({ content, type, boxId, imgId, fieldId }) {
    const modal = document.getElementById("image-modal");
    const galBtn = document.getElementById("img-gal");
    const restab = document.getElementById("restab");

    if (!modal) return;

    // store for use by search and pager
    modalContent = content;
    modalType = type;
    modalBoxId = boxId;
    modalImgId = imgId;
    modalFieldId = fieldId;

    // update the Add Image/s button with correct values
    if (galBtn) {
        galBtn.dataset.content = content;
        galBtn.dataset.type = type;
        galBtn.dataset.boxId = boxId;
        galBtn.dataset.imgId = imgId;
        galBtn.dataset.fieldId = fieldId;
    }

    if (restab) restab.innerHTML = "";

    modal.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");

    if (typeof window.getImages === "function") {
        window.getImages(1, content, type);
    }
}

// ---- Close the modal ------------------------------------------

export function closeModal() {
    const modal = document.getElementById("image-modal");
    const restab = document.getElementById("restab");

    if (!modal) return;

    modal.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");

    if (restab) restab.innerHTML = "";

    modalContent = null;
    modalType = null;
}

// ---- Hand off to bgt once user clicks Add ---------------------

function bgtFromModal(content, type) {
    if (typeof window.bgt === "function") {
        window.bgt(content, type);
    }
}
