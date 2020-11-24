-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2020-11-24 10:06:54
-- 服务器版本： 5.6.48-log
-- PHP Version: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `b`
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
(1004, '请重置后对接', '钉钉', 'https://hamm.cn', 1, '', 0, 0, 0);

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
-- 表的结构 `sa_auth`
--

CREATE TABLE `sa_auth` (
  `auth_id` bigint(20) NOT NULL COMMENT '权限ID',
  `auth_group` int(11) NOT NULL DEFAULT '0' COMMENT '权限管理组',
  `auth_node` int(11) NOT NULL DEFAULT '0' COMMENT '功能ID',
  `auth_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `auth_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `auth_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

--
-- 转存表中的数据 `sa_auth`
--

INSERT INTO `sa_auth` (`auth_id`, `auth_group`, `auth_node`, `auth_status`, `auth_createtime`, `auth_updatetime`) VALUES
(36, 5, 1012, 0, 1598545110, 1598545110),
(37, 5, 1128, 0, 1598545110, 1598545110),
(38, 5, 1134, 0, 1598545110, 1598545110),
(39, 5, 1136, 0, 1598545110, 1598545110),
(40, 5, 1137, 0, 1598545110, 1598545110),
(41, 5, 1138, 0, 1598545110, 1598545110),
(42, 5, 1127, 0, 1598545110, 1598545110),
(43, 5, 1148, 0, 1598545110, 1598545110),
(44, 5, 1147, 0, 1598545110, 1598545110),
(45, 5, 1077, 0, 1598545110, 1598545110),
(46, 5, 1013, 0, 1598545110, 1598545110);

-- --------------------------------------------------------

--
-- 表的结构 `sa_code`
--

CREATE TABLE `sa_code` (
  `code_id` int(11) NOT NULL,
  `code_user` int(11) NOT NULL DEFAULT '0' COMMENT 'user',
  `code_code` varchar(255) NOT NULL DEFAULT '' COMMENT 'code',
  `code_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `code_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `code_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='临时凭证表';

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
(1, 'wechat_appid', '', '微信ID', 0, 0, 0, 0),
(2, 'wechat_appkey', '', '微信密钥', 0, 0, 0, 0),
(3, 'wechat_token', 'StartAdmin', '微信TOKEN', 0, 0, 0, 1585844226),
(4, 'wechat_aes_key', 'StartAdmin', '微信AES密钥', 0, 0, 0, 1585844226),
(11, 'weapp_appid', '', '小程序APPID', 0, 0, 0, 0),
(12, 'weapp_appkey', '', '小程序SECRET', 0, 0, 0, 0),
(39, 'upload_max_file', '2097152', '最大文件上传限制', 0, 0, 0, 0),
(40, 'upload_file_type', 'jpg,png,gif,jpeg,bmp,txt,pdf,mp3,mp4,amr,m4a,xls,xlsx,ppt,pptx,doc,docx', '允许文件上传类型', 0, 0, 0, 0),
(41, 'upload_max_image', '2097152', '最大图片上传限制', 0, 0, 0, 0),
(42, 'upload_image_type', 'jpg,png,gif,jpeg,bmp', '允许上传图片类型', 0, 0, 0, 0),
(43, 'alisms_appkey', '', '阿里云短信key', 0, 0, 0, 0),
(44, 'alisms_appid', '', '阿里云短信ID', 0, 0, 0, 0),
(45, 'alisms_sign', '', '阿里云短信签名', 0, 0, 0, 0),
(46, 'alisms_template', '', '阿里云短信模板', 0, 0, 0, 0),
(47, 'default_group', '5', '注册默认用户组', 0, 0, 0, 1598539052),
(48, 'email_account', 'admin@mail.bbbug.com', '邮箱账号', 0, 0, 0, 1598539052),
(49, 'email_password', '123456', '邮箱密码', 0, 0, 0, 1598539052),
(50, 'email_host', 'smtp.exmail.qq.com', '邮箱服务器', 0, 0, 0, 1598539052),
(51, 'email_remark', 'BBBUG TEAM', '邮箱签名', 0, 0, 0, 1598539052),
(52, 'email_port', '465', '邮箱端口号', 0, 0, 0, 1598539052),
(53, 'websocket_http', 'http://127.0.0.1:10012/', 'WebsocketHTTP请求地址', 0, 0, 0, 1598539052),
(54, 'websocket_token', 'wss_bbbug_com', 'Websocket验证串', 0, 0, 0, 1598539052),
(55, 'api_guest_token', '45af3cfe44942c956e026d5fd58f0feffbd3a237', '临时用户access_token', 0, 0, 0, 1598539052);

-- --------------------------------------------------------

--
-- 表的结构 `sa_group`
--

CREATE TABLE `sa_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '管理组名称',
  `group_desc` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '管理组描述',
  `group_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `group_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `group_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理组表';

--
-- 转存表中的数据 `sa_group`
--

INSERT INTO `sa_group` (`group_id`, `group_name`, `group_desc`, `group_status`, `group_createtime`, `group_updatetime`) VALUES
(1, '超级管理员', '不允许删除', 0, 0, 1575903468),
(5, '普通用户', '', 0, 1598539040, 1598539040);

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
(3, '<script>', '我傻乎乎的想试试能不能XSS，结果被系统拦截了。。。', 1, 0, 1592574791, 1592575266),
(6, '习近平', '习近平，男，汉族，1953年6月生，陕西富平人，1969年1月参加工作，1974年1月加入中国共产党，清华大学人文社会学院马克思主义理论与思想政治教育专业毕业，在职研究生学历，法学博士学位。 现任中国共产党中央委员会总书记，中共中央军事委员会主席，中华人民共和国主席，中华人民共和国中央军事委员会主席。', 1, 0, 1596161752, 1598412447),
(7, 'whoami', '你好，我是一个自己都不知道自己是谁的憨批。', 1, 0, 1597841138, 1597841138),
(8, 'rm -rf', '恭喜你，删除服务器所有文件成功!', 1, 0, 1597841200, 1597841200),
(9, '共产党', '中国共产党万岁！', 1, 0, 1597925397, 1597925397),
(11, '李克强', '李克强，男，汉族，1955年7月生，安徽定远人，1974年3月参加工作，1976年5月加入中国共产党，北京大学法律系和经济学院经济学专业毕业，在职研究生学历，法学学士、经济学博士学位。 现任中共十九届中央政治局常委，国务院总理、党组书记', 1, 0, 1598412416, 1598412416),
(12, '特朗普', '川建国是中国最好的朋友。', 1, 0, 1598412473, 1598412473),
(13, 'mkdir', '创建文件夹成功', 1, 0, 1597841200, 1597841200),
(15, 'reboot', '服务器重启中,请稍候..', 1, 0, 1597841200, 1597841200),
(16, '金三胖', '鑫胖是谁？', 1, 0, 1597841200, 1597841200),
(17, '旺仔', '我的蛋蛋', 0, 1, 1597841200, 1597841200),
(19, '咖啡', '1982年川建国埋在白宫地下通道口垃圾桶旁被人吐过口水的咖啡。', 0, 1, 1597841200, 1597841200),
(20, '黄色', '《JavaScript从入门到放弃》', 0, 0, 1597841200, 1597841200),
(21, '可乐', '1982年川建国埋在白宫地下通道口垃圾桶旁被人吐过口水的可乐。', 0, 1, 1597841200, 1597841200),
(22, 'ghs', '学习大数据知识', 0, 0, 1597841200, 1597841200),
(23, '嫖娼', '学习如何快速入门Java', 0, 0, 1597841200, 1597841200),
(24, 'HS', '学习如何快速入门Java', 0, 0, 1597841200, 1597841200),
(25, '打飞机', '学习如何快速入门JavaScript', 0, 0, 1597841200, 1597841200),
(26, '黄片', '《青少年如何自律》', 0, 0, 1597841200, 1597841200),
(27, '嫖', '谈如何尊重', 0, 0, 1597841200, 1597841200),
(28, '撸管', '薅羊毛', 0, 0, 1597841200, 1597841200),
(30, '打手枪', '玩射击', 0, 0, 1597841200, 1597841200),
(31, '鸡巴', '一只大公鸡', 0, 0, 1597841200, 1597841200),
(32, 'pornhub', 'bbbug', 0, 0, 1597841200, 1597841200),
(33, 'porn', 'git', 0, 0, 1597841200, 1597841200),
(34, '操你', '', 0, 0, 1597841200, 1597841200),
(35, '你妈', '好牛逼啊！', 1, 0, 1603956336, 1603956360);

-- --------------------------------------------------------

--
-- 表的结构 `sa_log`
--

CREATE TABLE `sa_log` (
  `log_id` int(11) NOT NULL COMMENT '操作ID',
  `log_user` int(11) NOT NULL COMMENT '用户UID',
  `log_gets` text CHARACTER SET utf8 COMMENT 'GET参数',
  `log_posts` text CHARACTER SET utf8 COMMENT 'POST参数',
  `log_cookies` text CHARACTER SET utf8 COMMENT 'Cookies数据',
  `log_node` int(11) NOT NULL COMMENT '节点ID',
  `log_ip` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'IP地址',
  `log_os` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '操作系统',
  `log_browser` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '浏览器',
  `log_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `log_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `log_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='访问记录表';

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
-- 表的结构 `sa_node`
--

CREATE TABLE `sa_node` (
  `node_id` int(11) NOT NULL COMMENT '功能ID',
  `node_title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '功能名称',
  `node_desc` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '功能描述',
  `node_module` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'api' COMMENT '模块',
  `node_controller` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '控制器',
  `node_action` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '方法',
  `node_pid` int(11) NOT NULL DEFAULT '0' COMMENT '父ID',
  `node_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序ID',
  `node_show` int(11) NOT NULL DEFAULT '1' COMMENT '1显示到菜单',
  `node_icon` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '图标',
  `node_extend` text CHARACTER SET utf8 COMMENT '扩展数据',
  `node_login` int(11) NOT NULL DEFAULT '1' COMMENT '是否需要登录',
  `node_access` int(11) NOT NULL DEFAULT '1' COMMENT '是否校验权限',
  `node_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `node_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `node_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='功能节点表';

--
-- 转存表中的数据 `sa_node`
--

INSERT INTO `sa_node` (`node_id`, `node_title`, `node_desc`, `node_module`, `node_controller`, `node_action`, `node_pid`, `node_order`, `node_show`, `node_icon`, `node_extend`, `node_login`, `node_access`, `node_status`, `node_createtime`, `node_updatetime`) VALUES
(1, '管理首页', '', 'admin', 'index', 'index', 0, 0, 1, 'shouye', NULL, 1, 1, 0, 0, 1585131318),
(2, '用户管理', '', 'admin', '', '', 0, 0, 1, 'haoyouliebiao', NULL, 1, 1, 0, 0, 1575948484),
(3, '系统设置', '', 'admin', '', '', 0, 0, 1, 'shezhi', NULL, 1, 1, 0, 0, 1575948484),
(4, '接口列表', '', 'api', '', '', 0, 0, 0, '', NULL, 1, 1, 0, 0, 1576045995),
(5, '数据日志', '', 'admin', '', '', 0, 0, 1, 'book', NULL, 1, 1, 0, 0, 1575948636),
(6, '微信管理', '', 'admin', '', '', 0, 0, 1, 'wechat', NULL, 1, 1, 0, 1585323009, 1585323009),
(100, '用户管理', '', 'admin', 'user', 'index', 2, 0, 1, '', '', 1, 1, 0, 0, 1575948484),
(101, '用户组管理', '', 'admin', 'group', 'index', 2, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(102, '系统配置', '', 'admin', 'conf', 'index', 3, 0, 1, '', '', 1, 1, 0, 0, 1575960614),
(104, '节点管理', '', 'admin', 'node', 'index', 3, 0, 1, '', '', 1, 1, 0, 0, 1575948484),
(105, '附件管理', '', 'admin', 'attach', 'index', 3, 0, 1, '', '', 1, 1, 0, 0, 1575948484),
(106, '清理数据', '', 'admin', 'system', 'clean', 5, 0, 1, '', '', 1, 1, 0, 0, 1575984190),
(107, '代码生成', '', 'admin', 'system', 'build', 3, 0, 1, '', '', 1, 1, 0, 0, 1575948484),
(108, '基础设置', '', 'admin', 'conf', 'base', 3, 0, 1, '', '', 1, 1, 0, 0, 1575948484),
(109, '访问日志', '', 'admin', 'log', 'index', 5, 0, 1, '', '', 1, 1, 0, 0, 1575984177),
(110, '访问统计', '', 'admin', 'log', 'state', 5, 0, 1, '', '', 1, 1, 0, 0, 1575984183),
(111, '微信菜单管理', '', 'admin', 'wemenu', 'index', 6, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(113, '微信粉丝管理', '', 'admin', 'wechat', 'index', 6, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(114, '小程序用户管理', '', 'admin', 'weapp', 'index', 6, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1000, '获取用户列表接口', '', 'api', 'user', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1001, '获取用户组列表接口', '', 'api', 'group', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1003, '获取所有配置列表接口', '', 'api', 'conf', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1005, '获取节点列表接口', '', 'api', 'node', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1006, '获取用户详细信息接口', '', 'api', 'user', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1007, '添加用户接口', '', 'api', 'user', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1008, '修改用户接口', '', 'api', 'user', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1009, '禁用用户接口', '', 'api', 'user', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1010, '启用用户接口', '', 'api', 'user', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1011, '删除用户接口', '', 'api', 'user', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1012, '获取我的资料接口', '', 'api', 'user', 'getmyinfo', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1013, '修改我的资料接口', '', 'api', 'user', 'updatemyinfo', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1014, '添加用户组接口', '', 'api', 'group', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1015, '获取用户组信息接口', '', 'api', 'group', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1016, '修改用户组信息接口', '', 'api', 'group', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1017, '禁用用户组接口', '', 'api', 'group', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1018, '启用用户组接口', '', 'api', 'group', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1019, '删除用户组接口', '', 'api', 'group', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1020, '设置用户组的权限', '', 'api', 'group', 'authorize', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1021, '获取用户组的权限', '', 'api', 'group', 'getauthorize', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1022, '禁用节点接口', '', 'api', 'node', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1023, '启用节点接口', '', 'api', 'node', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1024, '删除节点接口', '', 'api', 'node', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1026, '显示节点到菜单接口', '', 'api', 'node', 'show_menu', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1027, '隐藏节点到菜单接口', '', 'api', 'node', 'hide_menu', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1028, '获取节点信息接口', '', 'api', 'node', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1029, '修改节点信息接口', '', 'api', 'node', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1030, '添加节点信息接口', '', 'api', 'node', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1031, '节点导入接口', '', 'api', 'node', 'import', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1038, '修改我的密码接口', '', 'api', 'user', 'motifypassword', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1040, '微信小程序登录接口', '', 'api', 'user', 'wxapplogin', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1041, '微信小程序手机号解密接口', '', 'api', 'weapp', 'wxphonedecodelogin', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1042, '添加配置接口', '', 'api', 'conf', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1043, '修改配置接口', '', 'api', 'conf', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1044, '获取配置信息接口', '', 'api', 'conf', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1045, '删除配置信息接口', '', 'api', 'conf', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1046, '获取附件列表接口', '', 'api', 'attach', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1047, '上传文件接口', '', 'api', 'attach', 'uploadfile', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1048, '删除附件接口', '', 'api', 'attach', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1049, '清空授权信息接口', '', 'api', 'auth', 'clean', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1050, '清空访问日志接口', '', 'api', 'log', 'clean', 4, 0, 1, '', NULL, 1, 1, 0, 1575948342, 1575948484),
(1052, '代码生成接口', '', 'api', 'system', 'build', 4, 0, 1, '', '', 1, 1, 0, 0, 1575948484),
(1074, '获取基础设置接口', '', 'api', 'conf', 'getBaseConfig', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1075, '修改基础设置接口', '', 'api', 'conf', 'updateBaseConfig', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1077, '上传图片接口', '', 'api', 'attach', 'uploadimage', 4, 0, 1, '', '', 1, 1, 0, 1575981672, 1575981701),
(1078, '获取访问统计数据接口', '', 'api', 'log', 'state', 4, 0, 1, '', NULL, 1, 1, 0, 1575981672, 1575981672),
(1079, '获取日志列表接口', '', 'api', 'log', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1575981672, 1575981672),
(1080, '删除日志接口', '', 'api', 'log', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1575981672, 1575981672),
(1081, '导出节点接口', '', 'api', 'node', 'excel', 4, 0, 1, '', NULL, 1, 1, 0, 1575981672, 1575981672),
(1082, '导出日志接口', '', 'api', 'log', 'excel', 4, 0, 1, '', NULL, 1, 1, 0, 1575981672, 1575981672),
(1091, '获取微信菜单详情接口', '', 'api', 'wemenu', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1092, '添加微信菜单接口', '', 'api', 'wemenu', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1093, '修改微信菜单接口', '', 'api', 'wemenu', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1094, '删除微信菜单接口', '', 'api', 'wemenu', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1095, '禁用微信菜单接口', '', 'api', 'wemenu', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1096, '启用微信菜单接口', '', 'api', 'wemenu', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1097, '获取微信菜单列表接口', '', 'api', 'wemenu', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1100, '微信发布自定义菜单接口', '', 'api', 'wemenu', 'publish', 4, 0, 1, '', NULL, 1, 1, 0, 1585323009, 1585323009),
(1101, '获取微信粉丝列表接口', '', 'api', 'wechat', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1102, '禁用微信粉丝接口', '', 'api', 'wechat', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1103, '启用微信粉丝接口', '', 'api', 'wechat', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1104, '用户导出Excel接口', '', 'api', 'user', 'excel', 4, 0, 1, '', NULL, 1, 1, 0, 0, 1575948484),
(1113, '获取小程序用户详情接口', '', 'api', 'weapp', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1114, '添加小程序用户接口', '', 'api', 'weapp', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1115, '修改小程序用户接口', '', 'api', 'weapp', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1116, '删除小程序用户接口', '', 'api', 'weapp', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1117, '禁用小程序用户接口', '', 'api', 'weapp', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1118, '启用小程序用户接口', '', 'api', 'weapp', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1119, '获取小程序用户列表接口', '', 'api', 'weapp', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1585854558, 1585854558),
(1120, '用户登录接口', '', 'api', 'user', 'login', 4, 0, 1, '', NULL, 0, 0, 0, 1585854558, 1585854558),
(1121, '用户注册接口', '', 'api', 'user', 'reg', 4, 0, 1, '', NULL, 0, 0, 0, 1585854558, 1585854558),
(1122, '找回密码接口', '', 'api', 'user', 'resetPassword', 4, 0, 1, '', NULL, 0, 0, 0, 1585854558, 1585854558),
(1123, '发送验证码接口', '', 'api', 'sms', 'send', 4, 0, 1, '', NULL, 0, 0, 0, 1585854558, 1585854558),
(1124, '获取图形验证码', '', 'api', 'system', 'getCaptcha', 4, 0, 1, '', NULL, 0, 0, 0, 1585854558, 1585854558),
(1125, '微信小程序登录接口', '', 'api', 'weapp', 'wxAppLogin', 4, 0, 1, '', NULL, 0, 0, 0, 1585854558, 1585854558),
(1126, '聊天室', '', 'admin', 'index', 'index', 0, 1, 1, 'user', NULL, 0, 0, 0, 1598493553, 1598493565),
(1127, '接口', '', 'api', 'index', 'index', 0, 1, 0, '', NULL, 1, 1, 0, 1598493588, 1598949783),
(1128, '获取房间详情接口', '', 'api', 'room', 'detail', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1129, '添加房间接口', '', 'api', 'room', 'add', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1130, '修改房间接口', '', 'api', 'room', 'update', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1131, '删除房间接口', '', 'api', 'room', 'delete', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1132, '禁用房间接口', '', 'api', 'room', 'disable', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1133, '启用房间接口', '', 'api', 'room', 'enable', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1134, '获取房间列表接口', '', 'api', 'room', 'getList', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1135, '房间管理', '', 'admin', 'room', 'index', 0, 0, 0, '', NULL, 1, 1, 0, 1598497409, 1598949792),
(1136, '创建房间接口', '', 'api', 'room', 'create', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1137, '获取热门房间列表接口', '', 'api', 'room', 'hotrooms', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1138, '获取房间Websocket地址接口', '', 'api', 'room', 'getwebsocketurl', 1127, 0, 1, '', NULL, 1, 1, 0, 1598497409, 1598497409),
(1139, '获取消息详情接口', '', 'api', 'message', 'detail', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1140, '添加消息接口', '', 'api', 'message', 'add', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1141, '修改消息接口', '', 'api', 'message', 'update', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1142, '删除消息接口', '', 'api', 'message', 'delete', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1143, '禁用消息接口', '', 'api', 'message', 'disable', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1144, '启用消息接口', '', 'api', 'message', 'enable', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1145, '获取消息列表接口', '', 'api', 'message', 'getList', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1146, '消息管理', '', 'admin', 'message', 'index', 0, 0, 0, '', NULL, 1, 1, 0, 1598518395, 1598949793),
(1147, '发送消息接口', '', 'api', 'message', 'send', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1148, '获取房间信息接口', '', 'api', 'room', 'getRoomInfo', 1127, 0, 1, '', NULL, 1, 1, 0, 1598518395, 1598518395),
(1149, '获取歌曲详情接口', '', 'api', 'song', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1150, '添加歌曲接口', '', 'api', 'song', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1151, '修改歌曲接口', '', 'api', 'song', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1152, '删除歌曲接口', '', 'api', 'song', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1153, '禁用歌曲接口', '', 'api', 'song', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1154, '启用歌曲接口', '', 'api', 'song', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1155, '获取歌曲列表接口', '', 'api', 'song', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1598578605, 1598578605),
(1156, '歌曲管理', '', 'admin', 'song', 'index', 0, 0, 0, '', NULL, 1, 1, 0, 1598578605, 1598949794),
(1157, '点歌接口', '', 'api', 'song', 'addsong', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949757),
(1158, '顶歌接口', '', 'api', 'song', 'push', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949758),
(1159, '切歌接口', '', 'api', 'song', 'pass', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949760),
(1160, '保存我的房间接口', '', 'api', 'room', 'saveMyRoom', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949761),
(1161, '撤回消息接口', '', 'api', 'message', 'back', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949762),
(1162, '禁止发言接口', '', 'api', 'user', 'shutdown', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949763),
(1163, '禁止点歌接口', '', 'api', 'user', 'songdown', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949766),
(1164, '解禁接口', '', 'api', 'user', 'removeban', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949765),
(1165, '获取应用详情接口', '', 'api', 'app', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1598837113, 1598837113),
(1166, '添加应用接口', '', 'api', 'app', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1598837113, 1598837113),
(1167, '修改应用接口', '', 'api', 'app', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1598837113, 1598837113),
(1168, '删除应用接口', '', 'api', 'app', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1598837113, 1598837113),
(1169, '禁用应用接口', '', 'api', 'app', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1598837113, 1598837113),
(1170, '启用应用接口', '', 'api', 'app', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1598837114, 1598837114),
(1171, '获取应用列表接口', '', 'api', 'app', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1598837114, 1598837114),
(1172, '应用管理', '', 'admin', 'app', 'index', 0, 0, 0, '', NULL, 1, 1, 0, 1598837114, 1598949795),
(1173, 'PASS游戏接口', '', 'api', 'song', 'gamepass', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949767),
(1174, '关键词管理', '', 'admin', 'keywords', 'index', 1126, 0, 1, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1175, '获取关键词列表接口', '', 'api', 'keywords', 'getList', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1176, '启用关键词接口', '', 'api', 'keywords', 'enable', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1177, '禁用关键词接口', '', 'api', 'keywords', 'disable', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1178, '删除关键词接口', '', 'api', 'keywords', 'delete', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1179, '修改关键词接口', '', 'api', 'keywords', 'update', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1180, '添加关键词接口', '', 'api', 'keywords', 'add', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1181, '获取关键词详情接口', '', 'api', 'keywords', 'detail', 1126, 0, 0, '', NULL, 1, 1, 0, 1592573786, 1592573786),
(1182, '顶歌接口', '', 'api', 'song', 'remove', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949758),
(1183, '获取视频详情接口', '', 'api', 'video', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1184, '添加视频接口', '', 'api', 'video', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1185, '修改视频接口', '', 'api', 'video', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1186, '删除视频接口', '', 'api', 'video', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1187, '禁用视频接口', '', 'api', 'video', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1188, '启用视频接口', '', 'api', 'video', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1189, '获取视频列表接口', '', 'api', 'video', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1190, '视频管理', '', 'admin', 'video', 'index', 0, 0, 1, '', NULL, 1, 1, 0, 1599117239, 1599117239),
(1191, '我点的歌接口', '', 'api', 'song', 'mysonglist', 1126, 1, 0, '', NULL, 1, 0, 0, 1598580603, 1598949767),
(1192, '上传头像接口', '', 'api', 'attach', 'uploadhead', 4, 0, 1, '', '', 1, 0, 0, 1575981672, 1575981701),
(1193, '获取宠物详情接口', '', 'api', 'pet', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1194, '添加宠物接口', '', 'api', 'pet', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1195, '修改宠物接口', '', 'api', 'pet', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1196, '删除宠物接口', '', 'api', 'pet', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1197, '禁用宠物接口', '', 'api', 'pet', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1198, '启用宠物接口', '', 'api', 'pet', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1199, '获取宠物列表接口', '', 'api', 'pet', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1200, '宠物管理', '', 'admin', 'pet', 'index', 0, 0, 1, '', NULL, 1, 1, 0, 1599384913, 1599384913),
(1201, '获取故事详情接口', '', 'api', 'story', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1202, '添加故事接口', '', 'api', 'story', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1203, '修改故事接口', '', 'api', 'story', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1204, '删除故事接口', '', 'api', 'story', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1205, '禁用故事接口', '', 'api', 'story', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1206, '启用故事接口', '', 'api', 'story', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1207, '获取故事列表接口', '', 'api', 'story', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1208, '故事管理', '', 'admin', 'story', 'index', 0, 0, 1, '', NULL, 1, 1, 0, 1599746656, 1599746656),
(1209, '播放故事', '', 'api', 'story', 'playStory', 0, 0, 1, '', NULL, 1, 0, 0, 1599746656, 1599746656),
(1210, '播放歌曲', '', 'api', 'song', 'playSong', 0, 0, 1, '', NULL, 1, 0, 0, 1599746656, 1599746656),
(1211, '删除歌曲', '', 'api', 'song', 'deleteMySong', 0, 0, 1, '', NULL, 1, 0, 0, 1599746656, 1599746656),
(1212, '获取在线详情接口', '', 'api', 'online', 'detail', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1213, '添加在线接口', '', 'api', 'online', 'add', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1214, '修改在线接口', '', 'api', 'online', 'update', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1215, '删除在线接口', '', 'api', 'online', 'delete', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1216, '禁用在线接口', '', 'api', 'online', 'disable', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1217, '启用在线接口', '', 'api', 'online', 'enable', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1218, '获取在线列表接口', '', 'api', 'online', 'getList', 4, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1219, '在线管理', '', 'admin', 'online', 'index', 0, 0, 1, '', NULL, 1, 1, 0, 1600668831, 1600668831),
(1220, '获取消息历史接口', '', 'api', 'message', 'getmessagelist', 0, 0, 1, '', NULL, 1, 0, 0, 1600668831, 1600668831),
(1221, '搜索歌曲接口', '', 'api', 'song', 'search', 0, 0, 1, '', NULL, 1, 0, 0, 1600668831, 1600668831),
(1222, '进入房间服务器接口', '', 'api', 'room', 'joinroom', 0, 0, 1, '', NULL, 1, 0, 0, 1600668831, 1600668831),
(1223, '删除房间聊天记录接口', '', 'api', 'message', 'clear', 0, 0, 1, '', NULL, 1, 0, 0, 1600668831, 1600668831),
(1224, '摸一摸接口', '', 'api', 'message', 'touch', 0, 0, 1, '', NULL, 1, 0, 0, 1600668831, 1600668831),
(1225, '搜藏歌曲到我的歌单', '', 'api', 'song', 'addmysong', 0, 0, 1, '', NULL, 1, 0, 0, 1600668831, 1600668831),
(1227, '域名查询房间接口', '', 'api', 'room', 'getRoomByDomain', 0, 0, 1, '', NULL, 1, 1, 0, 0, 0);

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
  `room_online` int(11) NOT NULL DEFAULT '0',
  `room_realonline` int(11) NOT NULL DEFAULT '0',
  `room_hide` int(1) NOT NULL DEFAULT '0' COMMENT '是否从列表隐藏',
  `room_name` varchar(255) NOT NULL DEFAULT '' COMMENT '房间名称',
  `room_type` int(11) NOT NULL DEFAULT '1' COMMENT '房间类型',
  `room_public` int(11) NOT NULL DEFAULT '0',
  `room_password` varchar(8) NOT NULL DEFAULT '' COMMENT '房间密码',
  `room_notice` varchar(255) NOT NULL DEFAULT '' COMMENT '进入房间提醒',
  `room_addsong` int(11) NOT NULL DEFAULT '0',
  `room_sendmsg` int(11) NOT NULL DEFAULT '0',
  `room_robot` int(11) NOT NULL DEFAULT '0',
  `room_order` int(11) NOT NULL DEFAULT '0',
  `room_reason` varchar(255) NOT NULL DEFAULT '',
  `room_playone` int(11) NOT NULL DEFAULT '0' COMMENT '0随机1单曲',
  `room_votepass` int(11) NOT NULL DEFAULT '1',
  `room_votepercent` int(11) NOT NULL DEFAULT '30',
  `room_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `room_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `room_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='房间表';

--
-- 转存表中的数据 `sa_room`
--

INSERT INTO `sa_room` (`room_id`, `room_user`, `room_addsongcd`, `room_addcount`, `room_pushdaycount`, `room_pushsongcd`, `room_online`, `room_realonline`, `room_hide`, `room_name`, `room_type`, `room_public`, `room_password`, `room_notice`, `room_addsong`, `room_sendmsg`, `room_robot`, `room_order`, `room_reason`, `room_playone`, `room_votepass`, `room_votepercent`, `room_status`, `room_createtime`, `room_updatetime`) VALUES
(888, 1, 60, 5, 5, 3600, 2, 5, 0, 'BBBUG音乐大厅', 1, 0, '', '大厅为电台播放模式，欢迎大家点歌，房间已支持自定义点歌/顶歌等CD和数量，快去房间管理页面看看吧~', 0, 0, 0, 10000000, '', 0, 1, 30, 0, 1598539777, 1604990895);

-- --------------------------------------------------------

--
-- 表的结构 `sa_song`
--

CREATE TABLE `sa_song` (
  `song_id` int(11) NOT NULL,
  `song_user` int(11) NOT NULL DEFAULT '0',
  `song_mid` int(11) NOT NULL DEFAULT '0',
  `song_name` varchar(255) NOT NULL DEFAULT '' COMMENT '歌曲名称',
  `song_singer` varchar(255) NOT NULL DEFAULT '' COMMENT '歌手',
  `song_pic` varchar(255) NOT NULL DEFAULT '',
  `song_length` int(11) NOT NULL DEFAULT '0',
  `song_play` int(11) NOT NULL DEFAULT '1' COMMENT '被点次数',
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
  `user_head` varchar(255) NOT NULL DEFAULT 'https://cdn.bbbug.com/images/nohead.jpg',
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
(1, 1, 0, 'admin@bbbug.com', '123456', 'abcd', '%E6%9C%BA%E5%99%A8%E4%BA%BA', 'https://cdn.bbbug.com/uploads/thumb/image/20201016/2a4a54f2a696179a963bbf1cb4426cb7.jpg', '别@我,我只是个测试号', 1, '127.0.0.1', '', '', 1, 'iPhone', '%EF%BC%8C%E6%9C%BA%E5%99%A8%E4%BA%BA%E5%B7%AE%E7%82%B9%E7%88%BD%E7%BF%BB%E5%A4%A9%E3%80%82', '', 0, 0, 1605004436);

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
-- Indexes for table `sa_auth`
--
ALTER TABLE `sa_auth`
  ADD PRIMARY KEY (`auth_id`) USING BTREE,
  ADD KEY `role_group` (`auth_group`) USING BTREE,
  ADD KEY `role_auth` (`auth_node`) USING BTREE;

--
-- Indexes for table `sa_code`
--
ALTER TABLE `sa_code`
  ADD PRIMARY KEY (`code_id`) USING BTREE;

--
-- Indexes for table `sa_conf`
--
ALTER TABLE `sa_conf`
  ADD PRIMARY KEY (`conf_id`) USING BTREE,
  ADD KEY `conf_key` (`conf_key`) USING BTREE;

--
-- Indexes for table `sa_group`
--
ALTER TABLE `sa_group`
  ADD PRIMARY KEY (`group_id`) USING BTREE;

--
-- Indexes for table `sa_keywords`
--
ALTER TABLE `sa_keywords`
  ADD PRIMARY KEY (`keywords_id`);

--
-- Indexes for table `sa_log`
--
ALTER TABLE `sa_log`
  ADD PRIMARY KEY (`log_id`) USING BTREE,
  ADD KEY `log_user` (`log_user`) USING BTREE,
  ADD KEY `log_node` (`log_node`) USING BTREE;

--
-- Indexes for table `sa_message`
--
ALTER TABLE `sa_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `message_user` (`message_user`),
  ADD KEY `message_createtime` (`message_createtime`);

--
-- Indexes for table `sa_node`
--
ALTER TABLE `sa_node`
  ADD PRIMARY KEY (`node_id`) USING BTREE,
  ADD KEY `auth_pid` (`node_pid`) USING BTREE,
  ADD KEY `node_module` (`node_module`) USING BTREE,
  ADD KEY `node_controller` (`node_controller`) USING BTREE,
  ADD KEY `node_action` (`node_action`) USING BTREE;

--
-- Indexes for table `sa_room`
--
ALTER TABLE `sa_room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `sa_song`
--
ALTER TABLE `sa_song`
  ADD PRIMARY KEY (`song_id`);

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
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1005;
--
-- 使用表AUTO_INCREMENT `sa_attach`
--
ALTER TABLE `sa_attach`
  MODIFY `attach_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_auth`
--
ALTER TABLE `sa_auth`
  MODIFY `auth_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '权限ID', AUTO_INCREMENT=47;
--
-- 使用表AUTO_INCREMENT `sa_code`
--
ALTER TABLE `sa_code`
  MODIFY `code_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_conf`
--
ALTER TABLE `sa_conf`
  MODIFY `conf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
--
-- 使用表AUTO_INCREMENT `sa_group`
--
ALTER TABLE `sa_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `sa_keywords`
--
ALTER TABLE `sa_keywords`
  MODIFY `keywords_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- 使用表AUTO_INCREMENT `sa_log`
--
ALTER TABLE `sa_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '操作ID';
--
-- 使用表AUTO_INCREMENT `sa_message`
--
ALTER TABLE `sa_message`
  MODIFY `message_id` bigint(30) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `sa_node`
--
ALTER TABLE `sa_node`
  MODIFY `node_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '功能ID', AUTO_INCREMENT=1228;
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
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
