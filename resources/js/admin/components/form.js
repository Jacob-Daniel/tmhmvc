import { initTinyMCE } from "./tinymce.js";
import { initImageModal } from "./imageModalNew";

export function initForm() {
    setTimeout(() => {
        initTinyMCE();
        initFlatpickr();
        initImageModal();
    }, 100);
}

function initFlatpickr() {
    if (!window.flatpickr) {
        console.warn("Flatpickr not loaded");
        return;
    }
    const dateInputs = document.querySelectorAll(".datepickr");
    if (dateInputs.length === 0) {
        console.warn('No elements with class "datepickr" found');
        return;
    }
    flatpickr(".datepickr", { dateFormat: "Y-m-d" });
}
