<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚡ 快捷操作
        </x-slot>
        <x-slot name="description">
            常用功能快速访问
        </x-slot>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <a href="{{ route('filament.admin.resources.articles.index') }}" 
               style="padding: 20px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 12px; text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <span style="font-size: 32px;">📝</span>
                <span style="font-weight: 600;">文章管理</span>
                <span style="font-size: 12px; opacity: 0.8;">查看/编辑文章</span>
            </a>
            
            <a href="{{ route('filament.admin.resources.projects.index') }}" 
               style="padding: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <span style="font-size: 32px;">🐙</span>
                <span style="font-weight: 600;">项目管理</span>
                <span style="font-size: 12px; opacity: 0.8;">GitHub 项目</span>
            </a>
            
            <a href="{{ route('filament.admin.pages.email-manager') }}" 
               style="padding: 20px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <span style="font-size: 32px;">📧</span>
                <span style="font-weight: 600;">邮件发送</span>
                <span style="font-size: 12px; opacity: 0.8;">选择模板发送</span>
            </a>
            
            <a href="{{ route('filament.admin.pages.task-manager') }}" 
               style="padding: 20px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <span style="font-size: 32px;">📋</span>
                <span style="font-weight: 600;">任务管理</span>
                <span style="font-size: 12px; opacity: 0.8;">查看执行记录</span>
            </a>
            
            <a href="{{ route('filament.admin.resources.knowledge-bases.index') }}" 
               style="padding: 20px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <span style="font-size: 32px;">📚</span>
                <span style="font-weight: 600;">知识库</span>
                <span style="font-size: 12px; opacity: 0.8;">文档管理</span>
            </a>
            
            <a href="{{ route('filament.admin.pages.points-manager') }}" 
               style="padding: 20px; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border-radius: 12px; text-decoration: none; color: white; display: flex; flex-direction: column; align-items: center; gap: 10px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <span style="font-size: 32px;">🪙</span>
                <span style="font-weight: 600;">积分管理</span>
                <span style="font-size: 12px; opacity: 0.8;">积分流水</span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
