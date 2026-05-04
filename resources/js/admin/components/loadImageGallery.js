// ============================================================
// Image Gallery Handler
// ============================================================

const imgBaseUrl = window.CONFIG?.imgBaseUrl ?? "";
const thumbspath = window.thumbspath ?? "../../images/";

if (!imgBaseUrl) {
    console.error(
        "CONFIG.imgBaseUrl is not defined — check config_js.php is loaded before your JS bundle",
    );
}

function thumbSrc(path, size = 200) {
    return `${imgBaseUrl}/thumbs/${size}/${path}`;
}

function fullSrc(path) {
    return `${imgBaseUrl}/${path}`;
}

// ============================================================
// Drag & Drop ordering
// ============================================================

let dragSrcEl = null;

function dragstart(e) {
    dragSrcEl = e.currentTarget;
    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.setData("text/html", dragSrcEl.outerHTML);
    dragSrcEl.classList.add("opacity-50");
}

function dragover(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = "move";
    return false;
}

function dragend(e) {
    e.currentTarget.classList.remove("opacity-50");
    updateImageOrder();
}

function drop(e) {
    e.stopPropagation();
    if (dragSrcEl !== e.currentTarget) {
        dragSrcEl.outerHTML = e.currentTarget.outerHTML;
        e.currentTarget.outerHTML = e.dataTransfer.getData("text/html");
        updateImageOrder();
    }
    return false;
}

// ============================================================
// Update the hidden imageorder input after any reorder/delete
// ============================================================

function updateImageOrder() {
    const rows = document.querySelectorAll(
        "table.extra-images-order tr[data-img-id]",
    );
    const order = [...rows].map((r) => r.dataset.imgId).join(",");
    const input = document.getElementById("imageorder");
    if (input) input.value = order;

    const msg = document.getElementById("save-ord-msg");
    if (msg) {
        msg.textContent = "Click 'Save' button to update Item";
        msg.style.display = "block";
    }
}

// ============================================================
// Delete a row from the extra images table
// ============================================================

function handleImageDelete(imgId) {
    const row = document.querySelector(`tr[data-img-id="${imgId}"]`);
    if (row) row.remove();
    updateImageOrder();
}

// ============================================================
// Delegate checkbox + delete clicks inside #eximg
// ============================================================

document.getElementById("eximg")?.addEventListener("click", (e) => {
    const checkbox = e.target.closest("input.select-images");
    const deleteBtn = e.target.closest(".delete");

    if (checkbox) {
        const imgId = checkbox.getAttribute("id");
        if (deleteBtn) handleImageDelete(imgId);
    }

    if (deleteBtn) {
        // find the nearest row's img id via the checkbox sibling
        const row = deleteBtn.closest("tr[data-img-id]");
        const imgId = row?.dataset.imgId;
        if (imgId) handleImageDelete(imgId);
    }
});

// ============================================================
// Gallery trigger button
// ============================================================

document.getElementById("img-gal")?.addEventListener("click", (e) => {
    const btn = e.currentTarget;
    const content = btn.dataset.content;
    const type = btn.dataset.type; // 'multi' | 'single' | 'thumb'
    bgt(content, type);
});

// ============================================================
// Build image row HTML for the extra images table
// ============================================================

function buildImageRow(content, pid, ifv, imid) {
    const tr = document.createElement("tr");
    tr.className = "thumbs";
    tr.dataset.imgId = imid;
    tr.draggable = true;
    tr.addEventListener("dragover", dragover);
    tr.addEventListener("dragstart", dragstart);
    tr.addEventListener("dragend", dragend);
    tr.addEventListener("drop", drop);

    tr.innerHTML = `
        <td class="grabbable">
            <i class="fa fa-arrows-alt"></i>
            <input type="checkbox"
                   class="select-images"
                   id="${imid}"
                   value="${imid}"
                   data-content="${content}"
                   data-pid="${pid}"
                   data-ifv="${ifv}">
            <input name="cur_imagepaths[]" type="hidden" value="${ifv}">
        </td>
        <td>
            <img class="nodrag"
                 draggable="false"
                 src="${thumbspath}${ifv}"
                 alt="">
        </td>`;

    return tr;
}

// ============================================================
// Replace or append a single/thumb image preview
// ============================================================

function setSingleImage({
    boxId,
    imgId,
    fieldId,
    src,
    thumb = true,
    thumbSize = 200,
}) {
    const box = document.getElementById(boxId);
    const input = document.getElementById(fieldId);

    if (!box) {
        console.warn(`setSingleImage: #${boxId} not found`);
        return;
    }

    if (input) input.value = src;

    const resolvedSrc = thumb ? thumbSrc(src, thumbSize) : fullSrc(src);
    const existing = document.getElementById(imgId);

    if (existing) {
        existing.src = resolvedSrc;
    } else {
        const img = document.createElement("img");
        img.id = imgId;
        img.src = resolvedSrc;
        img.alt = "";
        img.className = "rounded border max-h-32";
        box.appendChild(img);
    }

    box.querySelectorAll("span.loadprogress").forEach(
        (el) => (el.innerHTML = ""),
    );
}

// ============================================================
// Main gallery handler
// ============================================================

export function bgt(content, type, boxId, imgId, fieldId) {
    const form = document.getElementById(content);
    if (!form) return;

    const formData = new FormData(form);
    const entries = [...formData.entries()];
    const pid = entries[0]?.value ?? "";

    if (type === "multi") {
        const tbody =
            document.querySelector("table.extra-images-order tbody") ??
            document.querySelector("table.extra-images-order");
        if (!tbody) return;

        let currentIfv = "";
        entries.forEach(({ name, value }) => {
            if (name === "image_gal_array[]") currentIfv = value;
            if (name === "imagepaths[]") {
                tbody.appendChild(
                    buildImageRow(content, pid, currentIfv, value),
                );
            }
        });
    } else if (type === "single" || type === "thumb") {
        const checked = document.querySelector(
            '#restab input[type="radio"]:checked, #restab input.select-images:checked',
        );

        if (!checked) {
            console.warn("No image selected");
            return;
        }

        const ifv = checked.value;
        console.log(ifv, "selected image");

        setSingleImage({
            boxId:
                boxId ?? (type === "single" ? "main-img-box" : "thumb-img-box"),
            imgId: imgId ?? (type === "single" ? "main-img" : "thumb-img"),
            fieldId:
                fieldId ?? (type === "single" ? "imagepath" : "imagethumb"),
            src: ifv,
            thumb: true,
            thumbSize: 200,
        });
    }
}

// ============================================================
// Legacy alias used by older bgt_multi_img callers if needed
// ============================================================

export function bgt_multi_img(content, type) {
    bgt(content, type);
}

export function initImageGallery() {
    console.log("initIMageGalery.js");

    initImageModal();
    document.getElementById("eximg")?.addEventListener("click", (e) => {
        const deleteBtn = e.target.closest(".delete");
        if (deleteBtn) {
            const row = deleteBtn.closest("tr[data-img-id]");
            const imgId = row?.dataset.imgId;
            if (imgId) handleImageDelete(imgId);
        }
    });

    document.getElementById("img-gal")?.addEventListener("click", (e) => {
        const btn = e.currentTarget;
        const content = btn.dataset.content;
        const type = btn.dataset.type;
        bgt(content, type);
    });
}
