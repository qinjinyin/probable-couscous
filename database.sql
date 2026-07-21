/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: 1_71jc_cn
-- ------------------------------------------------------
-- Server version	10.11.18-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ms_dsp_account`
--

DROP TABLE IF EXISTS `ms_dsp_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `email_password` varchar(255) DEFAULT NULL COMMENT '邮箱注册码',
  `token` varchar(255) DEFAULT NULL COMMENT '账户激活码',
  `token_exptime` int(10) DEFAULT NULL COMMENT '激活码有效期',
  `status` tinyint(1) DEFAULT 0 COMMENT '状态,0=未激活，1=已激活',
  `regtime` int(10) DEFAULT NULL COMMENT '注册时间',
  `lasttime` int(10) DEFAULT NULL,
  `reg_ip` varchar(100) DEFAULT NULL,
  `is_vip` int(1) DEFAULT 1 COMMENT '是否是vip0是1不是',
  `vip_type` int(10) DEFAULT 0 COMMENT 'vip等级0就是没有开通',
  `vip_begintime` varchar(60) DEFAULT NULL COMMENT 'vip开通时间',
  `vip_endtime` varchar(60) DEFAULT NULL COMMENT 'vip结束时间',
  `is_api` int(1) DEFAULT 1 COMMENT '是否有vip权限1没有0有',
  `api_token` varchar(200) DEFAULT NULL COMMENT 'api token',
  `day_count` int(11) DEFAULT NULL COMMENT '每日解析次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=202036988 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='//---前台邮箱注册';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_account`
--

