import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

export function initFlatpickr() {
    if (!window.flatpickr) {
        console.warn("Flatpickr not loaded");
        return;
    }

    const dateInputs = document.querySelectorAll(".datepickr");
    if (dateInputs.length === 0) {
        console.warn('No elements with class "datepickr" found');
        return;
    }

    if (document.querySelector(".datepickr")) {
        flatpickr(".datepickr", {
            dateFormat: "Y-m-d",
            mode: "multiple",
        });
    }

    if (document.querySelector(".timepickr")) {
        flatpickr(".timepickr", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });
    }
}
