import { initTinyMCE } from "./tinymce.js";
import { initFlatpickr } from "./flatpickr.js";
import { initImageModal } from "./imageModalNew";

export function initForm() {
    setTimeout(() => {
        initTinyMCE();
        initFlatpickr();
        initImageModal();
        toggleRecurring();
    }, 100);
}

function toggleRecurring() {
    const recurringToggle = document.getElementById("is_recurring");
    if (recurringToggle) {
        recurringToggle.addEventListener("change", function () {
            const fields = document.getElementById("recurring-fields");
            if (fields) {
                fields.classList.toggle("hidden", !this.checked);
            }
        });
    }
}
