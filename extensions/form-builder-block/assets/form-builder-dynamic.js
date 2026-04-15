(function() {
  'use strict';
  const baseUrl = 'https://codelocksolutions.com/form_builder';

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
      const fs = s.fontSize ? (parseInt(s.fontSize) > 9 ? parseInt(s.fontSize) + 'px' : '') : '';
      if (fs) { 
        el.style.fontSize = fs; 
        el.querySelectorAll('input, textarea, select, label, .upload-p, .file_button').forEach(i => i.style.fontSize = fs); 
      }
      if (s.fontWeight) {
        el.style.fontWeight = s.fontWeight;
        el.querySelectorAll('label, .upload-p, .file_button').forEach(i => i.style.fontWeight = s.fontWeight);
      }
      if (s.color) { 
        el.style.color = s.color; 
        el.querySelectorAll('input, textarea, select, label, .upload-p').forEach(i => i.style.color = s.color); 
      }
      if (s.borderRadius) { 
        const br = parseInt(s.borderRadius) + 'px'; 
        el.querySelectorAll('input, textarea, select, .upload-area, .file_button').forEach(i => i.style.borderRadius = br); 
      }
      if (s.bgColor) {
        // Only apply background color to non-input elements or specific components
        el.querySelectorAll('.upload-area, .file_button').forEach(i => i.style.backgroundColor = s.bgColor);
      }
      if (s.textAlign) {
        el.style.textAlign = s.textAlign;
        el.querySelectorAll('.globo-form-input, label, .messages').forEach(i => i.style.textAlign = s.textAlign);
      }
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
    console.log('EFB: Starting initForm for ID:', formId);
    if (!container) { console.error('EFB: No container provided'); return; }
    
    const renderUrl = `/apps/easy-form-builder/render?form_id=${encodeURIComponent(formId)}&shop=${encodeURIComponent(shop)}&cb=${Date.now()}`;
    console.log('EFB: Fetching URL:', renderUrl);
    
    try {
      const res = await fetch(renderUrl, { credentials: 'same-origin' });
      const html = await res.text();
      if (!html || !html.trim()) { 
        console.warn('EFB: Empty HTML response');
        container.style.display = 'none'; return; 
      }
      
      container.innerHTML = html;
      console.log('EFB: HTML injected');
      
      // Setup file and star rating events normally
      container.querySelectorAll('.file_button').forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); const i = btn.closest('.upload-area')?.querySelector('input[type="file"]'); if (i) i.click(); }));
    } catch (e) {
      console.error('EFB: Init setup error', e);
    }
  }

  // GLOBAL FALLBACK HANDLER - Handles ANY form in ANY theme
  const globalSubmitHandler = async (e, forcedForm = null) => {
    const btn = e ? e.target.closest('.action.submit, button[type="submit"]') : null;
    const form = forcedForm || (btn ? btn.closest('form') : null);
    
    if (form && (form.classList.contains('get_selected_elements') || form.querySelector('[data-formdataid]'))) {
      if (e) { e.preventDefault(); e.stopPropagation(); }
      console.log('EFB: Global submit/click detected');
      
      if (form.reportValidity && !form.reportValidity()) { console.log('EFB: Validation failed'); return; }
      
      const subBtn = btn || form.querySelector('.action.submit, button[type="submit"]');
      if (subBtn && subBtn.disabled) return;
      
      if (subBtn) { 
        subBtn.disabled = true; subBtn.style.opacity = '0.6'; 
        subBtn.dataset.orig = subBtn.innerHTML; 
        subBtn.innerHTML = 'Submitting...'; 
      }
      
      const formDataId = form.querySelector('.form_id')?.value || form.getAttribute('data-form-id');
      const shopDomain = window.Shopify?.shop || new URLSearchParams(window.location.search).get('shop');
      
      const fd = new FormData(form);
      fd.append('routine_name', 'addformdata');
      if (!fd.has('store')) fd.append('store', shopDomain);
      if (!fd.has('form_id')) fd.append('form_id', formDataId);
      
      console.log('EFB: Sending POST...');
      try {
        const r = await fetch('/apps/easy-form-builder/ajax', { method:'POST', body:fd, credentials:'same-origin' });
        const d = await r.json();
        console.log('EFB: Result', d);
        if (d.result === 'success') { 
          notify(d.msg || 'Success!', 'success'); 
          form.reset(); 
          form.querySelectorAll('.img-container').forEach(c => c.innerHTML = '');
          form.querySelectorAll('.upload-area').forEach(a => { a.classList.remove('has-files'); a.querySelectorAll('.upload-p, .file_button').forEach(el => el.style.display = ''); });
        } else {
          notify(d.msg || 'Error', 'error');
        }
      } catch(err) { console.error('EFB: Err', err); notify('Failed', 'error'); }
      if (subBtn) { subBtn.disabled = false; subBtn.style.opacity = '1'; subBtn.innerHTML = subBtn.dataset.orig; }
    }
  }

  // Intercept all submits and clicks globally
  document.addEventListener('submit', globalSubmitHandler, true);
  document.addEventListener('click', e => {
    if (e.target.closest('.action.submit, button[type="submit"]')) globalSubmitHandler(e);
  }, true);

  // Initialize star rating handlers with proper event delegation
  function initializeStarRating() {
    document.querySelectorAll('.star-rating input[type="radio"][data-type="rating-star"]').forEach(input => {
      // Set up click handler for rating inputs
      input.addEventListener('change', function(e) {
        updateStars(this);
        this.closest('form')?.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
    
    // Also handle label clicks to ensure proper radio button checking
    document.querySelectorAll('.star-rating fieldset label').forEach(label => {
      label.addEventListener('click', function(e) {
        const radioId = this.getAttribute('for');
        if (radioId) {
          const radio = document.getElementById(radioId);
          if (radio && !radio.checked) {
            radio.checked = true;
            updateStars(radio);
            radio.closest('form')?.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }
      });
    });
  }

  // Initialize on document ready and on efb:loaded event
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeStarRating);
  } else {
    initializeStarRating();
  }
  document.addEventListener('efb:loaded', initializeStarRating);

  window.EFB = window.EFB || { init: initForm, notify: notify, initializeStarRating: initializeStarRating };
  document.dispatchEvent(new CustomEvent('efb:loaded'));
})();
