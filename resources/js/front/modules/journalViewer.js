const viewer = document.querySelector("#journalism-viewer");

export function initJournalismSections() {
    const sections = document.querySelectorAll("[data-item-section]");

    sections.forEach((section) => {
        section.style.transform = "translateY(3rem)";
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove("opacity-0");
                    entry.target.classList.add("opacity-100");
                    entry.target.style.transform = "translateY(0)";
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.1,
            rootMargin: "100px 0px -50px 0px",
        },
    );

    setTimeout(() => {
        sections.forEach((section) => observer.observe(section));
    }, 300);
}

export function initJournalViewer() {
    document.addEventListener("click", (e) => {
        // Trigger click
        const trigger = e.target.closest(".more, .img-box");
        if (trigger) {
            e.preventDefault();
            handleTriggerClick(trigger);
        }

        // Close click
        if (e.target.matches("[data-close]")) {
            closeViewer();
        }
    });
}

async function handleTriggerClick(trigger) {
    const { itemid, prodid, catid } = trigger.dataset;
    if (!itemid || !prodid || !catid) return;

    try {
        document.getElementById("spinner")?.classList.remove("hidden");

        const postData = new URLSearchParams({
            id: itemid,
            cat_id: catid,
            prod_id: prodid,
            pcid: 1, // journalism
        });
        const response = await fetch("/inc/extracontent-journal.php", {
            method: "POST",
            body: postData,
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();
        if (data.error) throw new Error(data.error);

        document.getElementById("spinner")?.classList.add("hidden");

        const cleanDesc = data.desc?.replace(/<p>(&nbsp;|\s)*<\/p>/gi, "");

        setHTML("[data-h2-container]", data.h2);
        setHTML("[data-video-container]", data.video);
        setHTML("[data-desc-container]", cleanDesc);

        openViewer();
    } catch (err) {
        console.error("Failed to load content:", err);
        document.getElementById("spinner")?.classList.add("hidden");
    }
}

function setHTML(selector, html) {
    const el = viewer.querySelector(selector);
    if (el) el.innerHTML = html ?? "";
}

function openViewer() {
    viewer.classList.remove("hidden");
    void viewer.offsetWidth;
    viewer.classList.remove("opacity-0");
    const content = viewer.querySelector("[data-viewer-container]");
    content.classList.remove("scale-x-50");
    content.classList.add("scale-x-100");
}

function closeViewer() {
    const content = viewer.querySelector("[data-viewer-container]");
    content.classList.add("scale-x-90");

    setTimeout(() => {
        viewer.classList.add("opacity-0");
    }, 50);

    setTimeout(() => {
        viewer.classList.add("hidden");
        viewer.querySelector("[data-video-container]").innerHTML = "";
        content.classList.remove("scale-x-90", "scale-x-100");
        content.classList.add("scale-x-50");
    }, 200);
}
