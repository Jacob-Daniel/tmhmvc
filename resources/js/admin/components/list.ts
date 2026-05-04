import { DataModuleButton, DataModuleContainer } from "../types";

export function initList() {
    // const pageCount = document.querySelector(".page-count");
    const searchInput = document.getElementById("psch") as HTMLInputElement;
    const restab = document.getElementById("restab");

    async function fetchList() {
        if (!searchInput || !restab) return;
        const table = encodeURIComponent(
            searchInput.dataset.table || "products",
        );
        const field = encodeURIComponent(searchInput.dataset.field || "slug");
        const val = searchInput.value.trim();

        if (val.length > 0 && val.length < 3) return;

        const url = `/admin/api/getlist?table=${table}&fld=${field}&val=${encodeURIComponent(val)}`;

        try {
            const resp = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!resp.ok) throw new Error("Failed to fetch list");
            const data = await resp.text();
            restab.innerHTML = data;
        } catch (err) {
            console.error("getList error:", err);
        }
    }
    if (!searchInput) return;

    searchInput.addEventListener("input", () => fetchList());
    searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            fetchList();
        }
    });
    if (searchInput) {
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
    }

    document
        .querySelectorAll("#newPageBtn, #newPageBtnBottom")
        .forEach((btn) => {
            btn.addEventListener("click", () =>
                window.loadContent("pageform", null, null, null, ""),
            );
        });

    const parentSelect = document.getElementById("pageParent");
    parentSelect?.addEventListener("change", () => fetchList());

    if (searchInput && searchInput.value.trim().length) {
        fetchList();
    }
}
