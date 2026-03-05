{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2/qz-tray.js"></script>
<script>
    // QZ Tray: Override certificate and signature for development
    // In production, replace with actual certificate from https://qz.io/docs/
    qz.security.setCertificatePromise(function (resolve, reject) {
        resolve('');
    });
    qz.security.setSignatureAlgorithm('SHA512');
    qz.security.setSignaturePromise(function (toSign) {
        return function (resolve, reject) {
            resolve('');
        };
    });

    // QZ Tray connection state
    let qzConnected = false;

    async function connectQzTray() {
        if (qzConnected) return true;
        try {
            if (!qz.websocket.isActive()) {
                await qz.websocket.connect();
            }
            qzConnected = true;
            return true;
        } catch (err) {
            console.warn('QZ Tray tidak tersedia:', err.message);
            qzConnected = false;
            return false;
        }
    }

    async function printViaQzTray(base64Data, printerName) {
        const connected = await connectQzTray();
        if (!connected) {
            // Fallback: open HTML receipt in new window
            return false;
        }

        try {
            const config = qz.configs.create(printerName || null);
            const data = [{ type: 'raw', format: 'base64', data: base64Data }];
            await qz.print(config, data);
            return true;
        } catch (err) {
            console.error('Gagal cetak via QZ Tray:', err);
            return false;
        }
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('clear-search-input', () => {
            const input = document.getElementById('search-input');
            if (input) input.value = '';
        });

        Livewire.on('printReceipt', async (data) => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const response = await fetch('/pos/receipt/' + data.orderId + '/print', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const result = await response.json();

                if (!result.success) {
                    console.error('Print error:', result.message);
                    // Fallback: open HTML receipt
                    window.open('/pos/receipt/' + data.orderId, '_blank');
                    return;
                }

                if (result.raw) {
                    // Scenario B: Cloud mode — send raw data to QZ Tray
                    const printed = await printViaQzTray(result.raw);
                    if (!printed) {
                        // QZ Tray not available — fallback to browser print
                        let iframe = document.getElementById('receipt-frame');
                        if (!iframe) {
                            iframe = document.createElement('iframe');
                            iframe.id = 'receipt-frame';
                            iframe.style.position = 'absolute';
                            iframe.style.width = '0px';
                            iframe.style.height = '0px';
                            iframe.style.border = 'none';
                            document.body.appendChild(iframe);
                        }
                        iframe.src = '/pos/receipt/' + data.orderId;
                    }
                }
                // Scenario A: Local mode — server already printed, nothing to do
            } catch (err) {
                console.error('Print request failed:', err);
                // Ultimate fallback
                let iframe = document.getElementById('receipt-frame');
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    iframe.id = 'receipt-frame';
                    iframe.style.position = 'absolute';
                    iframe.style.width = '0px';
                    iframe.style.height = '0px';
                    iframe.style.border = 'none';
                    document.body.appendChild(iframe);
                }
                iframe.src = '/pos/receipt/' + data.orderId;
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') { e.preventDefault(); document.getElementById('search-input').focus(); }
            if (e.key === 'F9') { e.preventDefault(); @this.call('openCheckout'); }
            if (e.key === 'F4') { e.preventDefault(); @this.call('openDiscountModal'); }
            if (e.key === 'F10') { e.preventDefault(); @this.call('toggleTax'); }
            if (e.altKey && e.key === 'Delete') { e.preventDefault(); @this.call('clearCart'); }
        });
    });

    // Auto-connect QZ Tray on page load
    connectQzTray();
</script>