LOCK TABLES `ms_dsp_account` WRITE;
/*!40000 ALTER TABLE `ms_dsp_account` DISABLE KEYS */;
INSERT INTO `ms_dsp_account` VALUES
(202036983,'qymao@126.com','###14e1b600b1fd579f47433b88e8d85291','1',1,1,1627994060,0,'111.16.132.107',1,0,NULL,NULL,1,NULL,10),
(202036984,'guoguochuan@126.com','###674c5ccd1108fca6d97a58348c714382','1',1,1,1627994116,NULL,'111.16.132.107',1,0,NULL,NULL,1,NULL,10),
(202036985,'cs123456@qq.com','###8d17bfa52c12252773db23998f46bb62','1',1,1,1707901597,1707926534,'192.168.253.1',1,0,NULL,NULL,1,NULL,10),
(202036986,'2509694148@qq.com','###0db7e3530cd60ab3bb2d4092de35637e','1',1,1,1746694476,0,'113.78.254.18',0,3,'1747396104','1778932104',0,'32DCA0F3777F381BC48A5B5F30B7DDF93ABD006196CC2CF065',5),
(202036987,'2922643564@qq.com','###0db7e3530cd60ab3bb2d4092de35637e','1',1,1,1747216147,NULL,'113.78.255.165',1,0,NULL,NULL,1,NULL,10);
/*!40000 ALTER TABLE `ms_dsp_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_admin`
--

DROP TABLE IF EXISTS `ms_dsp_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_admin` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `qq` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_admin`
--

LOCK TABLES `ms_dsp_admin` WRITE;
/*!40000 ALTER TABLE `ms_dsp_admin` DISABLE KEYS */;
INSERT INTO `ms_dsp_admin` VALUES
(1,'admin','###a01506e6a28dbd3ce2baee23a7b20f51','织梦科技',''),
(2,'admin','###e10adc3949ba59abbe56e057f20f883e','全网解析','3061163489');
/*!40000 ALTER TABLE `ms_dsp_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_config`
--

DROP TABLE IF EXISTS `ms_dsp_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_config` (
  `dsp_title` varchar(64) DEFAULT NULL,
  `dsp_name` varchar(64) NOT NULL DEFAULT '' COMMENT '配置名',
  `dsp_value` int(5) DEFAULT NULL COMMENT '配置值',
  `dsp_explain` varchar(200) NOT NULL COMMENT '配置说明',
  PRIMARY KEY (`dsp_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='全站配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_config`
--

LOCK TABLES `ms_dsp_config` WRITE;
/*!40000 ALTER TABLE `ms_dsp_config` DISABLE KEYS */;
INSERT INTO `ms_dsp_config` VALUES
('接口权限','api_open',1,'api接口是否开放，1开放0不开放'),
('广告模块','index_gg',0,'首页广告模块，0开启1关闭'),
('公告模块','index_notice',0,'首页公告模块，0开启1关闭'),
('最近解析模块','index_zjjx',1,'首页最近解析模块，0为开启1为关闭'),
('是否登录后使用','is_login',0,'未注册用户是否可以直接使用，0关闭1开启，建议设置成开启'),
('注册用户每日赠送次数','user_count',5,'注册用户每日免费解析次数0没有次数'),
('同IP限制注册次数','user_ip',3,'同IP限制注册次数0不限制'),
('注册邮箱验证','user_mail',1,'用户注册验证邮箱模块，0为开启1为关闭'),
('青铜会员每日解析次数','user_vip1',5,'等级1的会员解析次数'),
('白银会员每日解析次数','user_vip2',5,'等级2的会员解析次数'),
('黄金会员每日解析次数','user_vip3',5,'等级3的会员解析次数'),
('接口是否VIP才能使用','vip_get',0,'接口是否是VIP才能申请，0用户可申请1VIP用户可申请'),
('VIP申请接口等级','vip_int',0,'等级多少的可以去申请解析接口，1就是青铜会员，2白银以此类推');
/*!40000 ALTER TABLE `ms_dsp_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_gg`
--

DROP TABLE IF EXISTS `ms_dsp_gg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_gg` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `g_img` varchar(255) DEFAULT NULL,
  `g_url` varchar(255) DEFAULT NULL,
  `g_addtime` varchar(255) DEFAULT NULL,
  `g_sort` int(100) DEFAULT 0 COMMENT '0正常1',
  `g_sta` int(1) DEFAULT 1 COMMENT '1显示0不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_gg`
--

LOCK TABLES `ms_dsp_gg` WRITE;
/*!40000 ALTER TABLE `ms_dsp_gg` DISABLE KEYS */;
INSERT INTO `ms_dsp_gg` VALUES
(1,'/public/uploads/20210802/fd6a489c9269b1e47c9e0ebc56cea01e.jpg','http://dspjx.bheddp.cn/','1627912286',1,0);
/*!40000 ALTER TABLE `ms_dsp_gg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_info`
--

DROP TABLE IF EXISTS `ms_dsp_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_info` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `logo` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `descc` varchar(255) DEFAULT NULL,
  `banquan` varchar(255) DEFAULT NULL,
  `bb` varchar(5) DEFAULT '0',
  `tcode` text DEFAULT NULL,
  `scode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_info`
--

LOCK TABLES `ms_dsp_info` WRITE;
/*!40000 ALTER TABLE `ms_dsp_info` DISABLE KEYS */;
INSERT INTO `ms_dsp_info` VALUES
(1,'短视频去水印','短视频去水印解析接口-抖音快手皮皮虾微视去水印解析','API接口 小程序解析接口 短视频解析接口 抖音解析接口 小视频去水印解析接口 不限次数去水印解析','短视频去水印','','2.0.2','','https://api.tecms.net/dsp?token=&key=&url=');
/*!40000 ALTER TABLE `ms_dsp_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_interface`
--

DROP TABLE IF EXISTS `ms_dsp_interface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_interface` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT,
  `api_title` varchar(255) DEFAULT NULL,
  `api_sta` int(2) DEFAULT 1 COMMENT '1启用0禁用',
  `api_apiimg` varchar(255) DEFAULT NULL,
  `api_local` int(2) DEFAULT 1,
  `api_bs` varchar(20) DEFAULT NULL,
  `api_url` varchar(200) DEFAULT NULL,
  `api_return_data` varchar(50) DEFAULT NULL,
  `api_return_img` varchar(50) DEFAULT NULL,
  `api_return_video` varchar(50) DEFAULT NULL,
  `api_return_title` varchar(255) DEFAULT NULL,
  `is_del` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_interface`
--

LOCK TABLES `ms_dsp_interface` WRITE;
/*!40000 ALTER TABLE `ms_dsp_interface` DISABLE KEYS */;
INSERT INTO `ms_dsp_interface` VALUES
(1,'抖音',1,'/public/static/home/assets/images/douyin.png',1,'douyin','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(2,'皮皮虾',1,'/public/static/home/assets/images/pipixia.png',1,'pipix','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(3,'微视',1,'/public/static/home/assets/images/weishi.png',1,'weishi','','data','cover','url','biaoti',0),
(4,'最右',1,'/public/static/home/assets/images/zuiyou.png',1,'izuiyou','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(6,'火山',1,'/public/static/home/assets/images/huoshan.png',1,'huoshan','','data','cover','url','biaoti',0),
(9,'今日头条',1,'/public/static/home/assets/images/toutiao.png',1,'toutiaoimg','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(11,'快手',1,'/public/static/home/assets/images/kuaishou.png',1,'kuaishou','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(12,'网易',0,'https://ae01.alicdn.com/kf/Uea069598ecd8484bafa20068fd45467fC.jpg',1,'163','','data','cover','url','ti<x>tle',0),
(14,'西瓜',1,'https://ys.5266s.cn/public/static/home/assets/images/xigua.jpg',1,'ixigua','','data','cover','url','biaoti',0),
(19,'皮皮搞笑',1,'/public/static/home/assets/images/pipixia.png',1,'ippzone','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(22,'美拍',0,'/public/static/home/assets/images/douyin.png',1,'meipai','','data','cover','url','ti<x>tle',0),
(112,'哔哩哔哩',1,'https://s1.hdslb.com/bfs/static/game-web/duang/home/asserts/ic_pc_placeholder.fcc733a.svg',1,'bilibili','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(113,'A站',0,'https://imgs.aixifan.com/skjDBLPfwi-veumM3-AjQFfe-yM7Rv2-N32eaa.png',1,'acfun','','data','cover','url','biaoti',0),
(114,'咪咕音乐支持VIP',0,'｛｝',1,'migu','','type','cover','music_url','singer',0),
(115,'小红书解析',1,'',1,'xhs_parse','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(116,'豆包',1,'/public/static/home/assets/images/douyin.png',1,'doubao','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0),
(117,'即梦',1,'/public/static/home/assets/images/douyin.png',1,'jimeng','http://127.0.0.1:8081/proxy.php?url=','data','cover','url','title',0);
/*!40000 ALTER TABLE `ms_dsp_interface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_kami`
--

DROP TABLE IF EXISTS `ms_dsp_kami`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_kami` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `vtype` int(10) DEFAULT NULL,
  `kami` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `creatuser` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `useuser` int(10) DEFAULT 0,
  `creattime` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `usetime` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=gbk COLLATE=gbk_chinese_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_kami`
--

LOCK TABLES `ms_dsp_kami` WRITE;
/*!40000 ALTER TABLE `ms_dsp_kami` DISABLE KEYS */;
INSERT INTO `ms_dsp_kami` VALUES
(1,3,'VIPZZVAGQZFHXYIOBVPQ16063179406','system',202036986,'1747386391','1747396104');
/*!40000 ALTER TABLE `ms_dsp_kami` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_loginlog`
--

DROP TABLE IF EXISTS `ms_dsp_loginlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_loginlog` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `login_userid` int(100) DEFAULT NULL,
  `login_time` int(10) DEFAULT NULL,
  `login_ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=gbk COLLATE=gbk_chinese_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_loginlog`
--

LOCK TABLES `ms_dsp_loginlog` WRITE;
/*!40000 ALTER TABLE `ms_dsp_loginlog` DISABLE KEYS */;
INSERT INTO `ms_dsp_loginlog` VALUES
(5,202036980,1616245400,'117.188.84.25'),
(6,202036980,1616245400,'117.188.84.25'),
(7,202036981,1620911113,'122.194.1.145'),
(8,202036981,1620911113,'122.194.1.145'),
(9,202036982,1627909682,'123.169.193.148'),
(10,202036982,1627909682,'123.169.193.148'),
(11,202036982,1627909682,'112.247.53.46'),
(12,202036982,1627909682,'123.169.193.148'),
(13,202036985,1707926534,'192.168.253.1'),
(14,202036986,0,'113.78.254.18'),
(15,202036986,0,'113.78.254.18'),
(16,202036986,0,'113.78.254.18'),
(17,202036986,0,'113.78.255.198'),
(18,202036986,0,'14.150.200.139'),
(19,202036986,0,'183.46.193.138'),
(20,202036986,0,'113.78.255.198'),
(21,202036986,0,'113.78.255.165'),
(22,202036986,0,'113.78.255.198'),
(23,202036986,0,'223.104.84.5'),
(24,202036983,0,'127.0.0.1'),
(25,202036986,0,'223.104.84.5'),
(26,202036986,0,'223.104.82.193');
/*!40000 ALTER TABLE `ms_dsp_loginlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_mail_config`
--

DROP TABLE IF EXISTS `ms_dsp_mail_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_mail_config` (
  `id` int(1) NOT NULL DEFAULT 1,
  `send_sys_mail` varchar(100) DEFAULT NULL COMMENT '发件人邮箱',
  `send_sys_pwd` varchar(100) DEFAULT NULL COMMENT '密码',
  `send_sys_name` varchar(100) DEFAULT NULL COMMENT '设置发件人名称',
  `send_sys_smtp` varchar(100) DEFAULT NULL COMMENT 'SMTP服务器地址',
  `send_sys_port` int(10) DEFAULT NULL COMMENT '端口',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_mail_config`
--

LOCK TABLES `ms_dsp_mail_config` WRITE;
/*!40000 ALTER TABLE `ms_dsp_mail_config` DISABLE KEYS */;
INSERT INTO `ms_dsp_mail_config` VALUES
(1,'','','','',0);
/*!40000 ALTER TABLE `ms_dsp_mail_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_paytype`
--

DROP TABLE IF EXISTS `ms_dsp_paytype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `is_use` int(1) DEFAULT 1 COMMENT '1使用0不使用',
  `is_del` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_paytype`
--

LOCK TABLES `ms_dsp_paytype` WRITE;
/*!40000 ALTER TABLE `ms_dsp_paytype` DISABLE KEYS */;
INSERT INTO `ms_dsp_paytype` VALUES
(1,'支付宝','alipaydmf','支付宝官方当面付','MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnWH9H8u6SQ2gzKoZY35g9egmaEdbv3/Ph8Vq2nsvBjLT/84lNGLrqMAIN10RrXRHVPFmuBVILIwZOH2TeVr0Qk1NJEm58rKK5bshsk6fxDoro1LfclZlTOWVnfuA8PIRnDJ3igbIa7I2nXSIUm5z5pnLWwX/a2eJj/PDtwzH4ufIYCMM57gCQYWluhYPrq8ez80zQYkwZYWeA9hmE73L1FuAEh0Mnqk4pGyBnW5OVscxi4xcYIzY9VkYU2279prF75o4d1s1j/8RkvbdDJ/Fxn1BmE6ydeXbKOLDPaeU5R/i9xeWam8h7OS32cw8d1tB43pKJ4qKj87UdKscwHfLZwIDAQAB','MIIEowIBAAKCAQEApRy303SaqesrR57gTG46v4qIe/SqLxvK5W7HybGY3Fx5f0VG0n6xFT5ktAMAWPFj/G4u1nMYzkomwdFSqyybQfo9pxEE20VHr7jjUrs3kyp8Lg+0kI4D1XPpCCzqM1a2M3CC9WOEj/kkPZFy4XFH6Z02tb+dnwznQ+w+PjZ5vyii02p3rYEnjBTI3mbkIrN1fHBrhsqP5hgwjTykmG97ipIckfZYjNt3bhlN0xtRM8QNQKxKW+Ou5aGDIu8B0DoPCevtynRf93U0vEiWI9qfXbBIqrGmv2OOWI4zJRNhl9ILlO0loxEppblNBha9ZjD5sp1q+Ft/hwL4LSBZXBGqwwIDAQABAoIBACSkSudSaUBsQB6U05IipEb9p2xaz6nEsTJZc1z/XL0KiKwF48Gy+/mK7y5DvEAA0f+jNCHDSS3+prs8iOwT6iCqOCRrUniW12HX1hr2EU9HjmdqdNffrFoibwSxcwgRpj2kKmvqufB5ieFf9e6yBRODIDEVZRV295vLQcTssfgJ4RvTIMooaCkWX8SgszF9qw1yAHZpWQcXy32bxz3OEcElRdGvz9Qoehcz2W7qSK3WAk3RYO2weX6xGPD4i3RiXekxZOiK9JdJ+67OzAZkmJHzvWw5O+rtRYyU/vaVshs+LwIt1DJGZdneCOf+xYnFyIA1RywZ1hrcArV7u6FmXwECgYEA3Obd1cdcufQo+M1r0GGr1dQfK18EJm9cY04LEFKzRwmDu/cvW8OVXydBAhSB49HaLj53E7rtT6YwtXIAAsYS+vd5RuWeVMlLvF9zXaR1k8prrV6891HS4S4Bax6GhO6Qe5+zeurWaJ5M+bCrcA5Yak5rjykDoVV1AjyKJZwrC9sCgYEAv1iet1m6xtfgZEpst5+AOKrnZS6koF+gKB4df4t8SIN3lW2eH+t8etosNbqXUnwgyUGCZs23cUlkjyIfN7NzE95b35udfKMQEkIs5eOktiBtvD9ybgRbKKCl7LcMkXMtqvziiBPak2aLiZTulZI5rovxhHseYIE300Xyjnv+RTkCgYEA0nMPvG7mJDI8Jmxt3mRutJQV7dfjuEo3llPvrmlbar3hxt1DPQUj9TcMc9LdvBFc7tmL0DwxgcQue25UHFBgHMBPxf9TzCrKAHhfDEN10Irz8oNjO6u0X9rYdxjpxxMQivYmo1+/TIQjiW2KpoLEMOLtDcJhNv6XLr5GTnZtfCkCgYBOz4BnwlR0q9unUyQeKVrVfEbcXO6/g0Ni4qyBqQDimQt7B3A2c3aM3ymQY68J8UhLvGiqURa6WWFKtuImvgmq6E3s9ppSCJOJAaqecTmZLhgkjfliqPam8CwHKInLqqXnpErlg0/moDjezPHLtzN82fT6/P7Q/sfwhAHYf+eFuQKBgFK+jN7s1HcpVVwzTlnLmqqQhCtH3970LIe/KoCiPSwB6ihdipnCaQBjyxQSJUVubkDrsHa7Q+9RLRECGlM3mG0eII/fYuPcTcH+CxBbnHtSvcy7nUYv4LW/jSBsuAm8h4ztYN6cWUAIBvAKwVKquooYGZtRfUyzkeLGd28Zyi1g','http://42.193.239.48/user.html ','','2017040106514970',0,0),
(2,'易支付-支付宝','ealipay','易支付支付宝支付','http://pay.hmuwk.cn/','bmkt21Fr62T1HPBmhHh6mzMgAZ96MhzM','http://42.193.239.48/user.html','http://42.193.239.48/user.html','156295905',1,0),
(3,'易支付-微信','ewxpay','易支付微信支付','https://www.3mpay.com/','zxcvbnm123456789asdfghjkl12345','http://www.msg.com/vip.html','http://www.msg.com/pay/epaynotify.html','22141',0,0),
(4,'易支付-QQ','eqqpay','易支付QQ支付','https://www.3mpay.com/','zxcvbnm123456789asdfghjkl12345','http://www.msg.com/vip.html','http://www.msg.com/pay/epaynotify.html','22141',0,0);
/*!40000 ALTER TABLE `ms_dsp_paytype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_viplog`
--

DROP TABLE IF EXISTS `ms_dsp_viplog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_viplog` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `log_userid` int(100) DEFAULT NULL,
  `log_orderid` varchar(100) DEFAULT NULL,
  `log_mail` varchar(100) DEFAULT NULL,
  `log_text` varchar(255) DEFAULT NULL,
  `log_time` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_viplog`
--

LOCK TABLES `ms_dsp_viplog` WRITE;
/*!40000 ALTER TABLE `ms_dsp_viplog` DISABLE KEYS */;
INSERT INTO `ms_dsp_viplog` VALUES
(256,202036980,'','','系统赠送VIP贵宾月卡,30天','1616216323'),
(257,202036981,'','','系统赠送VIP至尊年卡,99999天','1620881000'),
(258,202036982,'','','系统赠送VIP贵宾月卡,30天','1627864206'),
(259,202036986,'VIPZZVAGQZFHXYIOBVPQ16063179406','2509694148@qq.com','新开365天至尊年卡，消费148.8','1747396104');
/*!40000 ALTER TABLE `ms_dsp_viplog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_dsp_viptype`
--

DROP TABLE IF EXISTS `ms_dsp_viptype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ms_dsp_viptype` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `vip_name` varchar(100) DEFAULT NULL COMMENT 'vip名称',
  `vip_bs` varchar(100) DEFAULT NULL,
  `vip_img` varchar(100) DEFAULT NULL COMMENT 'vip标识图标',
  `vip_desc` text DEFAULT NULL,
  `vip_price` float(9,2) DEFAULT NULL,
  `vip_pirce_old` float(9,2) DEFAULT NULL,
  `vip_day` int(20) DEFAULT NULL COMMENT 'vip购买后的有效时长',
  `vip_is_tj` int(1) DEFAULT 1 COMMENT '是否推荐购买0是1否',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_dsp_viptype`
--

LOCK TABLES `ms_dsp_viptype` WRITE;
/*!40000 ALTER TABLE `ms_dsp_viptype` DISABLE KEYS */;
INSERT INTO `ms_dsp_viptype` VALUES
(1,'贵宾月卡','user_vip1','/public/static/home/assets/vip/qtvip.png','<li>贵宾月卡独特标识</li>\r\n                                                <li>有效期：自开通起<span style=\"color: red\">30</span>天</li>\r\n                                                <li>每日解析次数：<span style=\"color: red\">2000</span>次</li>\r\n                                                <li>约合：<span style=\"color: red\">0.9元</span>/天</li>\r\n                                                <li>接口权限：有</li>',13.98,50.00,30,1),
(2,'铂金季卡','user_vip2','/public/static/home/assets/vip/byvip.png','<li>铂金季卡独特标识</li>\r\n                                                <li>有效期：自开通起<span style=\"color: red\">180</span>天</li>\r\n                                                <li>每日解析次数：<span style=\"color: red\">5000</span>次</li>\r\n                                                <li>约合：<span style=\"color: red\">0.7元</span>/天</li>\r\n                                                <li>接口权限：有</li>',39.90,288.00,180,1),
(3,'至尊年卡','user_vip3','/public/static/home/assets/vip/hjvip.png','<li>至尊年卡独特标识</li>\r\n                                                <li>有效期：自开通起<span style=\"color: red\">365</span>天</li>\r\n                                                <li>每日解析次数：<span style=\"color: red\">8000 </span>次数</li>\r\n                                                <li>约合：<span style=\"color: red\">0.5元</span>/天</li>\r\n                                                <li>接口权限：有</li>',148.80,580.00,365,1);
/*!40000 ALTER TABLE `ms_dsp_viptype` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-21  1:58:06
