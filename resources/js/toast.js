// Toast notification helper
export class Toast {
    static show(message, type = 'info', duration = 5000) {
        // Create toast container if it doesn't exist
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-3';
            document.body.appendChild(container);
        }

        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'toast-notification';
        
        // Set content based on type
        const icons = {
            success: `<svg class="w-5 h-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>`,
            error: `<svg class="w-5 h-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>`,
            info: `<svg class="w-5 h-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>`
        };

        const colors = {
            success: 'from-emerald-500/10 to-green-500/10 border-emerald-500/20',
            error: 'from-red-500/10 to-rose-500/10 border-red-500/20',
            info: 'from-blue-500/10 to-indigo-500/10 border-blue-500/20'
        };

        const textColors = {
            success: 'text-emerald-400',
            error: 'text-red-400',
            info: 'text-blue-400'
        };

        const iconColors = {
            success: 'bg-emerald-500/20',
            error: 'bg-red-500/20',
            info: 'bg-blue-500/20'
        };

        toast.innerHTML = `
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br ${colors[type]} backdrop-blur-xl border rounded-xl shadow-2xl max-w-sm transform transition-all duration-300 ease-out">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 ${iconColors[type]} rounded-full flex items-center justify-center">
                        ${icons[type]}
                    </div>
                </div>
                <div class="flex-1">
                    <p class="font-medium ${textColors[type]} text-sm capitalize">${type}</p>
                    <p class="text-slate-300 text-sm">${message}</p>
                </div>
                <button onclick="Toast.hide('${toastId}')" class="flex-shrink-0 text-slate-400 hover:text-slate-300 transition-colors">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;

        // Add entrance animation
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
        container.appendChild(toast);

        // Trigger entrance animation
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        // Auto-hide after duration
        setTimeout(() => {
            this.hide(toastId);
        }, duration);

        return toastId;
    }

    static hide(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    static success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    static error(message, duration = 7000) {
        return this.show(message, 'error', duration);
    }

    static info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
}

// Make Toast available globally
window.Toast = Toast;

// Example usage:
// Toast.success('User saved successfully!');
// Toast.error('An error occurred');
// Toast.info('Settings updated');
