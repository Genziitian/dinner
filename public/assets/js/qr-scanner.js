/**
 * Live Camera QR Code Scanner & Receipt Lookup Engine
 * Multi-layer Scanner: Live WebRTC Camera Stream + BarcodeDetector + Native Photo Snap + Manual Lookup
 */
(function() {
  'use strict';

  let videoStream = null;
  let animationFrameId = null;
  let barcodeDetector = null;
  let currentFacingMode = 'environment';
  let isScanning = false;

  // Initialize BarcodeDetector if available
  if (typeof window !== 'undefined' && 'BarcodeDetector' in window) {
    try {
      barcodeDetector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'code_39', 'ean_13', 'upc_a'] });
    } catch (e) {
      console.warn('Native BarcodeDetector note:', e);
      barcodeDetector = null;
    }
  }

  // Audio beep on successful scan
  function playBeep() {
    try {
      const AudioContextClass = window.AudioContext || window.webkitAudioContext;
      if (AudioContextClass) {
        const audioCtx = new AudioContextClass();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
      }
      if (navigator.vibrate) navigator.vibrate(120);
    } catch (e) {
      // Audio notification not critical
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

    // Check if mediaDevices supported
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      if (loadingNotice) loadingNotice.style.display = 'none';
      if (errorNotice) {
        errorNotice.style.display = 'block';
        errorNotice.innerHTML = `
          <div class="fw-bold mb-1" style="color: #ea580c;">📷 Live Stream Restricted</div>
          <div class="text-secondary small mb-2">Use the Camera Snapshot button or enter Order # below.</div>
          <button type="button" class="btn btn-sm text-white fw-bold px-3 py-1.5" style="background: var(--brand-orange); border-radius: 10px;" onclick="DineQrScanner.triggerPhotoCapture()">
            📸 Take Photo with Camera
          </button>
        `;
      }
      return;
    }

    // Try fallback constraint options for widest mobile device compatibility
    const constraintOptions = [
      { video: { facingMode: { ideal: 'environment' } }, audio: false },
      { video: { facingMode: 'environment' }, audio: false },
      { video: true, audio: false }
    ];

    let stream = null;
    for (const constraints of constraintOptions) {
      try {
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        if (stream) break;
      } catch (err) {
        console.warn('Constraint attempt failed, trying next:', err);
      }
    }

    if (!stream) {
      if (loadingNotice) loadingNotice.style.display = 'none';
      if (errorNotice) {
        errorNotice.style.display = 'block';
        errorNotice.innerHTML = `
          <div class="fw-bold mb-1" style="color: #ef4444;">📷 Camera Access Denied / Unavailable</div>
          <div class="text-secondary small mb-2">Allow camera permissions or tap below to snap receipt photo:</div>
          <button type="button" class="btn btn-sm text-white fw-bold px-3 py-1.5" style="background: var(--brand-orange); border-radius: 10px;" onclick="DineQrScanner.triggerPhotoCapture()">
            📸 Take Photo with Camera
          </button>
        `;
      }
      return;
    }

    try {
      videoStream = stream;
      video.srcObject = videoStream;
      video.setAttribute('autoplay', '');
      video.setAttribute('muted', '');
      video.setAttribute('playsinline', '');
      video.setAttribute('webkit-playsinline', '');
      await video.play();

      if (loadingNotice) loadingNotice.style.display = 'none';
      if (scannerOverlay) scannerOverlay.style.display = 'block';
      isScanning = true;

      scanVideoLoop();
    } catch (playErr) {
      console.error('Video play error:', playErr);
      if (loadingNotice) loadingNotice.style.display = 'none';
      if (errorNotice) {
        errorNotice.style.display = 'block';
        errorNotice.innerHTML = `
          <div class="text-secondary small mb-2">Tap below to snap a photo of the receipt:</div>
          <button type="button" class="btn btn-sm text-white fw-bold px-3 py-1.5" style="background: var(--brand-orange); border-radius: 10px;" onclick="DineQrScanner.triggerPhotoCapture()">
            📸 Take Photo with Camera
          </button>
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
      videoStream.getTracks().forEach(track => {
        try { track.stop(); } catch(e) {}
      });
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
        // frame decode error
      }
    }

    animationFrameId = requestAnimationFrame(scanVideoLoop);
  }

  function handleScanSuccess(scannedData) {
    playBeep();
    stopCameraScanner();

    const statusEl = document.getElementById('qrScannerStatus');
    if (statusEl) {
      statusEl.innerHTML = `<span class="badge bg-success p-2 fs-6">✓ Scanned! Opening receipt...</span>`;
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

    // Check if Order Number (e.g. 7, #7, Order #7)
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

      // Start camera immediately on open
      setTimeout(function() {
        startCameraScanner();
      }, 200);

      modalEl.addEventListener('hidden.bs.modal', function onHidden() {
        modalEl.removeEventListener('hidden.bs.modal', onHidden);
        stopCameraScanner();
      });
    },

    toggleCamera: function() {
      currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
      startCameraScanner();
    },

    triggerPhotoCapture: function() {
      const input = document.getElementById('qrCameraFileInput');
      if (input) input.click();
    },

    handleFileInput: async function(inputEl) {
      const file = inputEl.files && inputEl.files[0];
      if (!file) return;

      const statusEl = document.getElementById('qrScannerStatus');
      if (statusEl) {
        statusEl.innerHTML = `<span class="spinner-border spinner-border-sm text-warning" role="status"></span> Analyzing photo...`;
      }

      try {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        await img.decode();

        if (barcodeDetector) {
          const barcodes = await barcodeDetector.detect(img);
          if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
            handleScanSuccess(barcodes[0].rawValue);
            return;
          }
        }

        if (statusEl) {
          statusEl.innerHTML = `<span class="text-danger small">No QR code found in photo. Please try entering the Order # below.</span>`;
        }
      } catch (err) {
        console.error('File scan error:', err);
        if (statusEl) {
          statusEl.innerHTML = `<span class="text-danger small">Could not read image. Please enter Order # below.</span>`;
        }
      }
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
