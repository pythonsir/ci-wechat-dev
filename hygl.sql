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

 Date: 08/29/2018 23:37:00 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `tbl_article`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_article`;
CREATE TABLE `tbl_article` (
  `id` varchar(11) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `headimgurl` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `article` text NOT NULL,
  `createdAt` datetime NOT NULL,
  `ispub` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `tbl_order`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_order`;
CREATE TABLE `tbl_order` (
  `id` varchar(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `money` varchar(255) NOT NULL,
  `createdAt` datetime NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ispay` int(1) NOT NULL,
  `out_trade_no` varchar(255) NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
