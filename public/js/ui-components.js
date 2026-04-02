/**
 * 全局 UI 组件 - 模态窗、Toast、Loading
 */

// Toast 通知
function showToast(message, type = 'success', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 8px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#6366f1'};
        color: white;
        font-weight: 600;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <span>${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// 确认模态窗
function showConfirm(options = {}) {
    const {
        title = '确认操作',
        content = '确定要执行此操作吗？',
        confirmText = '确认',
        cancelText = '取消',
        confirmColor = '#6366f1',
        onConfirm = () => {},
        onCancel = () => {},
    } = options;
    
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.2s ease;
        `;
        
        modal.innerHTML = `
            <div style="
                background: var(--dark-light, #1e293b);
                border-radius: 16px;
                padding: 40px;
                max-width: 450px;
                width: 90%;
                box-shadow: 0 20px 50px rgba(0,0,0,0.5);
                animation: slideUp 0.3s ease;
            ">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <span style="font-size: 32px;">${options.icon || '🤔'}</span>
                    <h3 style="color: white; font-size: 20px; font-weight: 600; margin: 0;">${title}</h3>
                </div>
                <div style="color: var(--gray-light, #94a3b8); font-size: 15px; line-height: 1.8; margin-bottom: 25px;">
                    ${content}
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button class="modal-cancel" style="
                        padding: 12px 24px;
                        background: var(--dark, #0f172a);
                        color: white;
                        border: 1px solid rgba(255,255,255,0.2);
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                    ">${cancelText}</button>
                    <button class="modal-confirm" style="
                        padding: 12px 24px;
                        background: ${confirmColor};
                        color: white;
                        border: none;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    ">
                        <span class="confirm-text">${confirmText}</span>
                        <span class="confirm-loading" style="display: none;">⏳</span>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const confirmBtn = modal.querySelector('.modal-confirm');
        const cancelBtn = modal.querySelector('.modal-cancel');
        
        const close = (result) => {
            modal.style.animation = 'fadeOut 0.2s ease';
            setTimeout(() => {
                modal.remove();
                resolve(result);
            }, 200);
        };
        
        confirmBtn.onclick = async () => {
            confirmBtn.disabled = true;
            confirmBtn.querySelector('.confirm-text').textContent = '处理中...';
            confirmBtn.querySelector('.confirm-loading').style.display = 'inline';
            
            try {
                await onConfirm();
                close(true);
            } catch (error) {
                showToast(error.message || '操作失败', 'error');
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.confirm-text').textContent = confirmText;
                confirmBtn.querySelector('.confirm-loading').style.display = 'none';
            }
        };
        
        cancelBtn.onclick = () => {
            onCancel();
            close(false);
        };
        
        modal.onclick = (e) => {
            if (e.target === modal) {
                onCancel();
                close(false);
            }
        };
    });
}

// Loading 状态
function showLoading(message = '处理中...') {
    const loading = document.createElement('div');
    loading.id = 'global-loading';
    loading.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10001;
        animation: fadeIn 0.2s ease;
    `;
    loading.innerHTML = `
        <div style="font-size: 48px; margin-bottom: 20px;">⏳</div>
        <div style="color: white; font-size: 18px; font-weight: 600;">${message}</div>
    `;
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('global-loading');
    if (loading) {
        loading.style.animation = 'fadeOut 0.2s ease';
        setTimeout(() => loading.remove(), 200);
    }
}

// 添加 CSS 动画
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
`;
document.head.appendChild(style);

// 导出全局函数
window.showToast = showToast;
window.showConfirm = showConfirm;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
