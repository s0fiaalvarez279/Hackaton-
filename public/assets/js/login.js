//  DOM y utilidades generales 
const messageBox = document.getElementById('messageBox');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const forgotForm = document.getElementById('forgotPasswordForm');
const tabs = document.querySelectorAll('.tab-btn');

if (typeof APP_URL !== 'undefined' && APP_URL.endsWith('/')) window.APP_URL = APP_URL.slice(0, -1);

// Tabs
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        loginForm.classList.toggle('active', target === 'login');
        registerForm.classList.toggle('active', target === 'register');
        forgotForm.classList.remove('active');
        hideMessage();
    });
});

// Mostrar/ocultar contraseña
window.togglePassword = function(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
};

// Validación de fortaleza de contraseña
const regPass = document.getElementById('registerPassword');
if (regPass) regPass.addEventListener('input', validatePasswordStrength);
function validatePasswordStrength() {
    const pwd = regPass.value;
    const rules = {
        len: pwd.length >= 8,
        upper: /[A-Z]/.test(pwd),
        num: /[0-9]/.test(pwd),
        special: /[^A-Za-z0-9]/.test(pwd)
    };
    const strengthBars = document.querySelectorAll('.strength-bar');
    const validCount = Object.values(rules).filter(Boolean).length;
    strengthBars.forEach((bar, idx) => {
        bar.classList.remove('weak','medium','strong');
        if (idx < validCount) {
            if (validCount <= 2) bar.classList.add('weak');
            else if (validCount === 3) bar.classList.add('medium');
            else bar.classList.add('strong');
        }
    });
    validatePasswordMatch();
}
function validatePasswordMatch() {
    const pwd = document.getElementById('registerPassword')?.value || '';
    const confirm = document.getElementById('confirmPassword')?.value || '';
    const matchDiv = document.getElementById('passwordMatch');
    if (!matchDiv) return;
    if (confirm === '') { matchDiv.innerHTML = ''; return; }
    if (pwd === confirm) {
        matchDiv.innerHTML = '<i class="fas fa-check-circle"></i> Las contraseñas coinciden';
        matchDiv.className = 'match-hint match-success';
    } else {
        matchDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
        matchDiv.className = 'match-hint match-error';
    }
}
document.getElementById('confirmPassword')?.addEventListener('input', validatePasswordMatch);

// Mensajes
function showMessage(msg, type = 'error') {
    if (!messageBox) return;
    messageBox.style.display = 'flex';
    messageBox.className = `msg-box ${type}`;
    messageBox.innerHTML = `<i class="fas ${type==='success'?'fa-check-circle':'fa-exclamation-triangle'}"></i><span>${msg}</span>`;
    setTimeout(hideMessage, 5000);
}
function hideMessage() { if (messageBox) messageBox.style.display = 'none'; }

// Login con email
window.handleEmailLogin = async function() {
    const email = document.getElementById('loginEmail').value.trim();
    const pwd = document.getElementById('loginPassword').value;
    const remember = document.getElementById('rememberLogin')?.checked;
    if (!email || !pwd) { showMessage('Completa todos los campos'); return; }
    showLoading('loginButton', true);
    try {
        const res = await fetch(`${APP_URL}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password: pwd, remember })
        });
        const data = await res.json();
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => window.location.href = data.redirect || `${APP_URL}/dashboard`, 1000);
        } else {
            showMessage(data.message);
            showLoading('loginButton', false);
        }
    } catch(e) {
        showMessage('Error de conexión con el servidor');
        showLoading('loginButton', false);
    }
};

// Registro con email
window.handleEmailRegister = async function() {
    const nombre = document.getElementById('registerName').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const pwd = document.getElementById('registerPassword').value;
    const confirm = document.getElementById('confirmPassword').value;
    const terms = document.getElementById('terms')?.checked;
    if (!nombre || !email || !pwd || !confirm) { showMessage('Todos los campos son obligatorios'); return; }
    if (!terms) { showMessage('Debes aceptar los términos y condiciones'); return; }
    if (pwd !== confirm) { showMessage('Las contraseñas no coinciden'); return; }
    if (pwd.length < 8 || !/[A-Z]/.test(pwd) || !/[0-9]/.test(pwd) || !/[^A-Za-z0-9]/.test(pwd)) {
        showMessage('La contraseña no cumple los requisitos de seguridad');
        return;
    }
    showLoading('registerButton', true);
    try {
        const res = await fetch(`${APP_URL}/auth/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre, email, password: pwd })
        });
        const data = await res.json();
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => window.location.href = `${APP_URL}/dashboard`, 1500);
        } else {
            showMessage(data.message);
            showLoading('registerButton', false);
        }
    } catch(e) {
        showMessage('Error de conexión');
        showLoading('registerButton', false);
    }
};

