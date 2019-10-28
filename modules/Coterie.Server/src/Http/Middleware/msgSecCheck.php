<?php

namespace iBrand\Coterie\Server\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iBrand\Coterie\Core\Common\MiniProgram;


class msgSecCheck
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    protected $miniProgram;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth ,MiniProgram $miniProgram)
    {
        $this->auth = $auth;

        $this->miniProgram=$miniProgram;

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if($request->isMethod('post') AND count($request->all())){

            $input=json_encode($request->all(),JSON_UNESCAPED_UNICODE);
            $pattern ='utf8'?'/[\x{4e00}-\x{9fa5}]/u':'/[\x80-\xFF]/';
            preg_match_all($pattern,$input,$result);
            $temp =join('',$result[0]);

            if(!empty($temp)){

                $res=$this->miniProgram->msgSecCheck($temp);



                if($res['errcode']!=0){

                    return response(['status' => false, 'code' => 400, 'message' =>'您的内容违反相关规定' ,'data' =>[]]);
                }
            }


        }

        return $next($request);
    }



}
