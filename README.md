<p align="left">
<h1>BBBUG聊天室 API端</h1>
</p>
<p align="left">
<a href="https://gitee.com/bbbug_com/ChatAPI/stargazers" target="_blank"><img src="https://svg.hamm.cn/gitee.svg?type=star&user=bbbug_com&project=ChatAPI"/></a>
<a href="https://gitee.com/bbbug_com/ChatAPI/members" target="_blank"><img src="https://svg.hamm.cn/gitee.svg?type=fork&user=bbbug_com&project=ChatAPI"/></a>
<img src="https://svg.hamm.cn/badge.svg?key=Base&value=Vue.Element"/>
<img src="https://svg.hamm.cn/badge.svg?key=License&value=Apache-2.0"/>
</p>

### 介绍

此仓库为BBBUG项目后端API部分，其他客户端代码请查看组织下的对应仓库。开发者QQ群：1140258698

体验一下：<a href="https://www.bbbug.com/" target="_blank">www.bbbug.com</a>

### 免责声明

平台音乐和视频直播流数据来源于第三方网站，仅供学习交流使用，请勿用于商业用途。

### 技术架构

IM后端采用 Node 实现 ```Websocket``` 服务，```Nginx``` 做Wss代理，前端采用 ```ElementUI&vue``` 实现，后端使用 ```StartAdmin``` 做管理平台。 Websocket.js 为后端Websocket实现代码，可自行安装相关包后使用pm2等进程管理工具将后端websocket持久化运行。


### 使用说明

1. clone当前项目 ```git clone https://gitee.com/bbbug_com/ChatAPI.git```

2. 安装依赖项 ```composer install```

3. 导入数据库文件 ```Database.sql```

4. 修改```conf```表中的部分配置即可。

5. 部署站点至public目录，api即通过url可访问。

6. 配合其他端运行此项目。

### 特色功能
```
1、创建房间、切换房间，房间权限与房间类型管理
2、点歌/切歌/听歌与歌曲播放进度同步
3、聊天、摸一摸、送歌等部分交互功能

更多功能等你来扩展开发...
```


### 参与贡献
```
1. Fork 本仓库
2. 新建分支 添加或修改功能
3. 提交代码
4. 新建 Pull Request
```

### 晒个截图
![BBBUG](https://images.gitee.com/uploads/images/2020/1105/220353_28e6e322_145025.png "截屏2020-11-05 22.03.36.png")
