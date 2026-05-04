// TOAST -----------------------------

export function showToast(
    msg: string,
    type: "success" | "error" | "info" = "info",
) {
    const colors = {
        success: "bg-green-100 text-green-800",
        error: "bg-red-100 text-red-800",
        info: "bg-blue-100 text-blue-800",
    };

    let container = document.getElementById("toast-container");

    if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        container.className = "fixed top-16 right-10 space-y-2 z-50";
        document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = `px-4 py-2 rounded shadow ${colors[type]} transition-all`;
    toast.textContent = msg;

    container.appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
}

// MODAL -----------------------------

export function showModalMessage(message: string) {
    const modal = document.getElementById("resModal");
    const box = document.getElementById("libox");

    if (!modal || !box) return;

    box.textContent = message;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

export function closeModal() {
    const modal = document.getElementById("resModal");
    if (!modal) return;

    const confirmBtn = document.getElementById("resModalConfirm");
    if (confirmBtn) confirmBtn.remove();

    modal.classList.add("hidden");
    modal.classList.remove("flex");

    modalConfirmCallback = null;
}

// INIT (bind close events once)
export function initModal() {
    const modal = document.getElementById("resModal");
    if (!modal) return;

    modal.addEventListener("click", (e) => {
        if (
            (e.target as HTMLElement).classList.contains("modal-overlay") ||
            (e.target as HTMLElement).classList.contains("close-modal")
        ) {
            closeModal();
        }
    });
}

export function showMessage(
    text: string,
    type: "success" | "error" | "info" = "success",
) {
    const container = document.getElementById("message");
    if (!container) {
        alert(text);
        return;
    }

    const baseClasses = [
        "w-full",
        "text-center",
        "mb-5",
        "text-sm",
        "p-2",
        "rounded",
    ];
    const colourClasses = [
        "text-green-600",
        "bg-green-100",
        "text-red-600",
        "bg-red-100",
        "text-blue-600",
        "bg-blue-100",
    ];
    const colourMap = {
        success: ["text-green-600", "bg-green-100"],
        error: ["text-red-600", "bg-red-100"],
        info: ["text-blue-600", "bg-blue-100"],
    };

    container.classList.add(...baseClasses);
    container.classList.remove(...colourClasses);
    container.classList.add(...(colourMap[type] ?? colourMap.info));
    container.textContent = text;
    container.classList.remove("hidden");

    setTimeout(() => {
        container.textContent = "";
        container.classList.add("hidden");
    }, 3000);
}

let modalConfirmCallback: (() => void) | null = null;

export function showConfirmModal(message: string, onConfirm: () => void) {
    const modal = document.getElementById("resModal");
    const box = document.getElementById("libox");
    const closeBtn = document.getElementById("resModalClose");

    if (!modal || !box || !closeBtn) return;

    box.textContent = message;
    modal.classList.remove("hidden");
    modal.classList.add("flex");

    modalConfirmCallback = onConfirm;

    const confirmBtn = document.createElement("button");
    confirmBtn.textContent = "Confirm";
    confirmBtn.className =
        "px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded ml-2";
    confirmBtn.id = "resModalConfirm";

    closeBtn.insertAdjacentElement("afterend", confirmBtn);

    confirmBtn.onclick = () => {
        modalConfirmCallback?.();
        closeModal();
    };

    closeBtn.onclick = closeModal;
}
