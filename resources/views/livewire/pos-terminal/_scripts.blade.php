{{-- Scripts --}}
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('clear-search-input', () => {
            const input = document.getElementById('search-input');
            if (input) input.value = '';
        });

        Livewire.on('printReceipt', (data) => {
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
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') { e.preventDefault(); document.getElementById('search-input').focus(); }
            if (e.key === 'F9') { e.preventDefault(); @this.call('openCheckout'); }
            if (e.key === 'F4') { e.preventDefault(); @this.call('openDiscountModal'); }
            if (e.key === 'F10') { e.preventDefault(); @this.call('toggleTax'); }
            if (e.altKey && e.key === 'Delete') { e.preventDefault(); @this.call('clearCart'); }
        });
    });
</script>