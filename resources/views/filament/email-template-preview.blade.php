<div style="padding: 20px;">
    <h3 style="margin-bottom: 16px; font-size: 16px; font-weight: 600;">📧 {{ $template->subject }}</h3>
    
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 16px;">
        <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #64748b;">可用变量：</h4>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach(($template->variables ?? []) as $var)
                <span style="background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-family: monospace;">
                    @{{{{ $var }}}}}
                </span>
            @endforeach
        </div>
    </div>
    
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
        {!! $template->content !!}
    </div>
</div>
