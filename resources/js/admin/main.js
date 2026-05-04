import "../../css/admin/main.css";
import { initImageModal, openImageModal } from "./components/imageModalNew.ts";
import { showMessage } from "./components/uiMessages";
import flatpickr from "flatpickr";
import { initGallery } from "./components/gallery";
import { setActiveMenu } from "./components/setActiveMenu";
import { initForm } from "./components/form";
import { initLogin } from "./components/login";
import { initList } from "./components/list";
import { flipField } from "./components/flipField";
import { initDeleteHandler } from "./components/delete";
import { initAjaxForms, saveForm } from "./components/save";
import { editF, hideEdit, goEdit, goProds } from "./components/editField.ts";
import { multipleDelete, updateDeleteButton } from "./components/delImage";
import { initActionDelegates } from "./components/actionButtons.ts";

import {
    initImageGallery,
    bgt,
    bgt_multi_img,
} from "./components/loadImageGallery.js";

const baseUrl =
    typeof CONFIG !== "undefined" && CONFIG.baseUrl ? CONFIG.baseUrl : "";

async function loadContent(action, item, delitem, page, condition = "") {
    const el = document.getElementById("main");
    const loading = document.getElementById("loading");
    if (!el) return console.error("Target element not found for loadContent");

    //TODO: REMOVE delitem param and this:
    // if (delitem && action !== "gallery") {
    //     if (
    //         !confirm(
    //             "Are you sure you want to delete this item from the system",
    //         )
    //     ) {
    //         return false;
    //     }
    // }

    const query = new URLSearchParams({
        action,
        item: item ?? "",
        delitem: delitem ?? "",
        page: page ?? "",
        condition: condition ?? "",
    }).toString();

    const url = `${baseUrl}/admin/index.php?${query}`;

    try {
        const response = await fetch(url, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (response.status === 401) {
            window.location.href = "/admin/login.php";
            return;
        }

        if (!response.ok) {
            throw new Error(`Failed to load content: ${response.status}`);
        }

        const html = await response.text();
        el.innerHTML = html;
        setActiveMenu(action);
        initPageJS(action, { item, delitem, page, condition });
        initImageModal();
    } catch (err) {
        console.error("Admin load error:", err);
        loading.innerHTML =
            '<div class="bg-gray-100 p-2"><span class="text-black">There was a problem loading..</span></div>';
    }
}

let routerInitialized = false;
function initRouter() {
    if (routerInitialized) return;
    routerInitialized = true;

    document.addEventListener("click", (e) => {
        if (e.target.closest("[data-module]")) return;

        const el = e.target.closest("[data-route]");
        if (!el) return;

        e.preventDefault();
        e.stopPropagation();

        loadContent(
            el.dataset.route,
            el.dataset.item || null,
            el.dataset.delitem || null,
            el.dataset.page ?? "1",
            el.dataset.condition || "",
        );
    });
}

async function loadMenu() {
    const menuEl = document.getElementById("leftmenu");
    if (!menuEl) return;

    try {
        const res = await fetch(`/admin/api/loadmenu?action=menu`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error("Failed to load menu");
        const text = await res.text();
        menuEl.innerHTML = text;
    } catch (err) {
        console.error("Menu load error:", err);
    }
}

function initPageJS(action, params = {}) {
    initAjaxForms();
    switch (action) {
        case "pageform":
        case "catform":
        case "prodform":
        case "configform":
            initForm();
            initImageModal();
            break;
        case "navform":
            initForm();
            break;
        case "pagelist":
        case "catlist":
        case "navlist":
        case "prodlist":
            initList(params);
            break;
        case "imageform":
            break;
        case "userform":
            initForm();
            break;
        case "gallery":
            initGallery(params);
            break;
        default:
            initLogin();
            break;
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    await loadMenu();
    initRouter();
    initActionDelegates();
    loadContent("dashboard");
    initImageModal();
    initDeleteHandler();
});

// Expose to global scope for inline onclick handlers
window.loadContent = loadContent;
window.loadMenu = loadMenu;
window.flipField = flipField;
window.editF = editF;
window.initDeleteHandler = initDeleteHandler;
window.hideEdit = hideEdit;
window.goEdit = goEdit;
window.goProds = goProds;
window.initImageModal = initImageModal;
window.multipleDelete = multipleDelete;
window.updateDeleteButton = updateDeleteButton;
window.showMessage = showMessage;
window.saveForm = saveForm;
window.bgt = bgt;
window.bgt_multi_img = bgt_multi_img;
window.flatpickr = flatpickr;
window.openImageModal = openImageModal;
