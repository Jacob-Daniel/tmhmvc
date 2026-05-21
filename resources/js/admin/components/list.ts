import { DataModuleButton, DataModuleContainer } from "../types";

export function initList(params = {}) {
    const searchInput = document.getElementById(
        "psch",
    ) as HTMLInputElement | null;
    if (!searchInput) return;

    const targetId = searchInput.dataset.target || "restable";
    const restab = document.getElementById(targetId);
    if (!restab) return;

    async function fetchList() {
        const table = encodeURIComponent(searchInput!.dataset.table || "");
        const field = encodeURIComponent(searchInput!.dataset.field || "");
        const val = searchInput!.value.trim();

        if (val.length > 0 && val.length < 3) return;

        // Collect all data-filter elements within the same fieldset/container
        const container = searchInput!.closest("fieldset") ?? document;
        const filterEls =
            container.querySelectorAll<HTMLElement>("[data-filter]");
        const extraParams = new URLSearchParams();
        filterEls.forEach((el) => {
            const filterField = el.dataset.filter!;
            const value = (el as HTMLInputElement | HTMLSelectElement).value;
            if (filterField && value !== "") {
                console.log(value, "value filter", filterField);
                extraParams.set(filterField, value);
            }
        });

        const url = `/admin/api/getlist?table=${table}&fld=${field}&val=${encodeURIComponent(val)}&${extraParams.toString()}&_=${Date.now()}`;

        try {
            const resp = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!resp.ok) throw new Error("Failed to fetch list");
            const html = await resp.text();
            restab!.innerHTML = html;
        } catch (err) {
            console.error("getList error:", err);
        }
    }

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

    // Bind any data-filter elements (selects, inputs) in the same container
    const container = searchInput.closest("fieldset") ?? document;
    container.querySelectorAll<HTMLElement>("[data-filter]").forEach((el) => {
        el.addEventListener("change", () => fetchList());
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
