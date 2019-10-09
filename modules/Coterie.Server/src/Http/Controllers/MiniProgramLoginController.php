<?php

/*
 * This file is part of ibrand/coterie-server.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iBrand\Coterie\Server\Http\Controllers;

use EasyWeChat;
use iBrand\Component\User\Repository\UserBindRepository;
use iBrand\Component\User\Repository\UserRepository;
use iBrand\Coterie\Core\Services\UserService;
use iBrand\Coterie\Core\Services\MiniProgramService;

class MiniProgramLoginController extends Controller
{
    protected $userRepository;
    protected $userBindRepository;
    protected $userService;
    protected $miniProgramService;

    public function __construct(UserRepository $userRepository, UserBindRepository $userBindRepository, UserService $userService, MiniProgramService $miniProgramService)
    {
        $this->userRepository = $userRepository;
        $this->userBindRepository = $userBindRepository;
        $this->userService = $userService;
        $this->miniProgramService = $miniProgramService;
    }

    public function login()
    {
        $code = request('code');
        if (empty($code)) {
            return $this->failed('缺失code');
        }

        if (env('SAAS_SERVER_TYPE') == 'public') {

            \Log::info('saas');

            $result = $this->miniProgramService->getSession(request()->header('wechatappid'), $code);

        } else {

            $miniProgram = EasyWeChat::miniProgram();

            $result = $miniProgram->auth->session($code);
        }


        if (!isset($result['openid'])) {
            return $this->failed('获取openid失败.');
        }

        $openid = $result['openid'];

        //1. openid 不存在相关用户和记录，直接返回 openid
        if (!$userBind = $this->userBindRepository->getByOpenId($openid)) {
            $userBind = $this->userBindRepository->create(['open_id' => $openid, 'type' => 'miniprogram',
                'app_id' => config('wechat.mini_program.default.app_id'),]);

            return $this->success(['open_id' => $openid]);
        }

        //2. openid 不存在相关用户，直接返回 openid
        if (!$userBind->user_id) {
            return $this->success(['open_id' => $openid]);
        }

        //3. 绑定了用户,直接返回 token
        $user = $this->userRepository->find($userBind->user_id);

        $token = $user->createToken($user->id)->accessToken;

        return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
    }

    public function mobileLogin()
    {
        //1. get session key.
        $code = request('code');

        if (env('SAAS_SERVER_TYPE') == 'public') {

            $result = $this->miniProgramService->getSession(request()->header('wechatappid'), $code);

        } else {

            $miniProgram = EasyWeChat::miniProgram();

            $result = $miniProgram->auth->session($code);
        }


        if (!isset($result['session_key'])) {
            return $this->failed('获取 session_key 失败.');
        }

        $sessionKey = $result['session_key'];

        //2. get phone number.
        $encryptedData = request('encryptedData');
        $iv = request('iv');

        $decryptedData = $miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptedData);

        if (!isset($decryptedData['purePhoneNumber'])) {
            return $this->failed('获取手机号失败.');
        }

        $mobile = $decryptedData['purePhoneNumber'];

        //3. get or create user.
        if (!$user = $this->userRepository->getUserByCredentials(['mobile' => $mobile])) {
            $data = ['mobile' => $mobile];
            if ($client_id = $this->client_id()) {
                $data['client_id'] = $client_id;
            }
            $user = $this->userRepository->create($data);
        }

        $token = $user->createToken($user->id)->accessToken;

        $this->userService->bindPlatform($user->id, request('open_id'), config('wechat.mini_program.default.app_id'), 'miniprogram');

        return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
    }

    public function getOpenIdByCode()
    {
        $code = request('code');

        if (empty($code)) {
            return $this->failed('缺失code');
        }

        if (env('SAAS_SERVER_TYPE') == 'public') {

            $result = $this->miniProgramService->getSession(request()->header('wechatappid'), $code);

        } else {

            $miniProgram = EasyWeChat::miniProgram();

            $result = $miniProgram->auth->session($code);
        }


        if (!isset($result['openid'])) {
            return $this->failed('获取openid失败.');
        }

        $openid = $result['openid'];

        return $this->success(compact('openid'));
    }
}
