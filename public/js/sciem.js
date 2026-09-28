/**
 * Modales <dialog> reutilizables para crear / ver / editar.
 *   abrirForm('modal-id', { url, method, values, title, ro, keep })
 *   - values: {campo: valor} para rellenar el formulario (si falta, se limpia)
 *   - ro: modo solo lectura (oculta el botón de guardar)
 *   - keep: conserva lo que ya hay en los campos (para reabrir tras un error de validación)
 */
function abrirForm(id, o = {}) {
    const d = document.getElementById(id);
    const f = d.querySelector('form');
    const values = o.values || {};

    [...f.elements].forEach(el => {
        if (!el.name || el.name[0] === '_' ) return;
        if (!o.keep) {
            if (el.type === 'radio') {
                el.checked = (el.name in values) ? String(values[el.name]) === el.value : el.defaultChecked;
            } else if (el.type !== 'submit' && el.type !== 'button') {
                el.value = (el.name in values) ? (values[el.name] ?? '') : (el.dataset.default ?? '');
            }
        }
        el.disabled = !!o.ro;
    });

    const ed = f.querySelector('[name=_editing]');
    if (ed && !o.keep) ed.value = o.editing || '';

    if (o.url) f.action = o.url;
    const m = f.querySelector('input[name=_method]');
    if (m) { m.value = o.method || 'POST'; m.disabled = (o.method || 'POST') === 'POST'; }

    const t = d.querySelector('[data-title]');
    if (t && o.title) t.textContent = o.title;
    const s = f.querySelector('[data-submit]');
    if (s) s.hidden = !!o.ro;
    f.querySelectorAll('.err').forEach(e => { if (!o.keep) e.remove(); });

    d.showModal();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('dialog').forEach(d =>
        d.addEventListener('click', e => { if (e.target === d) d.close(); }));
});
