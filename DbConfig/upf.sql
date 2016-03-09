-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 04, 2016 at 09:31 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `upf`
-- 
CREATE DATABASE `upf` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `upf`;

-- --------------------------------------------------------

-- 
-- Table structure for table `admin`
-- 

CREATE TABLE `admin` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `admin`
-- 

INSERT INTO `admin` VALUES (1, 'Testing Admin', 'testing@gmail.com', 'ae2b1fca515949e5d54fb22b8ed95575');
INSERT INTO `admin` VALUES (2, 'Three SS', 'ss_3@yahoo.com', '35d6d33467aae9a2e3dccb4b6b027878');
INSERT INTO `admin` VALUES (3, 'Another', 'another@gmail.com', 'b32d73e56ec99bc5ec8f83871cde708a');

-- --------------------------------------------------------

-- 
-- Table structure for table `department`
-- 

CREATE TABLE `department` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(300) NOT NULL,
  `faculty` varchar(300) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `department`
-- 

INSERT INTO `department` VALUES (1, 'Computer Science and Engineering', 'Engineering and Technology', 0);
INSERT INTO `department` VALUES (2, 'Accounting', 'Management Sciences', 0);
INSERT INTO `department` VALUES (3, 'Electrical and Electronics', 'Engineering and Technology', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `project`
-- 

CREATE TABLE `project` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(300) NOT NULL,
  `abstract` text NOT NULL,
  `author` varchar(400) NOT NULL,
  `category` varchar(300) NOT NULL,
  `department` varchar(300) NOT NULL,
  `supervisor` varchar(300) NOT NULL,
  `year` varchar(10) NOT NULL,
  `project_file` varchar(300) NOT NULL,
  `date_uploaded` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `title` (`title`,`author`,`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `project`
-- 

INSERT INTO `project` VALUES (7, 'Conclusive Praximal', 'Showing rows 0 - 2 (3 total, Query took 0.0007 sec). Showing rows 0 - 2 (3 total, Query took 0.0007 sec)Showing rows 0 - 2 (3 total, Query took 0.0007 sec)', '1', 'Under Graduate', '1', 'mojolagbe@gmail.com', '2015', '667087_project_file.pdf', '2016-03-02', 1);
INSERT INTO `project` VALUES (8, 'Conclusive Praximal II', 'Showing rows 0 - 2 (3 total, Query took 0.0007 sec). Showing rows 0 - 2 (3 total, Query took 0.0007 sec)Showing rows 0 - 2 (3 total, Query took 0.0007 sec)', '1', 'Post Graduate', '1', 'mojolagbe@gmail.com', '2015', '840589_project_file.pdf', '2016-03-02', 1);
INSERT INTO `project` VALUES (9, 'Demo Craze', 'Showing rows 0 - 2 (3 total, Query took 0.0007 sec). Showing rows 0 - 2 (3 total, Query took 0.0007 sec)Showing rows 0 - 2 (3 total, Query took 0.0007 sec)', '1', 'others', '1', 'mojolagbe@gmail.com', '2016', '331949_project_file.pdf', '2016-03-02', 1);
INSERT INTO `project` VALUES (6, 'Mobile 3', 'Mobile 3 abstract', '1', 'PhD', '1', 'hrdultimate@yahoo.com', '2016', '476309_project_file.pdf', '2016-02-14', 1);
INSERT INTO `project` VALUES (10, 'Demo Senders', 'Showing rows 0 - 2 (3 total, Query took 0.0007 sec). Showing rows 0 - 2 (3 total, Query took 0.0007 sec)Showing rows 0 - 2 (3 total, Query took 0.0007 sec)', '1', 'others', '1', 'mojolagbe@gmail.com', '2016', '910352_project_file.pdf', '2016-03-02', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `student`
-- 

CREATE TABLE `student` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(300) NOT NULL,
  `matric_number` varchar(20) NOT NULL,
  `department` varchar(300) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `matric_number` (`matric_number`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `student`
-- 

INSERT INTO `student` VALUES (1, 'Adewale Amos Tolani', '2002', '1', 'seun_adex2@yahoo.com', '08066435341', 'ae2b1fca515949e5d54fb22b8ed95575');
INSERT INTO `student` VALUES (2, 'James Jamiu', '2312142', '2', 'jame2jam@yahoo.com', '23486543218', 'ae2b1fca515949e5d54fb22b8ed95575');
INSERT INTO `student` VALUES (3, 'Jamiu Babatunde', '0785342', '1', 'mojo@gmail.com', '08123345245', 'ae2b1fca515949e5d54fb22b8ed95575');

-- --------------------------------------------------------

-- 
-- Table structure for table `supervisor`
-- 

CREATE TABLE `supervisor` (
  `id` varchar(200) NOT NULL,
  `name` varchar(400) NOT NULL,
  `password` varchar(800) NOT NULL,
  `department` varchar(800) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `supervisor`
-- 

INSERT INTO `supervisor` VALUES ('hrdultimate@yahoo.com', 'Haroon Babatunde', 'f5d1278e8109edd94e1e4197e04873b9', '1', 0);
INSERT INTO `supervisor` VALUES ('mojolagbe@gmail.com', 'Mojolagbe Jamiu Babatunde', 'ae2b1fca515949e5d54fb22b8ed95575', '1', 0);
