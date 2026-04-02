(function() {
  'use strict';
  const baseUrl = 'https://codelocksolutions.com/form_builder';
  const ajaxUrl = `${baseUrl}/user/ajax_call.php`;

  function updateStars(input) {
    const fs = input.closest('fieldset'); if (!fs) return;
    const val = parseInt(input.value) || 0;
    fs.querySelectorAll('input').forEach(i => {
      const v = parseInt(i.value), l = fs.querySelector(`label[for="${i.id}"]`);
      if (l) { const f = v >= 1 && v <= val; l.classList.toggle('star-filled', f); l.classList.toggle('star-empty', !f); }
    });
  }

  function setupFile(area, input) {
    let cont = area.querySelector('.img-container') || document.createElement('div');
    if (!cont.parentElement) { cont.className = 'img-container'; area.appendChild(cont); }
    if (!input.files?.length) return;
    area.classList.add('has-files');
    area.querySelectorAll('.upload-p, .file_button').forEach(el => el.style.display = 'none');
    cont.innerHTML = ''; cont.style.display = 'flex';
    Array.from(input.files).forEach((f, idx) => {
      const wrap = document.createElement('div'); wrap.className = 'img-preview-wrapper';
      const btn = document.createElement('button'); btn.className = 'img-remove-btn'; btn.innerHTML = '×';
      btn.onclick = e => { e.preventDefault(); wrap.remove(); if (!cont.querySelectorAll('.img-preview-wrapper').length) { area.classList.remove('has-files'); area.querySelectorAll('.upload-p, .file_button').forEach(el => el.style.display = ''); input.value = ''; } };
      if (f.type?.startsWith('image/')) {
        const r = new FileReader(); r.onload = e => { const img = document.createElement('img'); img.className = 'img-preview'; img.src = e.target.result; wrap.appendChild(img); wrap.appendChild(btn); }; r.readAsDataURL(f);
      } else {
        const icon = document.createElement('div'); icon.textContent = '📁'; icon.style.fontSize = '48px'; wrap.appendChild(icon); wrap.appendChild(btn);
      }
      cont.appendChild(wrap);
    });
  }

  function notify(msg, type) {
    const n = document.createElement('div'); n.className = 'form-notification';
    Object.assign(n.style, { position:'fixed', top:'20px', right:'20px', background:type==='success'?'#4caf50':'#f44336', color:'white', padding:'16px 24px', borderRadius:'4px', boxShadow:'0 4px 6px rgba(0,0,0,0.1)', zIndex:'10000', cursor:'pointer' });
    n.textContent = msg; document.body.appendChild(n);
    setTimeout(() => { n.style.animation = 'slideOut 0.3s ease-out'; setTimeout(() => n.remove(), 300); }, 3000);
    n.onclick = () => n.remove();
  }

  function applyDesign(container, id, s) {
    container.querySelectorAll(`[data-formdataid="${id}"]`).forEach(el => {
      const fs = s.fontSize ? parseInt(s.fontSize) + 'px' : '';
      if (fs) { el.style.fontSize = fs; el.querySelectorAll('input, textarea, select, .file_button').forEach(i => i.style.fontSize = fs); }
      if (s.fontWeight) el.style.fontWeight = s.fontWeight;
      if (s.color) { el.style.color = s.color; el.querySelectorAll('input, textarea, select, label, .upload-p').forEach(i => i.style.color = s.color); }
      if (s.borderRadius) { const br = parseInt(s.borderRadius) + 'px'; el.querySelectorAll('input, textarea, select, .upload-area, .file_button').forEach(i => i.style.borderRadius = br); }
      if (s.bgColor) el.querySelectorAll('input, textarea, select, .upload-area').forEach(i => i.style.backgroundColor = s.bgColor);
    });
  }

  function applyData(container, id, typeId, data) {
    const type = parseInt(typeId) || 0;
    container.querySelectorAll(`[data-formdataid="${id}"]`).forEach(el => {
      if ([11, 13].includes(type)) {
        const n = String(data[8] || '1');
        el.querySelectorAll('li.globo-list-control').forEach(li => {
          li.className = li.className.replace(/\boption-\d+-column\b/g, ''); li.classList.add(`option-${n}-column`);
        });
      }
      let idx = [11,13].includes(type)?9:(type==10?10:(type==15?6:([1,2,3,4,5,7,20,21,22,23].includes(type)?9:(type==9?12:-1))));
      if (idx >= 0 && data[idx] !== undefined) {
        el.className = el.className.replace(/\blayout-\d+-column\b/g, ''); el.classList.add(`layout-${data[idx]}-column`);
      }
    });
  }

  async function initForm(formId, shop, container) {
    const renderUrl = `/apps/easy-form-builder/render?form_id=${encodeURIComponent(formId)}&shop=${encodeURIComponent(shop)}`;
    const res = await fetch(renderUrl, { credentials: 'same-origin' });
    const html = await res.text();
    if (!html || !html.trim()) { container.style.display = 'none'; return; }
    container.innerHTML = html;
    
    container.querySelectorAll('.file_button').forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); const i = btn.closest('.upload-area')?.querySelector('input[type="file"]'); if (i) i.click(); }));
    container.addEventListener('change', e => {
      if (e.target.type === 'file' && e.target.dataset.type === 'file') setupFile(e.target.closest('.upload-area'), e.target);
      if (e.target.type === 'radio' && e.target.closest('.star-rating')) updateStars(e.target);
    }, true);

    container.querySelectorAll('.star-rating fieldset').forEach(fs => {
      Object.assign(fs.style, { display: 'flex', flexDirection: 'row-reverse', justifyContent: 'flex-end' });
      fs.querySelectorAll('label').forEach(l => {
        Object.assign(l.style, { width: '1.5em', margin: '0 2px', cursor: 'pointer', fontSize: '200%', color: 'transparent', display: 'block' });
        l.addEventListener('click', e => { const i = document.getElementById(l.getAttribute('for')); if (i) { i.checked = true; updateStars(i); i.dispatchEvent(new Event('change', { bubbles: true })); }});
      });
    });

    const form = container.querySelector('form');
    if (form) {
      form.addEventListener('submit', async e => {
        e.preventDefault(); const btn = form.querySelector('button[type="submit"], .action.submit');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; btn.dataset.orig = btn.innerHTML; btn.innerHTML = 'Submitting...'; }
        const fd = new FormData(form); fd.append('store', shop); fd.append('routine_name', 'addformdata'); fd.append('form_id', formId);
        try {
          const r = await fetch(ajaxUrl, { method: 'POST', body: fd }); const d = await r.json();
          if (d.result === 'success') { notify(d.msg || 'Success!', 'success'); form.reset(); } else notify(d.msg || 'Error', 'error');
        } catch(err) { notify('Submission failed', 'error'); }
        if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.innerHTML = btn.dataset.orig; }
      });
    }

    try {
      const designRes = await fetch(ajaxUrl, { method: 'POST', body: new URLSearchParams({ routine_name: 'get_form_design_settings', form_id: formId, store: shop }) });
      const designData = await designRes.json();
      if (designData.result === 'success') {
        const s = designData.settings || designData.data || {};
        Object.keys(s).forEach(k => { if (k.startsWith('element_')) applyDesign(container, k.replace('element_', ''), s[k]); });
        const ed = designData.element_data || {};
        Object.keys(ed).forEach(k => { if (k.startsWith('element_')) applyData(container, k.replace('element_', ''), ed[k].element_id, ed[k].data); });
      }
    } catch(e) {}
  }

  window.EFB = { init: initForm, notify: notify };
})();
