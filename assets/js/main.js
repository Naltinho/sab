document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleAdmin = document.getElementById('theme-toggle-admin');
    const body = document.body;

    function toggleTheme() {
        body.classList.toggle('dark-theme');
        const icon = themeToggle?.querySelector('i') || themeToggleAdmin?.querySelector('i');
        if (icon) {
            if (body.classList.contains('dark-theme')) {
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        }
    }

    themeToggle?.addEventListener('click', toggleTheme);
    themeToggleAdmin?.addEventListener('click', toggleTheme);

    // Auth Tabs
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            forms.forEach(form => {
                form.classList.remove('active');
                if (form.id === `${target}-form`) {
                    form.classList.add('active');
                }
            });
        });
    });

    // Helper functions for WebAuthn
    function base64UrlDecode(str) {
        str = str.replace(/-/g, '+').replace(/_/g, '/');
        while (str.length % 4) {
            str += '=';
        }
        const binary = atob(str);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes.buffer;
    }

    function base64UrlEncode(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary).replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
    }

    // Biometric Authentication
    const scannerBtn = document.getElementById('scanner-btn');
    const simularBtn = document.getElementById('simular-biometria');
    const scannerStatus = document.getElementById('scanner-status');
    const scannerIcon = scannerBtn?.querySelector('i');

    let isScanning = false;

    async function startBiometricScan() {
        if (isScanning) return;
        
        isScanning = true;
        scannerBtn?.classList.add('scanning');
        if (scannerStatus) scannerStatus.innerText = 'Escaneando impressão digital...';
        if (scannerIcon) scannerIcon.style.color = 'var(--secondary-blue)';

        try {
            const response = await fetch('api/authenticate.php');
            const options = await response.json();
            
            // Convert to ArrayBuffer
            options.challenge = base64UrlDecode(options.challenge);
            if (options.allowCredentials) {
                options.allowCredentials = options.allowCredentials.map(cred => ({
                    ...cred,
                    id: base64UrlDecode(cred.id)
                }));
            }

            const credential = await navigator.credentials.get({ publicKey: options });
            
            // Prepare for server
            const credentialForServer = {
                id: credential.id,
                rawId: base64UrlEncode(credential.rawId),
                type: credential.type,
                response: {
                    authenticatorData: base64UrlEncode(credential.response.authenticatorData),
                    clientDataJSON: base64UrlEncode(credential.response.clientDataJSON),
                    signature: base64UrlEncode(credential.response.signature),
                    userHandle: credential.response.userHandle ? base64UrlEncode(credential.response.userHandle) : null
                }
            };

            const verifyResponse = await fetch('api/authenticate_verify.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: credentialForServer })
            });

            const result = await verifyResponse.json();

            if (result.success) {
                if (scannerStatus) scannerStatus.innerText = 'Biometria reconhecida!';
                if (scannerIcon) scannerIcon.style.color = 'var(--success)';
                
                setTimeout(() => {
                    window.location.href = 'pages/welcome.php';
                }, 1000);
            } else {
                throw new Error(result.error || 'Erro de autenticação');
            }
        } catch (e) {
            console.error(e);
            isScanning = false;
            scannerBtn?.classList.remove('scanning');
            if (scannerStatus) scannerStatus.innerText = 'Erro na autenticação';
            if (scannerIcon) scannerIcon.style.color = 'var(--danger)';
            alert('Erro: ' + (e.message || 'Desconhecido'));
        }
    }

    scannerBtn?.addEventListener('click', startBiometricScan);
    simularBtn?.addEventListener('click', startBiometricScan);
});
