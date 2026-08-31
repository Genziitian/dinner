/**
 * Live Camera QR Code Scanner & Receipt Lookup Engine
 * Supports Native BarcodeDetector API + Camera Stream + Fallback
 */
(function() {
  'use strict';

  let videoStream = null;
  let animationFrameId = null;
  let barcodeDetector = null;
  let currentFacingMode = 'environment';
  let isScanning = false;

  // Initialize BarcodeDetector if available
  if ('BarcodeDetector' in window) {
    try {
      barcodeDetector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'code_39', 'ean_13', 'upc_a'] });
    } catch (e) {
      console.warn('Native BarcodeDetector initialization note:', e);
      barcodeDetector = null;
    }
  }

  // Audio beep on successful scan
  function playBeep() {
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
      gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start();
      osc.stop(audioCtx.currentTime + 0.15);
      if (navigator.vibrate) navigator.vibrate(100);
    } catch (e) {
      // AudioContext not allowed or unsupported
    }
  }

  async function startCameraScanner() {
    const video = document.getElementById('qrCameraVideo');
    const loadingNotice = document.getElementById('qrScannerLoading');
    const errorNotice = document.getElementById('qrScannerError');
    const scannerOverlay = document.getElementById('qrScannerOverlay');

    if (!video) return;

    if (errorNotice) errorNotice.style.display = 'none';
    if (loadingNotice) loadingNotice.style.display = 'flex';
    if (scannerOverlay) scannerOverlay.style.display = 'none';

    stopCameraScanner();

    try {
      const constraints = {
        video: {
          facingMode: currentFacingMode,
          width: { ideal: 1280 },
          height: { ideal: 720 }
        },
        audio: false
      };

      videoStream = await navigator.mediaDevices.getUserMedia(constraints);
      video.srcObject = videoStream;
      await video.play();

      if (loadingNotice) loadingNotice.style.display = 'none';
      if (scannerOverlay) scannerOverlay.style.display = 'block';
      isScanning = true;

      scanVideoLoop();
    } catch (err) {
      console.error('Camera access error:', err);
      if (loadingNotice) loadingNotice.style.display = 'none';
      if (errorNotice) {
        errorNotice.style.display = 'block';
        errorNotice.innerHTML = `
          <div class="text-danger fw-bold mb-1">📷 Camera Access Required</div>
          <div class="text-secondary small">Please allow camera permissions or enter the order number manually below.</div>
        `;
      }
    }
  }

  function stopCameraScanner() {
    isScanning = false;
    if (animationFrameId) {
      cancelAnimationFrame(animationFrameId);
      animationFrameId = null;
    }
    if (videoStream) {
      videoStream.getTracks().forEach(track => track.stop());
      videoStream = null;
    }
  }

  async function scanVideoLoop() {
    if (!isScanning) return;

    const video = document.getElementById('qrCameraVideo');
    if (!video || video.readyState < 2) {
      animationFrameId = requestAnimationFrame(scanVideoLoop);
      return;
    }

    if (barcodeDetector) {
      try {
        const barcodes = await barcodeDetector.detect(video);
        if (barcodes && barcodes.length > 0) {
          const rawValue = barcodes[0].rawValue;
          if (rawValue) {
            handleScanSuccess(rawValue);
            return;
          }
        }
      } catch (e) {
        // detection frame error
      }
    }

    animationFrameId = requestAnimationFrame(scanVideoLoop);
  }

  function handleScanSuccess(scannedData) {
    playBeep();
    stopCameraScanner();

    const statusEl = document.getElementById('qrScannerStatus');
    if (statusEl) {
      statusEl.innerHTML = `<span class="badge bg-success p-2">✓ QR Code Detected! Redirecting...</span>`;
    }

    executeLookup(scannedData);
  }

  function executeLookup(val) {
    val = String(val || '').trim();
    if (!val) return;

    // Check if full URL
    if (val.includes('/receipt/')) {
      const match = val.match(/receipt\/([a-zA-Z0-9_\-]+)/);
      if (match) {
        window.location.href = '/receipt/view?token=' + encodeURIComponent(match[1]);
        return;
      }
    }

    // Check if token (> 20 alphanumeric chars)
    if (val.length > 20 && /^[a-zA-Z0-9_\-]+$/.test(val)) {
      window.location.href = '/receipt/view?token=' + encodeURIComponent(val);
      return;
    }

    // Check if Order Number (e.g. 7 or #7 or Order #7)
    const cleanNum = val.replace(/[^0-9]/g, '');
    if (cleanNum) {
      window.location.href = '/receipt/view?order_number=' + encodeURIComponent(cleanNum);
      return;
    }

    // General fallback
    window.location.href = '/receipt/view?search=' + encodeURIComponent(val);
  }

  // Global scanner controller object
  window.DineQrScanner = {
    openModal: function() {
      const modalEl = document.getElementById('cameraQrLookupModal');
      if (!modalEl) return;

      const modal = new bootstrap.Modal(modalEl);
      modal.show();

      modalEl.addEventListener('shown.bs.modal', function onShown() {
        modalEl.removeEventListener('shown.bs.modal', onShown);
        startCameraScanner();
      });

      modalEl.addEventListener('hidden.bs.modal', function onHidden() {
        modalEl.removeEventListener('hidden.bs.modal', onHidden);
        stopCameraScanner();
      });
    },

    toggleCamera: function() {
      currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
      startCameraScanner();
    },

    submitManual: function() {
      const input = document.getElementById('manualQrInput');
      if (input && input.value.trim()) {
        executeLookup(input.value.trim());
      }
    }
  };

  // Backwards compatibility alias
  window.openQrLookupModal = function() {
    window.DineQrScanner.openModal();
  };

  window.lookupReceipt = function() {
    window.DineQrScanner.submitManual();
  };
})();
