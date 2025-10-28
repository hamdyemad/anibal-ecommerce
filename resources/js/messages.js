// Custom message function using utilities message system
window.showMessage = function(type, message, icon = 'check-circle', duration = 3000) {
    const messageWrapper = document.querySelector('.message-wrapper');
    if (!messageWrapper) return;

    const messageId = 'msg-' + Date.now();
    const iconClass = icon;
    
    const messageHTML = `
        <div id="${messageId}" class="alert-message alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease;">
            <div class="alert-content d-flex align-items-center">
                <i class="uil uil-${iconClass} me-2" style="font-size: 20px;"></i>
                <p class="mb-0">${message}</p>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    messageWrapper.insertAdjacentHTML('beforeend', messageHTML);
    
    // Auto remove after duration
    setTimeout(() => {
        const msgElement = document.getElementById(messageId);
        if (msgElement) {
            msgElement.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => msgElement.remove(), 300);
        }
    }, duration);
};
