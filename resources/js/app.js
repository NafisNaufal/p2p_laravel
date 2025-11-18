// Simple JavaScript for P2P application
document.addEventListener('DOMContentLoaded', function () {
    console.log('P2P Communication App Loaded');

    // Add any basic JS functionality here
    const pinInputs = document.querySelectorAll('input[name="pin"]');
    pinInputs.forEach((input) => {
        input.addEventListener('input', function (e) {
            // Only allow numbers
            this.value = this.value.replace(/\D/g, '');

            // Limit to 4 digits
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });
    });
});