// Recuperar contraseña (simulada)
window.handleForgotPassword = async function() {
    const email = document.getElementById('forgotEmail').value.trim();
    if (!email) { showMessage('Ingresa tu correo'); return; }
    showLoading('sendResetLink', true);
    setTimeout(() => {
        showMessage('Si el correo está registrado, recibirás un enlace de recuperación.', 'success');
        showLoading('sendResetLink', false);
        document.getElementById('forgotEmail').value = '';
    }, 1000);
};

// Demo
window.demoLogin = function() {
    document.getElementById('loginEmail').value = 'admin@transito.com';
    document.getElementById('loginPassword').value = 'admin123';
    handleEmailLogin();
};

// Helpers
function showLoading(btnId, show) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    const span = btn.querySelector('span');
    const spinner = btn.querySelector('.spinner');
    btn.disabled = show;
    if (span) span.style.display = show ? 'none' : 'inline';
    if (spinner) spinner.style.display = show ? 'inline-block' : 'none';
}

// Eventos de olvido/back
document.getElementById('forgotPassword')?.addEventListener('click', (e) => {
    e.preventDefault();
    loginForm.classList.remove('active');
    registerForm.classList.remove('active');
    forgotForm.classList.add('active');
    tabs.forEach(t => t.classList.remove('active'));
});
document.getElementById('backToLogin')?.addEventListener('click', (e) => {
    e.preventDefault();
    forgotForm.classList.remove('active');
    loginForm.classList.add('active');
    document.querySelector('[data-tab="login"]')?.classList.add('active');
});

// Enter key
['loginEmail','loginPassword','registerName','registerEmail','registerPassword','confirmPassword','forgotEmail'].forEach(id => {
    const input = document.getElementById(id);
    if (input) {
        let btnId = id.includes('login') ? 'loginButton' : (id.includes('register') ? 'registerButton' : 'sendResetLink');
        input.addEventListener('keypress', e => { if(e.key === 'Enter') document.getElementById(btnId)?.click(); });
    }
});

//  GOOGLE LOGIN 
let tokenClient = null;

function initializeGoogleClient() {
    if (!GOOGLE_CONFIGURED || !GOOGLE_CLIENT_ID || GOOGLE_CLIENT_ID === 'TU_CLIENT_ID_DE_GOOGLE.apps.googleusercontent.com') {
        console.warn('⚠️ Google Client ID no configurado. El botón de Google estará deshabilitado.');
        const googleBtn = document.getElementById('googleSignInBtn');
        if (googleBtn) {
            googleBtn.disabled = true;
            googleBtn.innerHTML = '<i class="fab fa-google"></i> Google no configurado';
        }
        return;
    }

    tokenClient = google.accounts.oauth2.initTokenClient({
        client_id: GOOGLE_CLIENT_ID,
        scope: 'email profile',
        response_type: 'id_token',
        callback: async (tokenResponse) => {
            const idToken = tokenResponse.id_token;
            if (!idToken) {
                showMessage('No se pudo obtener la identificación de Google', 'error');
                return;
            }

            // Mostrar estado de carga en el botón
            const googleBtn = document.getElementById('googleSignInBtn');
            const originalContent = googleBtn.innerHTML;
            googleBtn.innerHTML = '<div class="spinner" style="display:inline-block; border-top-color:#4285f4;"></div> Conectando...';
            googleBtn.disabled = true;

            try {
                const res = await fetch(`${APP_URL}/auth/google`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_token: idToken })
                });
                const data = await res.json();
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => window.location.href = data.redirect || `${APP_URL}/dashboard`, 1000);
                } else {
                    showMessage(data.message, 'error');
                    googleBtn.innerHTML = originalContent;
                    googleBtn.disabled = false;
                }
            } catch (error) {
                showMessage('Error de conexión con el servidor', 'error');
                googleBtn.innerHTML = originalContent;
                googleBtn.disabled = false;
            }
        },
        error_callback: (error) => {
            console.error('Error en Google OAuth:', error);
            showMessage('No se pudo iniciar sesión con Google. Intenta de nuevo.', 'error');
            const googleBtn = document.getElementById('googleSignInBtn');
            if (googleBtn) googleBtn.disabled = false;
        }
    });
}

// Esperar a que la librería de Google (GSI) esté completamente cargada
function waitForGoogleLib() {
    if (typeof google !== 'undefined' && google.accounts && google.accounts.oauth2) {
        initializeGoogleClient();
    } else {
        setTimeout(waitForGoogleLib, 100);
    }
}

window.addEventListener('load', () => {
    waitForGoogleLib();
});

// Manejar el clic en el botón de Google
const googleBtn = document.getElementById('googleSignInBtn');
if (googleBtn) {
    googleBtn.addEventListener('click', () => {
        if (!tokenClient) {
            showMessage('La configuración de Google aún no está lista. Por favor recarga la página.', 'error');
            return;
        }
        tokenClient.requestAccessToken(); 
    });
}