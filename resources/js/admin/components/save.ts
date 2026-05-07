import { showMessage } from "./uiMessages";

export async function saveForm(form: HTMLFormElement) {
    if ((window as any).tinyMCE) {
        (window as any).tinyMCE.triggerSave();
    }
    const formData = new FormData(form);
    showMessage("Saving…", "info");

    try {
        const response = await fetch(form.action, {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        let data: any;
        try {
            data = await response.json();
        } catch (jsonErr) {
            const text = await response.text().catch(() => "Unknown error");
            showMessage(text, "error");
            return;
        }

        showMessage(data.message ?? "Saved", data.type ?? "success");
    } catch (err: any) {
        console.error("saveForm error:", err);
        showMessage("There was a problem saving. Please try again.", "error");
    }
}

export function initAjaxForms() {
    document.querySelectorAll("form[data-ajax]").forEach((form) => {
        if ((form as any).dataset.ajaxBound) return;
        (form as any).dataset.ajaxBound = "1";

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            await saveForm(form as HTMLFormElement);
        });
    });
}
