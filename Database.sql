-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2021-06-30 20:32:25
-- 服务器版本： 5.6.48-log
-- PHP Version: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `BBBBUG_Export`
--

-- --------------------------------------------------------

--
-- 表的结构 `sa_access`
--

CREATE TABLE `sa_access` (
  `access_id` int(11) NOT NULL,
  `access_user` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `access_plat` varchar(255) NOT NULL DEFAULT 'all' COMMENT '登录平台',
  `access_ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP',
  `access_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `access_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `access_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='授权信息表';

-- --------------------------------------------------------

--
-- 表的结构 `sa_app`
--

CREATE TABLE `sa_app` (
  `app_id` int(11) NOT NULL,
  `app_key` varchar(255) NOT NULL DEFAULT '' COMMENT 'key',
  `app_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'name',
  `app_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'url',
  `app_user` int(11) NOT NULL DEFAULT '0' COMMENT 'user',
  `app_scope` varchar(255) NOT NULL DEFAULT '' COMMENT 'scope',
  `app_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `app_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `app_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='应用表';

--
-- 转存表中的数据 `sa_app`
--

INSERT INTO `sa_app` (`app_id`, `app_key`, `app_name`, `app_url`, `app_user`, `app_scope`, `app_status`, `app_createtime`, `app_updatetime`) VALUES
(1, '请重置后对接', 'BBBUG', 'https://bbbug.com', 1, '', 0, 0, 0),
(1001, '请重置后对接', 'Gitee', 'https://gitee.com/#extra#', 1, '', 0, 0, 0),
(1002, '请重置后对接', 'OSChina', 'https://my.oschina.net/#extra#', 1, '', 0, 0, 0),
(1003, '请重置后对接', 'QQ', 'https://hamm.cn', 1, '', 0, 0, 0),
(1004, '请重置后对接', '钉钉', 'https://hamm.cn', 1, '', 0, 0, 0),
(1005, '请重置后对接', '微信小程序', 'https://hamm.cn', 1, '', 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `sa_attach`
--

CREATE TABLE `sa_attach` (
  `attach_id` int(11) NOT NULL,
  `attach_path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `attach_used` int(11) NOT NULL DEFAULT '0',
  `attach_thumb` varchar(255) NOT NULL DEFAULT '',
  `attach_type` varchar(255) NOT NULL DEFAULT '' COMMENT '类型',
  `attach_sha` varchar(255) NOT NULL DEFAULT '',
  `attach_size` int(11) NOT NULL DEFAULT '0' COMMENT '大小',
  `attach_user` int(11) NOT NULL DEFAULT '0' COMMENT '用户',
  `attach_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `attach_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `attach_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件表';

-- --------------------------------------------------------

--
-- 表的结构 `sa_conf`
--

CREATE TABLE `sa_conf` (
  `conf_id` int(11) NOT NULL,
  `conf_key` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '参数名',
  `conf_value` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '参数值',
  `conf_desc` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '参数描述',
  `conf_int` int(11) NOT NULL DEFAULT '0' COMMENT '参数到期',
  `conf_status` int(11) NOT NULL DEFAULT '0',
  `conf_createtime` int(11) NOT NULL DEFAULT '0',
  `conf_updatetime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置表';

--
-- 转存表中的数据 `sa_conf`
--

INSERT INTO `sa_conf` (`conf_id`, `conf_key`, `conf_value`, `conf_desc`, `conf_int`, `conf_status`, `conf_createtime`, `conf_updatetime`) VALUES
(11, 'weapp_appid', '', '小程序APPID', 0, 0, 0, 0),
(12, 'weapp_appkey', '', '小程序SECRET', 0, 0, 0, 0),
(39, 'upload_max_file', '2097152', '最大文件上传限制', 0, 0, 0, 0),
(40, 'upload_file_type', 'jpg,png,gif,jpeg,bmp,txt,pdf,mp3,mp4,amr,m4a,xls,xlsx,ppt,pptx,doc,docx', '允许文件上传类型', 0, 0, 0, 0),
(41, 'upload_max_image', '2097152', '最大图片上传限制', 0, 0, 0, 0),
(42, 'upload_image_type', 'jpg,png,gif,jpeg,bmp', '允许上传图片类型', 0, 0, 0, 0),
(47, 'default_group', '5', '注册默认用户组', 0, 0, 0, 1598539052),
(48, 'email_account', 'admin@mail.bbbug.com', '邮箱账号', 0, 0, 0, 1598539052),
(49, 'email_password', '123456', '邮箱密码', 0, 0, 0, 1598539052),
(50, 'email_host', 'smtp.exmail.qq.com', '邮箱服务器', 0, 0, 0, 1598539052),
(51, 'email_remark', 'BBBUG TEAM', '邮箱签名', 0, 0, 0, 1598539052),
(52, 'email_port', '465', '邮箱端口号', 0, 0, 0, 1598539052),
(53, 'websocket_http', 'http://127.0.0.1:10012/', 'WebsocketHTTP请求地址', 0, 0, 0, 1598539052),
(54, 'websocket_token', 'wss_bbbug_com', 'Websocket验证串', 0, 0, 0, 1598539052),
(55, 'api_guest_token', '45af3cfe44942c956e026d5fd58f0feffbd3a237', '临时用户access_token', 0, 0, 0, 1598539052),
(56, 'frontend_url', '', '前端地址', 0, 0, 0, 0),
(57, 'api_url', '', 'API地址', 0, 0, 0, 0),
(58, 'tencent_ai_appid', '', '腾讯AI的APPID', 0, 0, 0, 1598539052),
(59, 'tencent_ai_appkey', '', '腾讯AI的APPKEY', 0, 0, 0, 1598539052),
(60, 'static_url', '', 'Static文件地址', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `sa_keywords`
--

CREATE TABLE `sa_keywords` (
  `keywords_id` int(11) NOT NULL,
  `keywords_source` varchar(255) NOT NULL DEFAULT '' COMMENT '原串',
  `keywords_target` varchar(255) NOT NULL DEFAULT '' COMMENT '替换',
  `keywords_all` int(11) NOT NULL DEFAULT '0' COMMENT '全替换',
  `keywords_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `keywords_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `keywords_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='关键词表';

--
-- 转存表中的数据 `sa_keywords`
--

INSERT INTO `sa_keywords` (`keywords_id`, `keywords_source`, `keywords_target`, `keywords_all`, `keywords_status`, `keywords_createtime`, `keywords_updatetime`) VALUES
(3, '<script>', '我傻乎乎的想试试能不能XSS，结果被系统拦截了。。。', 1, 0, 1592574791, 1592575266);

-- --------------------------------------------------------

--
-- 表的结构 `sa_message`
--

CREATE TABLE `sa_message` (
  `message_id` bigint(30) NOT NULL,
  `message_user` int(11) NOT NULL DEFAULT '0' COMMENT 'user',
  `message_type` varchar(255) NOT NULL DEFAULT '' COMMENT 'type',
  `message_where` varchar(255) NOT NULL DEFAULT '',
  `message_to` varchar(255) NOT NULL DEFAULT '',
  `message_content` text COMMENT 'content',
  `message_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `message_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `message_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息表';

-- --------------------------------------------------------

--
-- 表的结构 `sa_room`
--

CREATE TABLE `sa_room` (
  `room_id` int(11) NOT NULL,
  `room_user` int(11) NOT NULL DEFAULT '0' COMMENT '所有者ID',
  `room_addsongcd` int(11) NOT NULL DEFAULT '60' COMMENT '点歌CD',
  `room_addcount` int(5) NOT NULL DEFAULT '5' COMMENT '点歌数量',
  `room_pushdaycount` int(11) NOT NULL DEFAULT '5' COMMENT '顶歌日限额',
  `room_pushsongcd` int(11) NOT NULL DEFAULT '3600' COMMENT '顶歌CD',
  `room_online` int(11) NOT NULL DEFAULT '0' COMMENT '已登录在线',
  `room_realonline` int(11) NOT NULL DEFAULT '0' COMMENT '所有在线',
  `room_hide` int(1) NOT NULL DEFAULT '0' COMMENT '是否从列表隐藏',
  `room_name` varchar(255) NOT NULL DEFAULT '' COMMENT '房间名称',
  `room_type` int(11) NOT NULL DEFAULT '1' COMMENT '房间类型',
  `room_public` int(11) NOT NULL DEFAULT '0',
  `room_password` varchar(255) NOT NULL DEFAULT '' COMMENT '房间密码',
  `room_notice` text COMMENT '进入房间提醒',
  `room_addsong` int(11) NOT NULL DEFAULT '0',
  `room_sendmsg` int(11) NOT NULL DEFAULT '0',
  `room_robot` int(11) NOT NULL DEFAULT '0',
  `room_order` int(11) NOT NULL DEFAULT '0',
  `room_reason` varchar(255) NOT NULL DEFAULT '',
  `room_playone` int(11) NOT NULL DEFAULT '0' COMMENT '0随机1单曲',
  `room_votepass` int(11) NOT NULL DEFAULT '1',
  `room_votepercent` int(11) NOT NULL DEFAULT '30',
  `room_background` varchar(255) NOT NULL DEFAULT '' COMMENT '房间背景图',
  `room_app` varchar(255) NOT NULL DEFAULT '' COMMENT '插件地址',
  `room_fullpage` int(11) NOT NULL DEFAULT '0' COMMENT '插件是否全屏',
  `room_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `room_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `room_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='房间表';

--
-- 转存表中的数据 `sa_room`
--

INSERT INTO `sa_room` (`room_id`, `room_user`, `room_addsongcd`, `room_addcount`, `room_pushdaycount`, `room_pushsongcd`, `room_online`, `room_realonline`, `room_hide`, `room_name`, `room_type`, `room_public`, `room_password`, `room_notice`, `room_addsong`, `room_sendmsg`, `room_robot`, `room_order`, `room_reason`, `room_playone`, `room_votepass`, `room_votepercent`, `room_background`, `room_app`, `room_fullpage`, `room_status`, `room_createtime`, `room_updatetime`) VALUES
(888, 1, 60, 5, 5, 3600, 2, 5, 0, 'BBBUG音乐大厅', 1, 0, '', '大厅为电台播放模式，欢迎大家点歌，房间已支持自定义点歌/顶歌等CD和数量，快去房间管理页面看看吧~', 0, 0, 0, 10000000, '', 0, 1, 30, '', '', 0, 0, 1598539777, 1604990895);

-- --------------------------------------------------------

--
-- 表的结构 `sa_song`
--

CREATE TABLE `sa_song` (
  `song_id` int(11) NOT NULL,
  `song_user` int(11) NOT NULL DEFAULT '0',
  `song_mid` bigint(20) NOT NULL DEFAULT '0',
  `song_name` varchar(255) NOT NULL DEFAULT '' COMMENT '歌曲名称',
  `song_singer` varchar(255) NOT NULL DEFAULT '' COMMENT '歌手',
  `song_pic` varchar(255) NOT NULL DEFAULT '',
  `song_length` int(11) NOT NULL DEFAULT '0',
  `song_play` int(11) NOT NULL DEFAULT '1' COMMENT '被点次数',
  `song_week` int(9) NOT NULL DEFAULT '0' COMMENT '本周被点次数',
  `song_fav` int(11) NOT NULL DEFAULT '0' COMMENT '0点歌 1收藏',
  `song_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `song_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `song_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='歌曲表';

-- --------------------------------------------------------

--
-- 表的结构 `sa_user`
--

CREATE TABLE `sa_user` (
  `user_id` int(11) NOT NULL COMMENT 'UID',
  `user_icon` int(11) NOT NULL DEFAULT '0',
  `user_sex` int(2) NOT NULL DEFAULT '0',
  `user_account` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '帐号',
  `user_password` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '密码',
  `user_salt` varchar(4) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码盐',
  `user_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '用户昵称',
  `user_head` varchar(255) NOT NULL DEFAULT 'new/images/nohead.jpg',
  `user_remark` varchar(255) NOT NULL DEFAULT '每个人都应该有签名,但偏偏我没有.',
  `user_group` int(11) NOT NULL DEFAULT '0' COMMENT '用户组',
  `user_ipreg` varchar(255) NOT NULL COMMENT '注册IP',
  `user_openid` varchar(255) NOT NULL DEFAULT '',
  `user_extra` varchar(255) NOT NULL DEFAULT '',
  `user_app` int(11) NOT NULL DEFAULT '1',
  `user_device` varchar(255) NOT NULL DEFAULT '',
  `user_touchtip` varchar(255) NOT NULL DEFAULT '',
  `user_vip` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `user_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `user_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

--
-- 转存表中的数据 `sa_user`
--

INSERT INTO `sa_user` (`user_id`, `user_icon`, `user_sex`, `user_account`, `user_password`, `user_salt`, `user_name`, `user_head`, `user_remark`, `user_group`, `user_ipreg`, `user_openid`, `user_extra`, `user_app`, `user_device`, `user_touchtip`, `user_vip`, `user_status`, `user_createtime`, `user_updatetime`) VALUES
(1, 1, 0, 'admin@bbbug.com', '07014dde7319fa350b061bd5e37827a16b410791', 'zXZy', '%E6%9C%BA%E5%99%A8%E4%BA%BA', 'new/images/nohead.jpg', '别@我,我只是个测试号', 1, '127.0.0.1', '', '', 1, 'iPhone', '%EF%BC%8C%E6%9C%BA%E5%99%A8%E4%BA%BA%E5%B7%AE%E7%82%B9%E7%88%BD%E7%BF%BB%E5%A4%A9%E3%80%82', '', 0, 0, 1605004436);

-- --------------------------------------------------------

--
-- 表的结构 `sa_weapp`
--

CREATE TABLE `sa_weapp` (
  `weapp_id` int(11) NOT NULL,
  `weapp_openid` varchar(255) NOT NULL DEFAULT '' COMMENT 'OPENID',
  `weapp_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `weapp_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `weapp_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小程序用户表' ROW_FORMAT=COMPACT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sa_access`
--
ALTER TABLE `sa_access`
  ADD PRIMARY KEY (`access_id`) USING BTREE;

--
-- Indexes for table `sa_app`
--
ALTER TABLE `sa_app`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `sa_attach`
--
ALTER TABLE `sa_attach`
  ADD PRIMARY KEY (`attach_id`) USING BTREE;

--
-- Indexes for table `sa_conf`
--
ALTER TABLE `sa_conf`
  ADD PRIMARY KEY (`conf_id`) USING BTREE,
  ADD KEY `conf_key` (`conf_key`) USING BTREE;

--
-- Indexes for table `sa_keywords`
--
ALTER TABLE `sa_keywords`
  ADD PRIMARY KEY (`keywords_id`);

--
-- Indexes for table `sa_message`
--
ALTER TABLE `sa_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `message_user` (`message_user`),
  ADD KEY `message_createtime` (`message_createtime`);

--
-- Indexes for table `sa_room`
--
ALTER TABLE `sa_room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `sa_song`
--
ALTER TABLE `sa_song`
  ADD PRIMARY KEY (`song_id`),
  ADD KEY `song_mid` (`song_mid`) USING BTREE,
  ADD KEY `song_user` (`song_user`) USING BTREE;

--
-- Indexes for table `sa_user`
--
ALTER TABLE `sa_user`
  ADD PRIMARY KEY (`user_id`) USING BTREE,
  ADD KEY `admin_group` (`user_group`) USING BTREE,
  ADD KEY `admin_name` (`user_name`) USING BTREE,
  ADD KEY `admin_password` (`user_password`) USING BTREE,
  ADD KEY `admin_account` (`user_account`) USING BTREE;

--
-- Indexes for table `sa_weapp`
--
ALTER TABLE `sa_weapp`
  ADD PRIMARY KEY (`weapp_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `sa_access`
--
ALTER TABLE `sa_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_app`
--
ALTER TABLE `sa_app`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;
--
-- 使用表AUTO_INCREMENT `sa_attach`
--
ALTER TABLE `sa_attach`
  MODIFY `attach_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_conf`
--
ALTER TABLE `sa_conf`
  MODIFY `conf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
--
-- 使用表AUTO_INCREMENT `sa_keywords`
--
ALTER TABLE `sa_keywords`
  MODIFY `keywords_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- 使用表AUTO_INCREMENT `sa_message`
--
ALTER TABLE `sa_message`
  MODIFY `message_id` bigint(30) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_room`
--
ALTER TABLE `sa_room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=889;
--
-- 使用表AUTO_INCREMENT `sa_song`
--
ALTER TABLE `sa_song`
  MODIFY `song_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_user`
--
ALTER TABLE `sa_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'UID', AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `sa_weapp`
--
ALTER TABLE `sa_weapp`
  MODIFY `weapp_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
