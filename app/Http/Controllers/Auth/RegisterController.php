<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WechatBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * 显示注册表单 - MAX 版本
     */
    public function showRegistrationForm()
    {
        return view('max.auth.register');
    }

    /**
     * 发送短信验证码
     */
    public function sendSmsCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^1[3-9]\d{9}$/',
        ]);

        $phone = $request->phone;
        
        // 检查是否已注册
        $exists = User::where('phone', $phone)->exists();
        
        // 生成 6 位验证码
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // 存储验证码到缓存（5 分钟有效）
        cache()->set("sms_code_{$phone}", $code, 300);
        
        // 调用短信 API 发送
        $success = $this->sendSms($phone, $code);
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => '验证码已发送',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => '发送失败，请稍后重试',
        ], 500);
    }

    /**
     * 注册处理
     */
    public function register(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^1[3-9]\d{9}$/',
            'code' => 'required|size:6',
            'name' => 'nullable|string|max:50',
        ]);

        $phone = $request->phone;
        $code = $request->code;
        
        // 验证验证码
        $cachedCode = cache()->get("sms_code_{$phone}");
        if (!$cachedCode || $cachedCode !== $code) {
            return back()->withErrors(['code' => '验证码错误']);
        }
        
        // 检查是否已注册
        $user = User::where('phone', $phone)->first();
        
        if ($user) {
            // 已注册，直接登录
            Auth::login($user);
            cache()->forget("sms_code_{$phone}");
            
            return redirect()->intended('/max')
                ->with('success', '登录成功');
        }
        
        // 新用户，创建账号
        $user = User::create([
            'name' => $request->name ?? '用户' . substr($phone, -4),
            'phone' => $phone,
            'email' => null, // 可选，后续完善
            'password' => bcrypt(str()->random(32)), // 随机密码
            'role' => 'user',
            'phone_verified_at' => now(),
        ]);
        
        Auth::login($user);
        cache()->forget("sms_code_{$phone}");
        
        // 记录注册来源
        Log::info('新用户注册', [
            'user_id' => $user->id,
            'phone' => $phone,
            'source' => $request->source ?? 'web',
        ]);
        
        // 发送欢迎消息（可选）
        // $this->sendWelcomeMessage($user);
        
        return redirect()->route('max.home')
            ->with('success', '注册成功！');
    }

    /**
     * 显示企业微信绑定二维码
     */
    public function showEnterpriseWechatBind()
    {
        return view('auth.bind-enterprise-wechat');
    }

    /**
     * 企业微信回调（用户扫码加入后）
     */
    public function enterpriseWechatCallback(Request $request)
    {
        // 企业微信回调验证
        $echoStr = $request->get('echostr');
        if ($echoStr) {
            return response($echoStr);
        }
        
        // 处理回调数据
        $data = $request->all();
        $userId = $data['UserID'] ?? null;
        $openId = $data['OpenId'] ?? null;
        
        if ($userId) {
            // 查找对应的用户（通过手机号或其他方式）
            // 这里需要根据你的业务逻辑实现
            Log::info('企业微信用户绑定', ['user_id' => $userId]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * 发送短信验证码（接入阿里云/腾讯云）
     */
    protected function sendSms(string $phone, string $code): bool
    {
        // 示例：阿里云短信 API
        // 实际使用时需要配置 AccessKey/Secret
        
        try {
            // 这里调用阿里云/腾讯云短信 API
            // 示例代码：
            /*
            $response = Http::post('https://dysmsapi.aliyuncs.com/', [
                'PhoneNumbers' => $phone,
                'SignName' => 'AI 副业情报局',
                'TemplateCode' => 'SMS_XXX',
                'TemplateParam' => json_encode(['code' => $code]),
            ]);
            */
            
            Log::info('发送短信验证码', [
                'phone' => $phone,
                'code' => $code,
            ]);
            
            // 开发环境直接返回成功
            return true;
        } catch (\Exception $e) {
            Log::error('发送短信失败', [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * 发送欢迎消息
     */
    protected function sendWelcomeMessage(User $user)
    {
        // 发送欢迎邮件
        // Mail::to($user->email)->send(new WelcomeEmail($user));
        
        // 发送企业微信消息（如果已绑定）
        if ($user->enterprise_wechat_userid) {
            $bot = app(WechatBotService::class);
            $bot->sendText("欢迎加入 AI 副业情报局！\n\n每日 AI 资讯将在每天上午 9 点推送，请注意查收。\n\n有任何问题随时联系我。");
        }
    }
}
