/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : url

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2020-02-19 18:22:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ms_dsp_account`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_account`;
CREATE TABLE `ms_dsp_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `email_password` varchar(255) DEFAULT NULL COMMENT '邮箱注册码',
  `token` varchar(255) DEFAULT NULL COMMENT '账户激活码',
  `token_exptime` int(10) DEFAULT NULL COMMENT '激活码有效期',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态,0=未激活，1=已激活',
  `regtime` int(10) DEFAULT NULL COMMENT '注册时间',
  `lasttime` int(10) DEFAULT NULL,
  `reg_ip` varchar(100) DEFAULT NULL,
  `is_vip` int(1) DEFAULT '1' COMMENT '是否是vip0是1不是',
  `vip_type` int(10) DEFAULT '0' COMMENT 'vip等级0就是没有开通',
  `vip_begintime` varchar(60) DEFAULT NULL COMMENT 'vip开通时间',
  `vip_endtime` varchar(60) DEFAULT NULL COMMENT 'vip结束时间',
  `is_api` int(1) DEFAULT '1' COMMENT '是否有vip权限1没有0有',
  `api_token` varchar(200) DEFAULT NULL COMMENT 'api token',
  `day_count` int(11) DEFAULT NULL COMMENT '每日解析次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='//---前台邮箱注册';

-- ----------------------------
-- Records of ms_dsp_account
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_admin`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_admin`;
CREATE TABLE `ms_dsp_admin` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `qq` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_admin
-- ----------------------------
INSERT INTO `ms_dsp_admin` VALUES ('1', 'admin', '###a01506e6a28dbd3ce2baee23a7b20f51', '织梦科技', '321808886');

-- ----------------------------
-- Table structure for `ms_dsp_config`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_config`;
CREATE TABLE `ms_dsp_config` (
  `dsp_title` varchar(64) DEFAULT NULL,
  `dsp_name` varchar(64) NOT NULL DEFAULT '' COMMENT '配置名',
  `dsp_value` int(5) DEFAULT NULL COMMENT '配置值',
  `dsp_explain` varchar(200) NOT NULL COMMENT '配置说明',
  PRIMARY KEY (`dsp_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='全站配置表';

-- ----------------------------
-- Records of ms_dsp_config
-- ----------------------------
INSERT INTO `ms_dsp_config` VALUES ('接口权限', 'api_open', '1', 'api接口是否开放，1开放0不开放');
INSERT INTO `ms_dsp_config` VALUES ('广告模块', 'index_gg', '0', '首页广告模块，0开启1关闭');
INSERT INTO `ms_dsp_config` VALUES ('公告模块', 'index_notice', '0', '首页公告模块，0开启1关闭');
INSERT INTO `ms_dsp_config` VALUES ('最近解析模块', 'index_zjjx', '0', '首页最近解析模块，0为开启1为关闭');
INSERT INTO `ms_dsp_config` VALUES ('是否登录后使用', 'is_login', '1', '未注册用户是否可以直接使用，0开启1关闭，建议设置成关闭');
INSERT INTO `ms_dsp_config` VALUES ('注册用户每日赠送次数', 'user_count', '10', '注册用户每日免费解析次数0没有次数');
INSERT INTO `ms_dsp_config` VALUES ('同IP限制注册次数', 'user_ip', '3', '同IP限制注册次数0不限制');
INSERT INTO `ms_dsp_config` VALUES ('注册邮箱验证', 'user_mail', '1', '用户注册验证邮箱模块，0为开启1为关闭');
INSERT INTO `ms_dsp_config` VALUES ('青铜会员每日解析次数', 'user_vip1', '100', '等级1的会员解析次数');
INSERT INTO `ms_dsp_config` VALUES ('白银会员每日解析次数', 'user_vip2', '500', '等级2的会员解析次数');
INSERT INTO `ms_dsp_config` VALUES ('黄金会员每日解析次数', 'user_vip3', '1000', '等级3的会员解析次数');
INSERT INTO `ms_dsp_config` VALUES ('接口是否VIP才能使用', 'vip_get', '1', '接口是否是VIP才能申请，0用户可申请1VIP用户可申请');
INSERT INTO `ms_dsp_config` VALUES ('VIP申请接口等级', 'vip_int', '1', '等级多少的可以去申请解析接口，1就是青铜会员，2白银以此类推');

-- ----------------------------
-- Table structure for `ms_dsp_gg`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_gg`;
CREATE TABLE `ms_dsp_gg` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `g_img` varchar(255) DEFAULT NULL,
  `g_url` varchar(255) DEFAULT NULL,
  `g_addtime` varchar(255) DEFAULT NULL,
  `g_sort` int(100) DEFAULT '0' COMMENT '0正常1',
  `g_sta` int(1) DEFAULT '1' COMMENT '1显示0不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_gg
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_info`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_info`;
CREATE TABLE `ms_dsp_info` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `logo` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `descc` varchar(255) DEFAULT NULL,
  `banquan` varchar(255) DEFAULT NULL,
  `bb` varchar(5) DEFAULT '0',
  `tcode` text,
  `scode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_info
-- ----------------------------
INSERT INTO `ms_dsp_info` VALUES ('1', 'XS\'VdV2.0', '织梦科技短视频解析V2.0-抖音皮皮虾微视去水印解析', '织梦科技短视频解析V2.0', '织梦科技短视频解析V2.0', '2019 © made with <i class=\"material-icons\">favorite</i> by             LanShou for a better web.', '2.0.1', '<script type=\"text/javascript\">var cnzz_protocol = ((\"https:\" == document.location.protocol) ? \"https://\" : \"http://\");document.write(unescape(\"%3Cspan id=\'cnzz_stat_icon_1277775732\'%3E%3C/span%3E%3Cscript src=\'\" + cnzz_protocol + \"s96.cnzz.com/z_stat.php%3Fid%3D1277775732%26show%3Dpic\' type=\'text/javascript\'%3E%3C/script%3E\"));</script>', 'https://api.tecms.net/dsp?token=&key=&url=');

-- ----------------------------
-- Table structure for `ms_dsp_interface`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_interface`;
CREATE TABLE `ms_dsp_interface` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT,
  `api_title` varchar(255) DEFAULT NULL,
  `api_sta` int(2) DEFAULT '1' COMMENT '1启用0禁用',
  `api_apiimg` varchar(255) DEFAULT NULL,
  `api_local` int(2) DEFAULT '1',
  `api_bs` varchar(20) DEFAULT NULL,
  `api_url` varchar(200) DEFAULT NULL,
  `api_return_data` varchar(50) DEFAULT NULL,
  `api_return_img` varchar(50) DEFAULT NULL,
  `api_return_video` varchar(50) DEFAULT NULL,
  `api_return_title` varchar(255) DEFAULT NULL,
  `is_del` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_interface
-- ----------------------------
INSERT INTO `ms_dsp_interface` VALUES ('1', '抖音', '1', '/public/static/home/assets/images/douyin.png', '1', 'douyin', 'https://api.cn.berryapi.net/?s=Parse/Video&AppKey=wWGieVrfnmFDqsWvEfG8PxCNx4zhkhMC&url=', 'data', 'cover', 'video', 'title', '0');
INSERT INTO `ms_dsp_interface` VALUES ('2', '皮皮虾', '1', '/public/static/home/assets/images/pipixia.png', '0', 'pipix', null, null, null, null, null, '0');
INSERT INTO `ms_dsp_interface` VALUES ('3', '微视', '1', '/public/static/home/assets/images/weishi.png', '0', 'weishi', null, null, null, null, null, '0');
INSERT INTO `ms_dsp_interface` VALUES ('4', '最右', '1', '/public/static/home/assets/images/zuiyou.png', '0', 'izuiyou', null, null, null, null, null, '0');
INSERT INTO `ms_dsp_interface` VALUES ('6', '火山', '1', '/public/static/home/assets/images/huoshan.png', '0', 'huoshan', null, null, null, null, null, '0');
INSERT INTO `ms_dsp_interface` VALUES ('9', '今日头条', '1', '/public/static/home/assets/images/toutiao.png', '0', 'toutiaoimg', null, null, null, null, null, '0');
INSERT INTO `ms_dsp_interface` VALUES ('10', '西瓜', '1', '/public/static/home/assets/images/xigua.jpg', '0', 'ixigua', null, null, null, null, null, '0');
INSERT INTO `ms_dsp_interface` VALUES ('11', '快手', '1', '/public/static/home/assets/images/kuaishou.png', '0', 'chenzhongtech', null, null, null, null, null, '0');

-- ----------------------------
-- Table structure for `ms_dsp_jxlog`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_jxlog`;
CREATE TABLE `ms_dsp_jxlog` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `jx_url` varchar(255) DEFAULT NULL,
  `jx_img` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_jxlog
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_kami`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_kami`;
CREATE TABLE `ms_dsp_kami` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `vtype` int(10) DEFAULT NULL,
  `kami` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `creatuser` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `useuser` int(10) DEFAULT '0',
  `creattime` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `usetime` varchar(255) CHARACTER SET utf8 DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of ms_dsp_kami
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_loginlog`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_loginlog`;
CREATE TABLE `ms_dsp_loginlog` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `login_userid` int(100) DEFAULT NULL,
  `login_time` int(10) DEFAULT NULL,
  `login_ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of ms_dsp_loginlog
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_mail_config`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_mail_config`;
CREATE TABLE `ms_dsp_mail_config` (
  `id` int(1) NOT NULL DEFAULT '1',
  `send_sys_mail` varchar(100) DEFAULT NULL COMMENT '发件人邮箱',
  `send_sys_pwd` varchar(100) DEFAULT NULL COMMENT '密码',
  `send_sys_name` varchar(100) DEFAULT NULL COMMENT '设置发件人名称',
  `send_sys_smtp` varchar(100) DEFAULT NULL COMMENT 'SMTP服务器地址',
  `send_sys_port` int(10) DEFAULT NULL COMMENT '端口',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_mail_config
-- ----------------------------
INSERT INTO `ms_dsp_mail_config` VALUES ('1', '321808886@qq.com', '', '织梦科技短视频', 'smtp.qq.com', '465');

-- ----------------------------
-- Table structure for `ms_dsp_notice`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_notice`;
CREATE TABLE `ms_dsp_notice` (
  `id` int(1) NOT NULL DEFAULT '1',
  `title` varchar(50) DEFAULT NULL,
  `notice` text,
  `addtime` varchar(200) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_notice
-- ----------------------------
INSERT INTO `ms_dsp_notice` VALUES ('1', '❤网站公告❤', '<p>①此程序由<code>织梦科技</code>开发，QQ：321808886</p><p>                                    </p><p>②此程序售卖方式为<code>付费购买</code>，授权购买请到：<a href=\"http://ys.5266s.cn\" target=\"_blank\">ys.5266s.cn</a>购买，购买后请加售后群：<a href=\"https://jq.qq.com/?_wv=1027&k=Xpyf2aXe\" target=\"_blank\">321808886</a></p><p>                                    </p><p>③禁止任何人倒卖，贩卖此程序，如有发现，永久取消授权</p>', '1580643312', '0');

-- ----------------------------
-- Table structure for `ms_dsp_order`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_order`;
CREATE TABLE `ms_dsp_order` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(200) DEFAULT NULL,
  `order_goods_id` int(10) DEFAULT NULL,
  `order_title` varchar(100) DEFAULT NULL,
  `order_desc` varchar(100) DEFAULT NULL,
  `order_price` float(9,2) DEFAULT NULL,
  `order_creat_time` varchar(100) DEFAULT NULL,
  `order_paytype` varchar(100) DEFAULT NULL,
  `order_creat_user` int(100) DEFAULT NULL,
  `order_ispay` int(1) DEFAULT '0' COMMENT '0未支付1已支付',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_order
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_paytype`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_paytype`;
CREATE TABLE `ms_dsp_paytype` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `pay_name` varchar(50) DEFAULT NULL,
  `pay_bs` varchar(10) DEFAULT NULL,
  `pay_desc` varchar(100) DEFAULT NULL,
  `pay_public_key` varchar(2000) DEFAULT NULL,
  `pay_private_key` varchar(6000) DEFAULT NULL,
  `pay_return_url` varchar(100) DEFAULT NULL,
  `pay_notify_url` varchar(100) DEFAULT NULL,
  `pay_appid` varchar(100) DEFAULT NULL,
  `is_use` int(1) DEFAULT '1' COMMENT '1使用0不使用',
  `is_del` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_paytype
-- ----------------------------
INSERT INTO `ms_dsp_paytype` VALUES ('1', '支付宝', 'alipaydmf', '支付宝官方当面付', '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', null, null, '2018110462026129', '1', '0');
INSERT INTO `ms_dsp_paytype` VALUES ('2', '易支付-支付宝', 'ealipay', '易支付支付宝支付', 'http://pay.hackwl.cn/', 'zxcvbnm123456789asdfghjkl12345', 'http://www.msg.com/vip.html', 'http://www.msg.com/pay/epaynotify.html', '22141', '1', '0');
INSERT INTO `ms_dsp_paytype` VALUES ('3', '易支付-微信', 'ewxpay', '易支付微信支付', 'http://pay.hackwl.cn/', 'zxcvbnm123456789asdfghjkl12345', 'http://www.msg.com/vip.html', 'http://www.msg.com/pay/epaynotify.html', '22141', '1', '0');
INSERT INTO `ms_dsp_paytype` VALUES ('4', '易支付-QQ', 'eqqpay', '易支付QQ支付', 'http://pay.hackwl.cn/', 'zxcvbnm123456789asdfghjkl12345', 'http://www.msg.com/vip.html', 'http://www.msg.com/pay/epaynotify.html', '22141', '1', '0');

-- ----------------------------
-- Table structure for `ms_dsp_viplog`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_viplog`;
CREATE TABLE `ms_dsp_viplog` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `log_userid` int(100) DEFAULT NULL,
  `log_orderid` varchar(100) DEFAULT NULL,
  `log_mail` varchar(100) DEFAULT NULL,
  `log_text` varchar(255) DEFAULT NULL,
  `log_time` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_viplog
-- ----------------------------

-- ----------------------------
-- Table structure for `ms_dsp_viptype`
-- ----------------------------
DROP TABLE IF EXISTS `ms_dsp_viptype`;
CREATE TABLE `ms_dsp_viptype` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `vip_name` varchar(100) DEFAULT NULL COMMENT 'vip名称',
  `vip_bs` varchar(100) DEFAULT NULL,
  `vip_img` varchar(100) DEFAULT NULL COMMENT 'vip标识图标',
  `vip_desc` text,
  `vip_price` float(9,2) DEFAULT NULL,
  `vip_pirce_old` float(9,2) DEFAULT NULL,
  `vip_day` int(20) DEFAULT NULL COMMENT 'vip购买后的有效时长',
  `vip_is_tj` int(1) DEFAULT '1' COMMENT '是否推荐购买0是1否',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ms_dsp_viptype
-- ----------------------------
INSERT INTO `ms_dsp_viptype` VALUES ('1', '青铜会员', 'user_vip1', '/public/static/home/assets/vip/qtvip.png', '<li>青铜会员独特标识</li>\r\n                                                <li>有效期：自开通起<span style=\"color: red\">30</span>天</li>\r\n                                                <li>每日解析次数：<span style=\"color: red\">100</span>次</li>\r\n                                                <li>接口权限：无</li>', '0.01', '15.00', '30', '0');
INSERT INTO `ms_dsp_viptype` VALUES ('2', '白银会员', 'user_vip2', '/public/static/home/assets/vip/byvip.png', '<li>白银会员独特标识</li>\r\n                                                <li>有效期：自开通起<span style=\"color: red\">365</span>天</li>\r\n                                                <li>每日解析次数：<span style=\"color: red\">500</span>次</li>\r\n                                                <li>接口权限：有</li>', '88.00', '150.00', '30', '1');
INSERT INTO `ms_dsp_viptype` VALUES ('3', '黄金会员', 'user_vip3', '/public/static/home/assets/vip/hjvip.png', '<li>黄金会员独特标识</li>\r\n                                                <li>有效期：自开通起<span style=\"color: red\">永久</span>天</li>\r\n                                                <li>每日解析次数：<span style=\"color: red\">无限</span>次</li>\r\n                                                <li>接口权限：有</li>', '188.00', '550.00', '30', '0');
