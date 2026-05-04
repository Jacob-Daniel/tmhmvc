import "../../css/admin/main.css";
export function initLogin() {
    const form = document.getElementById("login") as HTMLFormElement;
    if (!form) return;
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const res = document.getElementById("res") as HTMLElement;

        const btn = form.querySelector("button") as HTMLButtonElement;
        btn.disabled = true;

        const data = new FormData(form);
        const resp = await fetch("/admin/api/login", {
            method: "POST",
            body: data,
            credentials: "same-origin",
        });
        const json = await resp.json();

        if (json.success) {
            window.location.href = json.redirect;
        } else {
            res.className = "p-2 bg-red-200 text-red-500";
            res.textContent = json.error;
            btn.disabled = false;
            setTimeout(() => {
                res.className = "";
                res.textContent = "";
            }, 5000);
        }
    });
}

if (document.getElementById("login")) {
    initLogin();
}

const validateEmail = (email: string): string | null => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
        ? null
        : "Incorrect email format";
};

interface ApiRes {
    message: string;
    error: boolean;
}

export function initResetForm() {
    const formElem = document.getElementById("reset-form") as HTMLFormElement;
    const resultElem = document.getElementById("result") as HTMLOutputElement;
    if (!formElem) return;

    formElem.addEventListener("submit", async (e) => {
        e.preventDefault();
        console.log(e, "submit");
        const formData = new FormData(formElem);
        const email = (formData.get("email") as string).trim();

        // Simple bot check
        if (formData.get("company")) {
            console.log("Bot detected");
            return;
        }

        // Validate email
        const emailError = validateEmail(email);
        if (emailError) {
            showResult(resultElem, emailError, true);
            return;
        }

        // Disable submit
        const submitBtn = formElem.querySelector(
            'button[type="submit"]',
        ) as HTMLButtonElement;
        submitBtn.disabled = true;
        submitBtn.textContent = "Sending...";

        // Send request
        const response = await postData(email);
        if (response) {
            const data: ApiRes = await response.json();
            showResult(resultElem, data.message, data.error);
            if (!data.error) formElem.reset();
        } else {
            showResult(resultElem, "Network error. Please try again.", true);
        }

        submitBtn.disabled = false;
        submitBtn.textContent = "Submit";
    });
}

function showResult(
    elem: HTMLOutputElement,
    message: string,
    isError: boolean,
) {
    elem.textContent = message;
    elem.classList.remove("hidden");
    elem.classList.toggle("border-red-500", isError);
    elem.classList.toggle("border-green-500", !isError);
    elem.classList.toggle("text-red-700", isError);
    elem.classList.toggle("text-green-700", !isError);
}

async function postData(email: string) {
    const url = "/api/sendresetmail.php";
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ email }),
        });
        return res;
    } catch (error) {
        console.error("Fetch error:", error);
        return null;
    }
}

initResetForm();
