export async function fetchImages({
    page = 1,
    content = "",
    type = "",
    search = "",
} = {}) {
    const resultsTab = document.getElementById("restab");
    if (!resultsTab) return;
    console.log(search, "searhc");
    const params = new URLSearchParams({
        fld: "imagepath",
        content,
        val: search,
        page: String(page),
        type,
    });

    const url = `/admin/api/getimages?${params.toString()}`;

    try {
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (!response.ok) {
            throw new Error(`Failed: ${response.status}`);
        }

        const html = await response.text();
        resultsTab.innerHTML = html;

        attachSelectionLogic(resultsTab);
    } catch (err) {
        console.error("Gallery error:", err);
        resultsTab.innerHTML =
            '<p class="text-red-600 p-4">Failed to load images</p>';
    }
}
