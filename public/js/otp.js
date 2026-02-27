document.addEventListener('DOMContentLoaded', () => {
    const resendBtn = document.getElementById('resend-otp');
    const statusText = document.getElementById('resend-status');
    const otpInput = document.getElementById('otp');

    if (otpInput) {
        // Only allow numbers
        otpInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    if (resendBtn) {
        let cooldown = 60; // 60 seconds cooldown
        let timer = null;

        const startCooldown = () => {
            resendBtn.disabled = true;
            timer = setInterval(() => {
                cooldown--;
                resendBtn.textContent = `Resend available in ${cooldown}s`;

                if (cooldown <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend OTP';
                    cooldown = 60;
                }
            }, 1000);
        };

        resendBtn.addEventListener('click', async () => {
            if (resendBtn.disabled) return;

            statusText.textContent = 'Sending...';
            statusText.style.color = 'var(--text-muted)';

            try {
                const response = await fetch('index.php?route=api/otp/resend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 429) {
                    throw new Error('Too many requests. Please wait.');
                }

                const data = await response.json();

                if (data.success) {
                    statusText.textContent = 'OTP Resent Successfully to your email!';
                    statusText.style.color = 'var(--success)';
                    // Trigger visual cue
                    statusText.classList.remove('fade-in');
                    void statusText.offsetWidth; // trigger reflow
                    statusText.classList.add('fade-in');

                    startCooldown();
                } else {
                    throw new Error(data.error || 'Failed to resend OTP');
                }
            } catch (error) {
                statusText.textContent = error.message;
                statusText.style.color = 'var(--danger)';
            }
        });
    }
});