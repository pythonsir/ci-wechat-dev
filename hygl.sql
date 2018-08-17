/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50633
 Source Host           : 127.0.0.1
 Source Database       : hygl

 Target Server Type    : MySQL
 Target Server Version : 50633
 File Encoding         : utf-8

 Date: 08/17/2018 11:45:48 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `tbl_order`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_order`;
CREATE TABLE `tbl_order` (
  `id` varchar(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `money` varchar(255) NOT NULL,
  `createdAt` int(11) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ispay` int(1) NOT NULL,
  `out_trade_no` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
