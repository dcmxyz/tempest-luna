import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'

const pages = import.meta.glob('../pages/**/*.svelte');

function resolve(name: string): Promise<{ default: object }> {
    const page = pages[`../pages/${name}.svelte`];

    if (!page) {
        showPageNotFound(name);
        throw new Error(`Component not found: "${name}.svelte"`);
    }

    return page();
}

void createInertiaApp({
    resolve,
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
});

function showPageNotFound(name: string) {
    document.body.innerHTML = `
        <div style="font-family:monospace;padding:2rem;background:#fff;color:#000;min-height:100vh">
            <p style="font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;color:#a8a29e">${name}.svelte</p>
            <p style="color:red;">The component could not be loaded.</p>
        </div>
    `
}