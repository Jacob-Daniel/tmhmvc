// edField.ts
import { showToast, showModalMessage } from "./uiMessages";

/**
 * Show the inline edit input box
 */
export function editF(div: string, pid: number) {
    const el = document.getElementById(`${div}Dv${pid}`);
    if (el) el.classList.remove("hidden");
}

/**
 * Hide the inline edit input box
 */
export function hideEdit(div: string, pid: number) {
    const el = document.getElementById(`${div}Dv${pid}`);
    if (el) el.classList.add("hidden");
}

/**
 * Save the edited field via AJAX
 */
export async function goEdit(
    table: string,
    div: string,
    fld: string,
    pid: number,
) {
    const input = document.getElementById(`n${div}${pid}`) as HTMLInputElement;
    if (!input) return console.error("Input not found", `n${div}${pid}`);

    const value = input.value;
    const span = document.getElementById(`${div}${pid}`);
    const editBox = document.getElementById(`${div}Dv${pid}`);

    try {
        const params = new URLSearchParams({
            table,
            fld,
            val: value,
            pid: pid.toString(),
        });

        const res = await fetch(
            `/admin/api/editfield?${params.toString()}`,
            {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            },
        );

        if (!res.ok) throw new Error(`Server error: ${res.status}`);

        const data = await res.json();

        if (data.error) {
            showModalMessage(`Error updating field: ${data.error}`);
            return;
        }

        if (span) span.textContent = data.value ?? value;
        if (editBox) editBox.classList.add("hidden");

        showToast("Field updated successfully.", "success");

        if (data.isr) {
            console.log("ISR revalidation:", data.isr);
        }
    } catch (err) {
        console.error("Edit field error:", err);
        showModalMessage(`Update failed: ${(err as Error).message}`);
    }
}

/**
 * Load products from select element
 */
export function goProds(elem: HTMLSelectElement) {
    const prod_id = elem.value;
    if (window.loadContent) window.loadContent("prodlist", { id: prod_id });
}
