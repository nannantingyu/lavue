<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminLogin
{
    /**
     * 后台接口权限中间件
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $controller_action = \Route::current()->action['controller'];
        list($controller, $action) = explode('@', $controller_action);

        $auth = getFunctionAnnotation($controller, $action, 'auth');
        if(!is_null($auth) && strpos($auth, ':') !== false) {
            $user = Auth::user();

            list($auth_path, $auth_oprate) = explode(':', $auth);

            // 记录操作日志
            if($auth_oprate != 'read') {
                $inputs = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
                $log = "用户%d(%s),【%s】了模块 %s-%s, 请求参数为 %s";
                Log::channel('opration')->info(sprintf($log, $user->id, $user->name, $auth_oprate, $controller, $action, $inputs));
            }

            if(is_null($user)) {
                return response()->json(['success'=>-3, 'msg'=>'请先登录']);
            }
            elseif($user->id != 1 && !call_user_func(array($user, 'can'.ucfirst($auth_oprate)), $auth_path)) {
                return response()->json(['success'=>-4, 'msg'=>'没有权限访问模块']);
            }
        }

        return $next($request);
    }
}
