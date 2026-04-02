@extends('layouts.app')

@section('title', '联系方式 - AI 副业情报局')

@section('content')
<section style="padding: 80px 0;">
    <div class="container" style="max-width: 700px;">
        <h1 style="font-size: 42px; margin-bottom: 20px; text-align: center;">联系我们</h1>
        <p style="text-align: center; color: var(--gray-light); font-size: 18px; margin-bottom: 60px;">
            有任何问题或建议？给我们留言吧！
        </p>

        <div class="card" style="padding: 40px;">
            <form id="contactForm" onsubmit="return false;">
                <div class="form-group">
                    <label class="form-label" for="name">姓名</label>
                    <input class="form-input" id="name" type="text" placeholder="你的姓名" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">邮箱</label>
                    <input class="form-input" id="email" type="email" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">主题</label>
                    <select class="form-input" id="subject" required>
                        <option value="">请选择主题</option>
                        <option value="question">问题咨询</option>
                        <option value="suggestion">功能建议</option>
                        <option value="cooperation">商务合作</option>
                        <option value="other">其他</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">留言内容</label>
                    <textarea class="form-input" id="message" rows="6" placeholder="请详细描述你的问题或需求..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 16px;" onclick="showConfirmModal()">
                    发送留言
                </button>
            </form>
        </div>

        <div class="card" style="padding: 30px; margin-top: 40px; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3);">
            <h3 style="margin-bottom: 16px;">💡 快速响应</h3>
            <p style="color: var(--gray-light); line-height: 1.8;">
                我们会在 1-2 个工作日内回复你的留言。如果是紧急问题，建议直接发送邮件至 contact@example.com。
            </p>
        </div>
    </div>
</section>

<script>
function showConfirmModal() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value.trim();

    if (!name || !email || !subject || !message) {
        showToast('请填写所有必填字段', 'error');
        return;
    }

    if (!isValidEmail(email)) {
        showToast('请输入有效的邮箱地址', 'error');
        return;
    }

    showConfirm({
        icon: '📧',
        title: '确认发送',
        content: '确定要发送这条留言吗？<br>我们会在 1-2 个工作日内回复你的邮箱。',
        confirmText: '确认发送',
        confirmColor: '#6366f1',
        onConfirm: async () => {
            // 模拟表单提交
            await new Promise(resolve => setTimeout(resolve, 1500));
            showToast('✅ 留言发送成功！我们会尽快回复你的邮箱。', 'success');
            document.getElementById('contactForm').reset();
        },
        onCancel: () => {
            // 取消操作
        }
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
</script>
@endsection
