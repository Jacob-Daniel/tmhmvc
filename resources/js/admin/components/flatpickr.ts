import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

export function initFlatpickr(): void {
    const dateInputs =
        document.querySelectorAll<HTMLInputElement>(".datepickr");
    const timeInputs =
        document.querySelectorAll<HTMLInputElement>(".timepickr");

    if (dateInputs.length > 0) {
        flatpickr(dateInputs, {
            dateFormat: "Y-m-d",
        });
    }

    if (timeInputs.length > 0) {
        flatpickr(timeInputs, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });
    }
}
