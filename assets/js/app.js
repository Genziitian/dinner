/**
 * DinePOS Core Application JavaScript & PWA Registration
 */
(function() {
  'use strict';

  // 1. Register Service Worker for PWA
  if ('serviceWorker' in navigator && window.location.protocol.startsWith('http')) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js')
        .then((reg) => {
          console.log('DinePOS ServiceWorker registered with scope:', reg.scope);
        })
        .catch((err) => {
          console.log('DinePOS ServiceWorker registration skipped/failed:', err);
        });
    });
  }

  // 2. Auto-dismiss alerts after 5 seconds
  document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert-dismissible, .alert');
    alerts.forEach((alert) => {
      setTimeout(() => {
        alert.style.transition = 'opacity 0.4s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 400);
      }, 5000);
    });

    // 3. Confirm prompts on delete actions
    document.querySelectorAll('[data-confirm]').forEach((el) => {
      el.addEventListener('click', (e) => {
        const msg = el.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
        if (!confirm(msg)) {
          e.preventDefault();
        }
      });
    });
  });

  // Global helper to get CSRF token
  window.getCSRFToken = function() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  };
})();
