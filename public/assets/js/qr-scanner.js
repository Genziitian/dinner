/**
 * Live Camera QR Code Scanner & Receipt Lookup Engine
 * Powered by jsQR engine + BarcodeDetector fallback + Native Camera Stream
 */
(function() {
  'use strict';

  let videoStream = null;
  let animationFrameId = null;
  let barcodeDetector = null;
  let currentFacingMode = 'environment';
  let isScanning = false;
  let scanCanvas = null;
  let scanCtx = null;
  let lastScanTime = 0;

  // Initialize BarcodeDetector if available
  if (typeof window !== 'undefined' && 'BarcodeDetector' in window) {
    try {
      barcodeDetector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'code_39', 'ean_13', 'upc_a'] });
    } catch (e) {
      barcodeDetector = null;
    }
  }

  function getScanContext() {
    if (!scanCanvas) {
      scanCanvas = document.createElement('canvas');
      scanCtx = scanCanvas.getContext('2d', { willReadFrequently: true });
    }
    return { canvas: scanCanvas, ctx: scanCtx };
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
        osc.frequency.setValueAtTime(950, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.35, audioCtx.currentTime);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.18);
      }
      if (navigator.vibrate) navigator.vibrate([80, 50, 80]);
    } catch (e) {
      // Audio notification not critical
    }
  }

  async function startCameraScanner() {
    const video = document.getElementById('qrCameraVideo');
    const loadingNotice = document.getElementById('qrScannerLoading');
    const errorNotice = document.getElementById('qrScannerError');
    const scannerOverlay = document.getElementById('qrScannerOverlay');
    const statusEl = document.getElementById('qrScannerStatus');

    if (!video) return;

    if (errorNotice) errorNotice.style.display = 'none';
    if (loadingNotice) loadingNotice.style.display = 'flex';
    if (scannerOverlay) scannerOverlay.style.display = 'none';
    if (statusEl) statusEl.innerHTML = `<small class="text-secondary">Point camera directly at the receipt QR code</small>`;

    stopCameraScanner();

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      if (loadingNotice) loadingNotice.style.display = 'none';
      if (errorNotice) {
        errorNotice.style.display = 'block';
        errorNotice.innerHTML = `
          <div class="fw-bold mb-1" style="color: #ea580c;">📷 Camera Stream Unavailable</div>
          <div class="text-secondary small mb-2">Use the photo button or enter Order # below.</div>
          <button type="button" class="btn btn-sm text-white fw-bold px-3 py-1.5" style="background: var(--brand-orange); border-radius: 10px;" onclick="DineQrScanner.triggerPhotoCapture()">
            📸 Take Photo of QR
          </button>
        `;
      }
      return;
    }

    const constraintOptions = [
      { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false },
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
        errorNotice.style.display = 'flex';
        errorNotice.style.flexDirection = 'column';
        errorNotice.style.alignItems = 'center';
        errorNotice.style.justifyContent = 'center';
        errorNotice.innerHTML = `
          <div class="fw-bold mb-1" style="color: #ea580c; font-size: 1rem;">📷 Camera Permission Needed</div>
          <div class="text-secondary small mb-3 text-center" style="max-width: 260px;">Allow camera access in your browser prompt or snap a receipt photo:</div>
          <div class="d-flex flex-column gap-2 w-100" style="max-width: 220px;">
            <button type="button" class="btn btn-sm text-white fw-bold py-2" style="background: var(--brand-orange); border-radius: 10px;" onclick="DineQrScanner.startScanner()">
              📷 Allow Camera Access
            </button>
            <button type="button" class="btn btn-sm btn-light border fw-bold py-2" style="border-radius: 10px;" onclick="DineQrScanner.triggerPhotoCapture()">
              📸 Take Photo of QR
            </button>
          </div>
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
          <div class="text-secondary small mb-2">Tap below to snap a photo of the receipt QR:</div>
          <button type="button" class="btn btn-sm text-white fw-bold px-3 py-1.5" style="background: var(--brand-orange); border-radius: 10px;" onclick="DineQrScanner.triggerPhotoCapture()">
            📸 Take Photo of QR
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
    if (!video || video.readyState < 2 || video.videoWidth === 0) {
      animationFrameId = requestAnimationFrame(scanVideoLoop);
      return;
    }

    const now = performance.now();
    // Throttle decoding to every ~40ms (~25 checks/sec) for optimal CPU performance
    if (now - lastScanTime > 40) {
      lastScanTime = now;

      try {
        const { canvas, ctx } = getScanContext();
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

        // 1. Primary decoder: jsQR (Instant software decode)
        if (typeof window.jsQR === 'function') {
          const qrCode = window.jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth'
          });

          if (qrCode && qrCode.data) {
            handleScanSuccess(qrCode.data);
            return;
          }
        }

        // 2. Secondary fallback: BarcodeDetector API
        if (barcodeDetector) {
          const barcodes = await barcodeDetector.detect(video);
          if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
            handleScanSuccess(barcodes[0].rawValue);
            return;
          }
        }
      } catch (e) {
        // scan frame error ignore
      }
    }

    animationFrameId = requestAnimationFrame(scanVideoLoop);
  }

  function handleScanSuccess(scannedData) {
    if (!isScanning) return;
    isScanning = false;
    playBeep();
    stopCameraScanner();

    const statusEl = document.getElementById('qrScannerStatus');
    if (statusEl) {
      statusEl.innerHTML = `<span class="badge bg-success p-2 fs-6">✓ Scanned Successfully! Opening...</span>`;
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

      setTimeout(function() {
        startCameraScanner();
      }, 150);

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
        statusEl.innerHTML = `<span class="spinner-border spinner-border-sm text-warning" role="status"></span> Decoding photo...`;
      }

      try {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        await img.decode();

        const { canvas, ctx } = getScanContext();
        canvas.width = img.naturalWidth || img.width;
        canvas.height = img.naturalHeight || img.height;
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

        // 1. jsQR software decode
        if (typeof window.jsQR === 'function') {
          const qrCode = window.jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth'
          });

          if (qrCode && qrCode.data) {
            handleScanSuccess(qrCode.data);
            return;
          }
        }

        // 2. BarcodeDetector fallback
        if (barcodeDetector) {
          const barcodes = await barcodeDetector.detect(img);
          if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
            handleScanSuccess(barcodes[0].rawValue);
            return;
          }
        }

        if (statusEl) {
          statusEl.innerHTML = `<span class="text-danger small">No QR code found in photo. Please enter Order # below.</span>`;
        }
      } catch (err) {
        console.error('File scan error:', err);
        if (statusEl) {
          statusEl.innerHTML = `<span class="text-danger small">Could not read image. Please enter Order # below.</span>`;
        }
      }
    },

    startScanner: function() {
      startCameraScanner();
    },

    submitManual: function() {
      const input = document.getElementById('manualQrInput');
      if (input && input.value.trim()) {
        executeLookup(input.value.trim());
      }
    }
  };

  // Backwards compatibility aliases
  window.openQrLookupModal = function() {
    window.DineQrScanner.openModal();
  };

  window.lookupReceipt = function() {
    window.DineQrScanner.submitManual();
  };
})();
