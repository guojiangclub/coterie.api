<?php

/*
 * This file is part of ibrand/EC-Open-Core.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iBrand\Coterie\Core\Providers;

use iBrand\Component\User\Models\User as BaseUser;
use iBrand\Component\User\UserServiceProvider;
use iBrand\Coterie\Core\Auth\User;
use Illuminate\Support\ServiceProvider;
use Schema;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use phpseclib\Crypt\RSA;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\RedisStore;
use iBrand\Coterie\Core\Repositories\CoterieRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\CoterieRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\MemberRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\MemberRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\ContentRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\ContentRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\CommentRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\CommentRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\QuestionRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\QuestionRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\ReplyRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\ReplyRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\PraiseRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\PraiseRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\OrderRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\OrderRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\InviteRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\InviteRepositoryEloquent;
use iBrand\Coterie\Core\Repositories\InviteMemberRepository;
use iBrand\Coterie\Core\Repositories\Eloquent\InviteMemberRepositoryEloquent;
use iBrand\Coterie\Core\Models\Comment;
use iBrand\Coterie\Core\Policies\CommentPolicy;
use iBrand\Coterie\Core\Models\Content;
use iBrand\Coterie\Core\Policies\ContentPolicy;
use iBrand\Coterie\Core\Models\Coterie;
use iBrand\Coterie\Core\Policies\CoteriePolicy;
use iBrand\Coterie\Core\Models\Reply;
use iBrand\Coterie\Core\Policies\ReplyPolicy;
use iBrand\Coterie\Core\Models\Member;
use iBrand\Coterie\Core\Policies\MemberPolicy;
use iBrand\Coterie\Core\Models\Question;
use iBrand\Coterie\Core\Policies\QuestionPolicy;
use iBrand\Coterie\Core\Models\Praise;
use iBrand\Coterie\Core\Policies\PraisePolicy;
use iBrand\Coterie\Core\Models\Order;
use iBrand\Coterie\Core\Policies\OrderPolicy;
use iBrand\Coterie\Core\Services\CoteriePayNotifyService;
use iBrand\Coterie\Core\Console\InstallCommand;


class AppServiceProvider extends ServiceProvider
{




    protected $policies = [
        Coterie::class => CoteriePolicy::class,
        Comment::class => CommentPolicy::class,
        Content::class => ContentPolicy::class,
        Reply::class => ReplyPolicy::class,
        Member::class => MemberPolicy::class,
        Question::class => QuestionPolicy::class,
        Praise::class => PraisePolicy::class,
        Order::class => OrderPolicy::class

    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {


        if (config('ibrand.coterie.secure')) {
            \URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        if (!class_exists('CreateCoterieTables')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/../../migrations/create_coterie_tables.php.stub' => database_path() . "/migrations/{$timestamp}_create_coterie_tables.php",
            ], 'migrations');
        }

        $this->registerPolicies();

        $this->publishes([
            app_path() . '/../vendor/laravel/passport/database/migrations' => database_path('migrations'),
        ], 'migrations');


        $this->commands([InstallCommand::class]);

        $this->commands([
            \iBrand\Coterie\Core\Console\DatabaseCreateCommand::class,
            \iBrand\Coterie\Core\Console\DatabaseDeleteCommand::class,
        ]);


        $uuid=client_id();

        if ($uuid AND $website = app(\Hyn\Tenancy\Contracts\Repositories\WebsiteRepository::class)->findByUuid($uuid)) {

        $environment = app(\Hyn\Tenancy\Environment::class);

        $environment->tenant($website);

        config(['database.default' => 'tenant']);

            $info=[
                // 公众号 APPID
                'app_id' => '',
                // 小程序 APPID
                'miniapp_id' => request()->header('wechatappid'),
                // APP 引用的 appid
                'appid' => '',
                // 微信支付分配的微信商户号
                'mch_id' => settings('wechat_payment_mcn_id'),
                // 微信支付异步通知地址
                'notify_url' => '/notify/wechat',
                // 微信支付签名秘钥
                'key' => settings('wechat_payment_key'),
                // 客户端证书路径，退款、红包等需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
                'cert_client' => '',
                // 客户端秘钥路径，退款、红包等需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
                'cert_key' => '',
                // optional，默认 warning；日志路径为：sys_get_temp_dir().'/logs/yansongda.pay.log'
                'log' => [
                    'file' => storage_path('logs/wechat.log'),
                    //  'level' => 'debug'
                    'type' => 'single', // optional, 可选 daily.
                    'max_file' => 30,
                ],

            ];

            config(['ibrand.pay.default.wechat.' . $uuid => $info]);

        } else {

            config(['database.default' => 'mysql']);
        }



        $this->setRedisTenancy();

    }

    public function register()
    {

        $this->registerComponent();

        $this->app->bind(BaseUser::class, User::class);

        $this->app->bind(CoterieRepository::class, CoterieRepositoryEloquent::class);

        $this->app->bind(MemberRepository::class, MemberRepositoryEloquent::class);

        $this->app->bind(ContentRepository::class, ContentRepositoryEloquent::class);

        $this->app->bind(CommentRepository::class, CommentRepositoryEloquent::class);

        $this->app->bind(QuestionRepository::class, QuestionRepositoryEloquent::class);

        $this->app->bind(ReplyRepository::class, ReplyRepositoryEloquent::class);

        $this->app->bind(PraiseRepository::class, PraiseRepositoryEloquent::class);

        $this->app->bind(OrderRepository::class, OrderRepositoryEloquent::class);

        $this->app->bind(InviteRepository::class, InviteRepositoryEloquent::class);

        $this->app->bind(InviteMemberRepository::class, InviteMemberRepositoryEloquent::class);

        $this->app->bind('ibrand.pay.notify.coterie', CoteriePayNotifyService::class);



    }

    protected function registerComponent()
    {
        $this->app->register(UserServiceProvider::class);
    }

    protected function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }

    }



    protected function setRedisTenancy()
    {

//        config(['cache.default' => 'redis_tenancy']);
//
//        Cache::extend('redis_tenancy', function ($app) {
//
//            $uuid = client_id();
//
//            $res = Cache::repository(new RedisStore(
//                $app['redis'],
//                $uuid,
//                $app['config']['cache.stores.redis.connection']
//            ));
//
//            return $res;
//
//        });


    }




}
