# 果酱圈子

一款可媲美 “知识星球” 的赋能社群运营者的平台，在这里每个人都能搭建一个真正属于自己社群平台。

目前只有小程序版本，未来会提供 uniapp 版本。小程序版本有一个问题需要注意，在 IOS 端收费圈子没办法付费，这是苹果政策问题导致的，目前无法解决，请知晓。

## 体验

![果酱社区圈子](https://cdn.guojiang.club/readme%E6%9E%9C%E9%85%B1%E7%A4%BE%E5%8C%BA%E5%9C%88%E5%AD%90.jpg)

## 安装

```
git clone git@github.com:guojiangclub/coterie.api.git

composer install -vvv

cp .env.example .env    # 务必配置好数据库信息

php artisan vendor:publish --all

chmod -R 0777 storage

chmod -R 0777 bootstrap
 
php artisan ibrand:coterie-install
```

## 小程序

小程序源码地址：[果酱圈子小程序源码](http://gitlab.guojiang.club:8090/guojiangclub/coterie.miniprogram)

## 果酱云社区

<p align="center">
  <a href="https://guojiang.club/" target="_blank">
    <img src="https://cdn.guojiang.club/image/2022/02/16/wu_1fs0jbco2182g280l1vagm7be6.png" alt="点击跳转"/>
  </a>
</p>



- 全网真正免费的IT课程平台

- 专注于综合IT技术的在线课程，致力于打造优质、高效的IT在线教育平台

- 课程方向包含Python、Java、前端、大数据、数据分析、人工智能等热门IT课程

- 300+免费课程任你选择



<p align="center">
  <a href="https://guojiang.club/" target="_blank">
    <img src="https://cdn.guojiang.club/image/2022/02/16/wu_1fs0l82ae1pq11e431j6n17js1vq76.png" alt="点击跳转"/>
  </a>
</p>