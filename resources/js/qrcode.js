import { Html5Qrcode } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', () => {

    const barcodeInput =
        document.getElementById('barcode');

    const productNameInput =
        document.getElementById('name');

    const scannerModalElement =
        document.getElementById(
            'barcodeScannerModal'
        );

    if (
        !barcodeInput ||
        !scannerModalElement
    ) {
        return;
    }

    let html5QrCode = null;
    let scannerRunning = false;

    barcodeInput.addEventListener(
        'keydown',
        function(e) {

            if (e.key === 'Enter') {

                e.preventDefault();

                productNameInput?.focus();

            }

        }
    );

    scannerModalElement.addEventListener(
        'shown.bs.modal',
        async function() {

            if (scannerRunning) {
                return;
            }

            try {

                html5QrCode =
                    new Html5Qrcode(
                        'reader'
                    );

                await html5QrCode.start(
                    {
                        facingMode:
                            'environment'
                    },
                    {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    async function(
                        decodedText
                    ) {

                        barcodeInput.value =
                            decodedText;

                        if (
                            html5QrCode &&
                            scannerRunning
                        ) {

                            await html5QrCode.stop();

                            scannerRunning =
                                false;

                        }

                        bootstrap.Modal
                            .getInstance(
                                scannerModalElement
                            )
                            ?.hide();

                        productNameInput?.focus();

                    }
                );

                scannerRunning = true;

            } catch (error) {

                console.error(
                    'Scanner Error:',
                    error
                );

            }

        }
    );

    scannerModalElement.addEventListener(
        'hidden.bs.modal',
        async function() {

            if (
                !html5QrCode ||
                !scannerRunning
            ) {
                return;
            }

            try {

                await html5QrCode.stop();

                await html5QrCode.clear();

            } catch (error) {

                console.error(error);

            }

            scannerRunning = false;

        }
    );

});
