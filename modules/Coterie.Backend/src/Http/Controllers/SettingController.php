<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/7
 * Time: 20:14
 */

namespace iBrand\Coterie\Backend\Http\Controllers;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class SettingController extends Controller
{
    public function paySetting()
    {
        return LaravelAdmin::content(function (Content $content) {

            $content->header('支付设置');

            $content->breadcrumb(
                ['text' => '支付设置', 'url' => '', 'no-pjax' => 1],
                ['text' => '支付设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '支付设置']
            );

            $content->body(view('account-backend::settings.pay'));
        });
    }

    public function savePay()
    {
        //1. 保存配置进入到数据库
        $data = request()->except('_token');

        settings()->setSetting($data);

        $this->ajaxJson();
    }
}