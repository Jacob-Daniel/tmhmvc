import { showToast, showModalMessage } from "./uiMessages";

export async function flipField(table, field, pid) {
    const flipid = `${field}_${pid}`;
    const url = `/admin/api/flipfield?field=${field}&pid=${pid}&table=${table}`;

    try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);

        const data = await res.json();

        const el = document.getElementById(flipid);
        if (el) {
            el.textContent = data.flag;

            if (data.flag === "Y") {
                el.classList.remove("text-gray-400");
                el.classList.add("text-blue-600");
            } else {
                el.classList.remove("text-blue-600");
                el.classList.add("text-gray-400");
            }
        }

        // 🔹 Toast for quick status
        showToast(
            `${table} ${field} is now ${data.flag === "Y" ? "active" : "inactive"}.`,
            "success",
        );

        // 🔹 Modal only if something important happened
        if (data.error) {
            showModalMessage(`Error: ${data.error}`);
        }

        if (data.isr) {
            console.log("ISR revalidation:", data.isr);
        }
    } catch (err) {
        console.error("Flip field error:", err);
        showModalMessage(`Network or server error: ${err.message}`);
    }
}
