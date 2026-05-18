import { DataModuleButton, DataModuleContainer } from "../types";

export function initList() {
    const searchInput = document.getElementById("psch") as HTMLInputElement;
    const targetId = searchInput.dataset.target || "restab";

    async function fetchList() {
        const restab = document.getElementById(
            searchInput.dataset.target || "restable",
        );
        if (!searchInput || !restab) return;

        const table = encodeURIComponent(
            searchInput.dataset.table || "products",
        );
        const field = encodeURIComponent(searchInput.dataset.field || "slug");
        const val = searchInput.value.trim();

        if (val.length > 0 && val.length < 3) return;

        const url = `/admin/api/getlist?table=${table}&fld=${field}&val=${encodeURIComponent(val)}&_=${Date.now()}`;

        try {
            const resp = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!resp.ok) throw new Error("Failed to fetch list");
            const html = await resp.text();
            restab.innerHTML = html;
        } catch (err) {
            console.error("getList error:", err);
        }
    }

    if (!searchInput) return;

    let timeout: number;
    searchInput.addEventListener("input", () => {
        clearTimeout(timeout);
        timeout = window.setTimeout(() => fetchList(), 300);
    });

    searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            fetchList();
        }
    });

    document
        .querySelectorAll("#newPageBtn, #newPageBtnBottom")
        .forEach((btn) => {
            btn.addEventListener("click", () =>
                window.loadContent("pageform", null, null, null, ""),
            );
        });

    document
        .getElementById("pageParent")
        ?.addEventListener("change", () => fetchList());
}
