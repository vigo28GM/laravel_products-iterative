const form = document.getElementById('quantity-form');

if (form) {
    const quantity = document.getElementById('quantity');
    const flash = document.getElementById('quantity-flash');

    const showFlash = (type, messages) => {
        const box = document.createElement('div');
        box.className = `flash flash-${type}`;
        box.setAttribute('role', type === 'success' ? 'status' : 'alert');
        messages.forEach((text) => {
            const line = document.createElement('div');
            line.textContent = text;
            box.append(line);
        });
        flash.replaceChildren(box);
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const data = new FormData(form);
        data.set('action', event.submitter.value);
        const buttons = form.querySelectorAll('button');
        buttons.forEach((button) => (button.disabled = true));

        try {
            const response = await fetch(form.action, {
                method: 'POST', // _method=PATCH is in the form data
                headers: { Accept: 'application/json' },
                body: data,
            });
            const body = await response.json();

            if (response.ok) {
                quantity.textContent = body.quantity;
                showFlash('success', [body.message]);
            } else {
                showFlash('errors', Object.values(body.errors ?? { error: [body.message] }).flat());
            }
        } catch {
            showFlash('errors', ['Network error, please try again.']);
        } finally {
            buttons.forEach((button) => (button.disabled = false));
        }
    });
}

import './tags';
