export function initLogin() {
    const form = document.getElementById('login') as HTMLFormElement;
    const resBox = form?.querySelector('.res') as HTMLDivElement;
    if (!form) return;

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const resBox = form.querySelector('.res') as HTMLDivElement | null;

        const btn = form.querySelector('button') as HTMLButtonElement;
        btn.disabled = true;

        const data = new FormData(form);
        const resp = await fetch('/admin/api/login', {
            method: 'POST',
            body: data,
            credentials: 'same-origin',
        });
        const json = await resp.json();

        if (json.success) {
            window.location.href = json.redirect;
        } else {
            resBox.textContent = json.error;
            btn.disabled = false;
        }
    });
}

if (document.body.classList.contains('login')) {
    initLogin();
}