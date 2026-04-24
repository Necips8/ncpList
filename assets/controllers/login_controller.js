import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["username", "password"];

    async login(event) {
        event.preventDefault();
        
        const username = this.usernameTarget.value;
        const password = this.passwordTarget.value;
        const errorEl = document.getElementById('login-error');
        
        if (errorEl) errorEl.style.display = 'none';

        try {
            const response = await fetch('/api/login_check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            if (response.ok) {
                const data = await response.json();
                localStorage.setItem('jwt_token', data.token);
                window.location.href = '/';
            } else {
                if (errorEl) errorEl.style.display = 'block';
            }
        } catch (error) {
            console.error("Login failed:", error);
            if (errorEl) errorEl.style.display = 'block';
        }
    }
}
