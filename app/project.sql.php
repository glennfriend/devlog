<?php exit; ?>

-- phpMyAdmin SQL Dump
-- version 4.4.6
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2015 年 05 月 26 日 03:15
-- 伺服器版本: 5.5.41-0ubuntu0.14.04.1
-- PHP 版本： 5.6.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `devlog`
--

-- --------------------------------------------------------

--
-- 資料表結構 `folders`
--

CREATE TABLE IF NOT EXISTS `folders` (
  `key` varchar(32) NOT NULL,
  `real` varchar(255) NOT NULL,
  `name` varchar(64) NOT NULL,
  `mtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `properties` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `folder_tags`
--

CREATE TABLE IF NOT EXISTS `folder_tags` (
  `folder_key` varchar(32) NOT NULL,
  `type` varchar(16) NOT NULL,
  `val` varchar(32) NOT NULL,
  `score` tinyint(3) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`key`);

--
-- 資料表索引 `folder_tags`
--
ALTER TABLE `folder_tags`
  ADD KEY `type_val_index` (`type`,`val`),
  ADD KEY `folder_key` (`folder_key`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
