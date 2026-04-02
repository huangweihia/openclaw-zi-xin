@props(['article', 'user' => null])

<div class="vip-paywall" style="position: relative;">
    @if($article->is_vip && (!$user || !$user->isVip()))
        {{-- VIP 内容模糊处理 --}}
        <div style="position: relative;">
            {{-- 显示前 100 字 --}}
            <div style="filter: blur(0);">
                {!! Str::limit($article->content, 100) !!}
            </div>
            
            {{-- 模糊遮罩 --}}
            <div style="
                position: relative;
                margin-top: 20px;
                padding: 40px 20px;
                background: linear-gradient(to bottom, transparent, rgba(15, 23, 42, 0.95));
                text-align: center;
                border-radius: 12px;
            ">
                {{-- 锁定图标 --}}
                <div style="font-size: 48px; margin-bottom: 15px;">🔒</div>
                
                <h3 style="color: white; font-size: 20px; margin-bottom: 10px;">
                    VIP 专属内容
                </h3>
                
                <p style="color: #94a3b8; font-size: 14px; margin-bottom: 25px;">
                    开通 VIP 解锁全文，享受更多权益
                </p>
                
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    {{-- 开通 VIP 按钮 --}}
                    <a href="{{ route('vip') }}" 
                       style="
                           padding: 12px 24px;
                           background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                           color: white;
                           border-radius: 8px;
                           font-weight: 600;
                           text-decoration: none;
                           display: inline-flex;
                           align-items: center;
                           gap: 8px;
                           box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
                       ">
                        👑 开通 VIP ¥9.9/月
                    </a>
                    
                    {{-- 积分解锁按钮 --}}
                    @if($user && $user->points && $user->points->balance >= 100)
                        <button onclick="unlockArticle({{ $article->id }})"
                                style="
                                    padding: 12px 24px;
                                    background: rgba(16, 185, 129, 0.2);
                                    color: #10b981;
                                    border: 1px solid #10b981;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    cursor: pointer;
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 8px;
                                ">
                            ⭐ 使用 100 积分解锁
                        </button>
                    @elseif($user)
                        <button disabled
                                style="
                                    padding: 12px 24px;
                                    background: rgba(255, 255, 255, 0.05);
                                    color: #64748b;
                                    border: 1px solid rgba(255, 255, 255, 0.1);
                                    border-radius: 8px;
                                    font-weight: 600;
                                    cursor: not-allowed;
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 8px;
                                ">
                            📊 积分不足 ({{ $user->points->balance ?? 0 }}/100)
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           style="
                               padding: 12px 24px;
                               background: rgba(255, 255, 255, 0.1);
                               color: white;
                               border-radius: 8px;
                               font-weight: 600;
                               text-decoration: none;
                               display: inline-flex;
                               align-items: center;
                               gap: 8px;
                           ">
                            🔐 登录后解锁
                        </a>
                    @endif
                </div>
                
                {{-- VIP 权益列表 --}}
                <div style="
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                    text-align: left;
                ">
                    <div style="color: #94a3b8; font-size: 13px;">
                        <span style="color: #10b981;">✓</span> 无限阅读 VIP 文章
                    </div>
                    <div style="color: #94a3b8; font-size: 13px;">
                        <span style="color: #10b981;">✓</span> 无限次知识检索
                    </div>
                    <div style="color: #94a3b8; font-size: 13px;">
                        <span style="color: #10b981;">✓</span> 专属邮件推送
                    </div>
                    <div style="color: #94a3b8; font-size: 13px;">
                        <span style="color: #10b981;">✓</span> 优先客服支持
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 解锁成功后的完整内容（通过 JS 加载）--}}
        <div id="unlocked-content" style="display: none;"></div>
    @else
        {{-- 非 VIP 内容或已是 VIP，显示完整内容 --}}
        <div class="rich-html-content">{!! $article->content !!}</div>
    @endif
</div>

<script>
function unlockArticle(articleId) {
    if (!confirm('确定使用 100 积分解锁这篇文章吗？')) return;
    
    fetch(`/interactions/articles/${articleId}/unlock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // 显示完整内容
            document.getElementById('unlocked-content').innerHTML = data.content;
            document.getElementById('unlocked-content').style.display = 'block';
            // 隐藏遮罩
            event.target.closest('.vip-paywall').querySelector('div[style*="blur"]').nextElementSibling.style.display = 'none';
            alert('解锁成功！');
        } else {
            alert(data.message || '解锁失败');
        }
    })
    .catch(err => {
        console.error(err);
        alert('解锁失败，请重试');
    });
}
</script>
