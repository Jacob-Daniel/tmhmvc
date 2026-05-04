const validateInput = (
	type: "name" | "message" | "email",
	value: string,
): string | null => {
	switch (type) {
		case "name":
			return /^[A-Za-z\s]{2,}$/.test(value)
				? null
				: "Min 2 characters, no numbers of special characters";
		case "message":
			return value.length >= 10 ? null : "Min 10 characters required";
		case "email":
			return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
				? null
				: "Incorrect format";
		default:
			return null;
	}
};

interface SendEmailRes {
	message: string;
	error: boolean;
}

interface SendEmailData {
	message: string;
	name: string;
	email: string;
}

export async function initFormSend() {
	const formElem = document.getElementById("email-form") as HTMLFormElement;
	const resultElem = document.getElementById("result") as HTMLOutputElement;

	if (!formElem) return;

	formElem.addEventListener("submit", async (e) => {
		e.preventDefault();

		const formData = new FormData(formElem);

		if (formData.get("company")) {
			console.log("Bot detected");
			return;
		}

		const name = formData.get("name") as string;
		const email = formData.get("email") as string;
		const message = formData.get("message") as string;

		// Validate
		const nameError = validateInput("name", name);
		const emailError = validateInput("email", email);
		const messageError = validateInput("message", message);

		if (nameError || emailError || messageError) {
			showResult(resultElem, nameError || emailError || messageError, true);
			return;
		}

		// Disable button
		const submitBtn = formElem.querySelector(
			'button[type="submit"]',
		) as HTMLButtonElement;
		submitBtn.disabled = true;
		submitBtn.textContent = "Sending...";

		// Send data
		const res: SendEmailData = { name, email, message };
		const response = await postData(res);

		// Handle response
		if (response) {
			const data: SendEmailRes = await response.json();
			showResult(resultElem, data.message, data.error);

			if (!data.error) {
				formElem.reset();
			}
		} else {
			showResult(resultElem, "Network error. Please try again.", true);
		}

		// Re-enable button
		submitBtn.disabled = false;
		submitBtn.textContent = "Submit";
	});
}

function showResult(
	elem: HTMLOutputElement,
	message: string | null,
	isError: boolean,
) {
	elem.textContent = message;
	elem.classList.remove("hidden");
	elem.classList.toggle("border-red-500", isError);
	elem.classList.toggle("border-green-500", !isError);
	elem.classList.toggle("text-red-700", isError);
	elem.classList.toggle("text-green-700", !isError);
}

async function postData(data: SendEmailData) {
	const url = "/api/sendmail.php";
	try {
		const res = await fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
			},
			body: new URLSearchParams({
				name: data.name,
				message: data.message,
				email: data.email,
			}),
		});
		return res;
	} catch (error) {
		console.error("Fetch error:", error);
		return null;
	}
}
