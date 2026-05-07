export function multipleDelete(content, checked, del, item = "") {
    const selectedCheckboxes = document.querySelectorAll(
        ".select-images:checked",
    );
    const delIds = [];
    const checkedFiles = [];

    selectedCheckboxes.forEach((checkbox) => {
        const id = checkbox.dataset.id;
        const filename = checkbox.dataset.filename;
        if (id) delIds.push(id);
        if (filename) checkedFiles.push(filename);
    });

    if (delIds.length === 0) {
        alert("Please select at least one image to delete");
        return false;
    }

    const delstr =
        "Are you sure you want to delete this image from the system?";

    if (del && !confirm(delstr)) {
        return false;
    }

    const delParam = delIds.join(",");
    const checkedParam = checkedFiles.join(",");

    // console.log('Deleting IDs:', delParam);
    // console.log('Deleting files:', checkedParam);
    // console.log('Calling loadContent with:', content, item, delParam, checkedParam);

    // Pass as: loadContent(action, item, delitem, page)
    window.loadContent(content, item, delParam, checkedParam);

    return true;
}

// Optional: Update button text with selected count
export function updateDeleteButton() {
    const selectedCount = document.querySelectorAll(
        ".select-images:checked",
    ).length;
    const deleteBtn = document.querySelector(".btn-delete-selected");

    if (deleteBtn) {
        if (selectedCount > 0) {
            deleteBtn.textContent = `Delete Selected (${selectedCount})`;
            deleteBtn.disabled = false;
        } else {
            deleteBtn.textContent = "Delete Selected";
            deleteBtn.disabled = true;
        }
    }
}
