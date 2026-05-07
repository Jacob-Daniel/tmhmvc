import { showToast, showConfirmModal } from "./uiMessages";

export function initDeleteHandler() {
    document.addEventListener("click", async (e) => {
        const btn = (e.target as HTMLElement).closest("[data-action='delete']");
        if (!btn) return;

        const table = btn.getAttribute("data-table");
        const target = btn.getAttribute("data-target");
        const id = Number(btn.getAttribute("data-id"));
        if (!table || !id) return;

        showConfirmModal(
            `Are you sure you want to delete this ${table}?`,
            async () => {
                try {
                    const res = await fetch(
                        `/admin/api/delete?table=${table}&id=${id}`,
                    );
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const data = await res.json();

                    if (!data.success) {
                        showToast(data.error || "Delete failed", "error");
                        return;
                    }

                    showToast(
                        data.message || "Deleted successfully",
                        "success",
                    );

                    if (target) {
                        await window.loadContent(target);
                    }

                    if (typeof window.loadContent === "function" && target) {
                        window.loadContent(target);
                    }
                } catch (err: any) {
                    showToast(`Network error: ${err.message}`, "error");
                }
            },
        );
    });
}
