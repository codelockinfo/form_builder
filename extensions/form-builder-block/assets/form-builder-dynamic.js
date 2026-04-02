(function() {
  'use strict';
  
  function updateStarRatingDisplay(checkedInput) {
    const fieldset = checkedInput.closest('fieldset');
    if (!fieldset) return;
    const checkedValue = parseInt(checkedInput.value || checkedInput.getAttribute('value')) || 0;
    if (checkedValue <= 0 || checkedValue > 5) return;
    
    const allInputs = Array.from(fieldset.querySelectorAll('input[type="radio"]'));
    allInputs.forEach(function(input) {
      const val = parseInt(input.value || input.getAttribute('value')) || 0;
      const labelId = input.getAttribute('id');
      if (!labelId) return;
      let label = fieldset.querySelector('label[for="' + labelId + '"]') || input.nextElementSibling;
      if (!label || label.tagName !== 'LABEL') return;
      
      if (val >= 1 && val <= checkedValue) {
        label.classList.add('star-filled');
        label.classList.remove('star-empty');
        label.setAttribute('data-star-state', 'filled');
      } else {
        label.classList.add('star-empty');
        label.classList.remove('star-filled');
        label.setAttribute('data-star-state', 'empty');
      }
    });
  }

  function applyFormElementStyles(container) {
    if (!container) return;
    container.querySelectorAll('input[type="file"][data-type="file"]').forEach(input => {
      const area = input.closest('.upload-area');
      if (area) {
        input.style.cssText = 'position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; z-index: 10;';
      }
    });
    
    const positionStars = () => {
      container.querySelectorAll('.star-rating fieldset input[type="radio"]').forEach(input => {
        const label = input.closest('fieldset').querySelector('label[for="' + input.id + '"]');
        if (label) {
          input.style.cssText = `position: absolute; opacity: 0; width: ${label.offsetWidth}px; height: ${label.offsetHeight}px; z-index: 10; top: ${label.offsetTop}px; left: ${label.offsetLeft}px;`;
        }
        if (!input.dataset.handler) {
          input.dataset.handler = 'true';
          input.addEventListener('change', () => updateStarRatingDisplay(input));
        }
      });
    };
    positionStars();
    setTimeout(positionStars, 200);
  }

  function initializeFileUploadHandlers(container) {
    if (!container || container.dataset.fileInit) return;
    container.dataset.fileInit = 'true';
    
    container.addEventListener('change', e => {
      if (e.target.type === 'file' && e.target.dataset.type === 'file') {
        const input = e.target;
        const area = input.closest('.upload-area');
        let imgContainer = area.querySelector('.img-container');
        if (!imgContainer) {
          imgContainer = document.createElement('div');
          imgContainer.className = 'img-container';
          area.appendChild(imgContainer);
        }
        
        if (input.files.length > 0) {
          area.classList.add('has-files');
          imgContainer.innerHTML = '';
          imgContainer.style.display = 'flex';
          Array.from(input.files).forEach((file, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'img-preview-wrapper';
            const name = document.createElement('p');
            name.textContent = file.name;
            wrapper.appendChild(name);
            
            if (file.type.startsWith('image/')) {
              const reader = new FileReader();
              reader.onload = ev => {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'img-preview';
                wrapper.insertBefore(img, name);
              };
              reader.readAsDataURL(file);
            }
            imgContainer.appendChild(wrapper);
          });
        }
      }
    });
  }

  async function fetchAndApplyDesignSettings(formId, shop, container) {
    const scripts = document.querySelectorAll('script[src]');
    let baseUrl = 'https://codelocksolutions.com/form_builder';
    for (let s of scripts) {
      if (s.src.includes('/form_builder/')) {
        baseUrl = s.src.substring(0, s.src.indexOf('/form_builder/') + 13);
        break;
      }
    }
    
    const formData = new FormData();
    formData.append('routine_name', 'get_form_design_settings');
    formData.append('form_id', formId);
    formData.append('store', shop);
    
    try {
      const res = await fetch(baseUrl + '/user/ajax_call.php', { method: 'POST', body: formData });
      if (!res.ok) return;
      const result = await res.json();
      if (result.result === 'success') {
        // Simple application logic...
      }
    } catch(e) {}
  }

  function showNotification(message, type = 'success') {
    const note = document.createElement('div');
    note.className = 'form-notification';
    note.style.cssText = `position:fixed; top:20px; right:20px; background:${type==='success'?'#4caf50':'#f44336'}; color:white; padding:16px 24px; z-index:10000;`;
    note.textContent = message;
    document.body.appendChild(note);
    setTimeout(() => note.remove(), 3000);
  }

  async function initializeFormBuilder() {
    const containers = document.querySelectorAll('.form-builder-container');
    containers.forEach(async container => {
      if (container.dataset.initialized) return;
      container.dataset.initialized = 'true';
      
      const config = container.dataset;
      const formId = config.formId;
      const shop = config.shopPermanent || config.shopMyshopify || config.shopDomain;
      
      if (!formId || formId === '0') {
        container.innerHTML = '<div style="color:red">Form ID Required</div>';
        return;
      }
      
      try {
        const res = await fetch(`/apps/easy-form-builder/render?form_id=${formId}&shop=${shop}`);
        if (!res.ok) throw new Error('Load failed');
        let html = await res.text();
        if (!html.trim()) { container.style.display = 'none'; return; }
        
        container.innerHTML = html;
        
        // Overrides
        const h = container.querySelector('.globo-heading');
        if (h && config.headingText) h.textContent = config.headingText;
        if (h && config.customHeading) h.innerHTML = config.customHeading;
        
        const d = container.querySelector('.globo-description');
        if (d && config.descriptionText) d.innerHTML = config.descriptionText;
        if (d && config.customDescription) d.innerHTML = config.customDescription;
        
        const b = container.querySelector('button[type="submit"]');
        if (b && config.customButtonLabel) (b.querySelector('span') || b).innerHTML = config.customButtonLabel;
        
        applyFormElementStyles(container);
        initializeFileUploadHandlers(container);
        fetchAndApplyDesignSettings(formId, shop, container);
      } catch(e) {
        container.innerHTML = '<div style="color:red">Error loading form</div>';
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFormBuilder);
  } else {
    initializeFormBuilder();
  }
  setTimeout(initializeFormBuilder, 100);
  if (typeof window.Shopify !== 'undefined' && window.Shopify.designMode) {
    document.addEventListener('shopify:section:load', initializeFormBuilder);
    document.addEventListener('shopify:block:select', initializeFormBuilder);
  }
})();
