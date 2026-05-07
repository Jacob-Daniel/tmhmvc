import { DataModuleContainer, DataModuleButton } from "../types";

let actionDelegatesBound = false;
export function initActionDelegates() {
    if (actionDelegatesBound) return;
    actionDelegatesBound = true;

    document.getElementById("main")?.addEventListener("click", async (e) => {
        const btn = (e.target as HTMLElement).closest(
            "button[data-action]",
        ) as DataModuleButton | null;
        if (!btn) return;

        const container = btn.closest(
            "[data-module]",
        ) as DataModuleContainer | null;
        if (!container) return;

        const id = container.dataset.id;
        const action = btn.dataset.action;
        const target = container.dataset[action];

        if (!target) return;

        switch (action) {
            case "back":
            case "new":
                await window.loadContent(target!);
                break;

            case "refresh":
            case "edit":
                if (id) await window.loadContent(target!, id);
                break;

            case "save":
                const form =
                    document.querySelector<HTMLFormElement>("form[data-ajax]");
                if (!form) return;
                form.requestSubmit();
                break;
            case "token":
                const input = document.getElementById(
                    "token-value",
                ) as HTMLInputElement;
                input.removeAttribute("readonly");
                const arr = new Uint8Array(32);
                crypto.getRandomValues(arr);
                input.value = Array.from(arr)
                    .map((b) => b.toString(16).padStart(2, "0"))
                    .join("");
                input.setAttribute("readonly", "");
                console.log("token", input);
                break;
        }
    });
}
