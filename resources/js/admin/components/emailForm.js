export function initEmailForm() {
    // Initialize placeholder selectors
    initPlaceholderSelector("plch", "em_body");
    initPlaceholderSelector("plche", "em_body");
    initPlaceholderSelector("plchp", "em_body");
}

/**
 * Initialize a single placeholder selector
 * @param {string} selectId - The ID of the select element
 * @param {string} textareaId - The ID of the textarea element
 */
function initPlaceholderSelector(selectId, textareaId) {
    const select = document.getElementById(selectId);
    if (!select) return;

    // Remove any existing onchange attribute
    select.removeAttribute("onchange");

    // Add event listener
    select.addEventListener("change", () => {
        insertPlaceholder(select, textareaId);
    });
}

/**
 * Insert a placeholder at cursor position
 * @param {HTMLSelectElement} select - The select element
 * @param {string} textareaId - The ID of the textarea element
 */
function insertPlaceholder(select, textareaId) {
    const value = select.value;
    if (!value) return;

    // Check if TinyMCE is active
    if (typeof tinymce !== "undefined" && tinymce.get(textareaId)) {
        // Insert using TinyMCE
        const editor = tinymce.get(textareaId);
        if (editor) {
            editor.execCommand("mceInsertContent", false, value);
        }
    } else {
        // Fallback for regular textarea
        const textarea = document.getElementById(textareaId);
        if (textarea) {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const before = text.substring(0, start);
            const after = text.substring(end, text.length);

            textarea.value = before + value + after;
            textarea.selectionStart = textarea.selectionEnd =
                start + value.length;
            textarea.focus();

            // Trigger input event for any listeners
            textarea.dispatchEvent(new Event("input", { bubbles: true }));
        }
    }

    // Reset select to default
    select.value = "";
}

// Export for global use if needed (legacy support)
if (typeof window !== "undefined") {
    window.insertPlaceholder = insertPlaceholder;
    window.initEmailForm = initEmailForm;
}
