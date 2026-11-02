const form = document.getElementById('tags-form');

if (form) {
    const list = document.getElementById('tag-list');
    const input = document.getElementById('tag-input');
    const box = document.getElementById('tag-suggestions');

    let options = []; // { label, value }
    let active = 0;
    let timer;
    let requestId = 0;

    const normalize = (text) => text.trim().toLowerCase();
    const selected = () => [...list.querySelectorAll('li')].map((li) => li.dataset.name);

    const addTag = (raw) => {
        const name = normalize(raw);

        if (name === '' || selected().includes(name)) {
            return;
        }

        const item = document.createElement('li');
        item.dataset.name = name;
        item.append(name);

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'tags[]';
        hidden.value = name;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'tag-remove';
        remove.setAttribute('aria-label', `Remove tag ${name}`);
        remove.textContent = '×';

        item.append(' ', hidden, remove);
        list.append(item);
    };

    const close = () => {
        options = [];
        box.hidden = true;
        box.replaceChildren();
        input.setAttribute('aria-expanded', 'false');
    };

    const highlight = () => {
        [...box.children].forEach((li, index) => {
            li.setAttribute('aria-selected', String(index === active));
        });
    };

    const choose = (option) => {
        addTag(option.value);
        input.value = '';
        close();
    };

    const render = () => {
        box.replaceChildren(
            ...options.map((option, index) => {
                const li = document.createElement('li');
                li.setAttribute('role', 'option');
                li.textContent = option.label;
                // mousedown fires before the input loses focus (blur closes the box)
                li.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    choose(option);
                });
                li.addEventListener('mousemove', () => {
                    active = index;
                    highlight();
                });
                return li;
            }),
        );
        box.hidden = options.length === 0;
        input.setAttribute('aria-expanded', String(options.length > 0));
        active = 0;
        highlight();
    };

    const search = async () => {
        const typed = normalize(input.value);

        if (typed === '') {
            close();
            return;
        }

        const id = ++requestId;
        let names = [];

        try {
            const response = await fetch(`${form.dataset.searchUrl}?q=${encodeURIComponent(typed)}`, {
                headers: { Accept: 'application/json' },
            });
            names = response.ok ? await response.json() : [];
        } catch {
            names = [];
        }

        if (id !== requestId) {
            return; // a newer keystroke superseded this response
        }

        const chosen = selected();
        options = names.filter((name) => !chosen.includes(name)).map((name) => ({ label: name, value: name }));

        if (!names.includes(typed) && !chosen.includes(typed)) {
            options.push({ label: `Create "${typed}"`, value: typed });
        }

        render();
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(search, 150);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault(); // Enter selects a tag, it must not submit the form

            if (options.length > 0) {
                choose(options[active]);
            } else {
                addTag(input.value);
                input.value = '';
            }
        } else if (event.key === 'ArrowDown' && options.length > 0) {
            event.preventDefault();
            active = (active + 1) % options.length;
            highlight();
        } else if (event.key === 'ArrowUp' && options.length > 0) {
            event.preventDefault();
            active = (active - 1 + options.length) % options.length;
            highlight();
        } else if (event.key === 'Escape') {
            close();
        }
    });

    input.addEventListener('blur', close);

    list.addEventListener('click', (event) => {
        event.target.closest('.tag-remove')?.closest('li').remove();
    });
}
