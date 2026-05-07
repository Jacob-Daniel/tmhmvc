import { GalleryParams } from "../types";
import { showToast, showConfirmModal } from "./uiMessages";

export function initGallery(params: GalleryParams = {}) {
    const searchInput = document.getElementById("psch") as HTMLInputElement;

    if (searchInput) {
        let debounceTimer: number | undefined;
        searchInput.focus();
        searchInput.setSelectionRange(
            searchInput.value.length,
            searchInput.value.length,
        );
        searchInput.addEventListener("keyup", () => {
            clearTimeout(debounceTimer);

            debounceTimer = window.setTimeout(() => {
                const value = searchInput.value.trim();

                if (value.length >= 2) {
                    window.loadContent(
                        "gallery",
                        null,
                        null,
                        "1", // reset to page 1
                        value,
                    );
                }

                // If cleared, reload full gallery
                if (value.length === 0) {
                    window.loadContent("gallery", null, null, "1");
                }
            }, 400); // 400ms debounce
        });
    }

    const selectAllCheckbox = document.getElementById(
        "select-all",
    ) as HTMLInputElement;

    const deleteBtn = document.getElementById(
        "deleteSelected",
    ) as HTMLButtonElement;

    const uploadBtn = document.getElementById(
        "uploadsubmit",
    ) as HTMLButtonElement;

    const fileInput = document.getElementById("imagepath") as HTMLInputElement;

    /* ---------------------------
       Select All
    ---------------------------- */

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const checkboxes =
                document.querySelectorAll<HTMLInputElement>(".select-images");

            checkboxes.forEach((cb) => (cb.checked = this.checked));
        });
    }

    /* ---------------------------
       Delete Selected
    ---------------------------- */

    if (deleteBtn) {
        deleteBtn.addEventListener("click", () => {
            const checkboxes = document.querySelectorAll<HTMLInputElement>(
                ".select-images:checked",
            );

            if (checkboxes.length === 0) {
                showToast("Please select images to delete", "error");
                return;
            }

            const ids: string[] = [];

            checkboxes.forEach((cb) => {
                if (cb.dataset.id) {
                    ids.push(cb.dataset.id);
                }
            });

            showConfirmModal(
                `Delete ${ids.length} selected image(s)?`,
                async () => {
                    deleteBtn.disabled = true;
                    deleteBtn.textContent = "Deleting...";

                    try {
                        for (const id of ids) {
                            const res = await fetch(
                                `/admin/api/delete?table=images&id=${id}`,
                            );

                            if (!res.ok) throw new Error(`HTTP ${res.status}`);

                            const data = await res.json();

                            if (!data.success) {
                                throw new Error(data.error || "Delete failed");
                            }
                        }

                        showToast(
                            `${ids.length} image(s) deleted successfully`,
                            "success",
                        );

                        await window.loadContent("gallery");
                    } catch (err: any) {
                        showToast(err.message || "Delete failed", "error");
                    } finally {
                        deleteBtn.disabled = false;
                        deleteBtn.textContent = "Delete Selected";
                    }
                },
            );
        });
    }

    /* ---------------------------
       Upload
    ---------------------------- */

    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener("click", async () => {
            if (!fileInput.files?.length) {
                showToast("Please select files to upload", "error");
                return;
            }

            const form = document.getElementById("gal") as HTMLFormElement;
            if (!form) return;

            const formData = new FormData(form);

            uploadBtn.disabled = true;
            uploadBtn.textContent = "Uploading...";

            try {
                const resp = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                });

                if (!resp.ok) {
                    throw new Error(`HTTP ${resp.status}`);
                }

                const result = await resp.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                showToast(result.message || "Upload successful!", "success");

                form.reset();

                // Clean reload (no legacy params)
                await window.loadContent("gallery");
            } catch (err: any) {
                console.error("Upload error:", err);

                showToast(err.message || "Upload failed", "error");
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.textContent = "Upload";
            }
        });
    }
}
