-- phpMyAdmin SQL Dump
-- version 4.2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 08, 2014 at 02:57 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `elite_trader`
--
CREATE DATABASE IF NOT EXISTS `elite_trader` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `elite_trader`;

-- --------------------------------------------------------

--
-- Table structure for table `goods`
--

DROP TABLE IF EXISTS `goods`;
CREATE TABLE IF NOT EXISTS `goods` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=99 ;

--
-- Dumping data for table `goods`
--

INSERT INTO `goods` (`id`, `name`, `description`) VALUES(50, 'Agri-Medicines', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(51, 'Algae', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(52, 'Animal Meat', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(53, 'Animal Monitors', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(54, 'Aquaponic Systems', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(55, 'Auto-Fabricators', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(56, 'Basic Meds', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(57, 'Bertrandite', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(58, 'Biowaste', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(59, 'Clothing', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(60, 'Cobalt', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(61, 'Coffee', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(62, 'Coltan', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(63, 'Computer Components', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(64, 'Consumer Technology', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(65, 'Cotton', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(66, 'Crop Harvesters', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(67, 'Dom. Appliances', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(68, 'Energy Drinks', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(69, 'Fish', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(70, 'Food Cartridges', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(71, 'Fruit and Vegetables', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(72, 'Gold', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(73, 'Grain', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(74, 'Hydrogen Fuels', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(75, 'Leather', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(76, 'Liquors', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(77, 'Lithium', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(78, 'Marine Supplies', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(79, 'Mineral Extractors', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(80, 'Mineral Oil', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(81, 'Non-lethal wpns', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(82, 'Palladium', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(83, 'Performance Enhancers', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(84, 'Personal Armour', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(85, 'Personal Weapons', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(86, 'Pesticides', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(87, 'Plastics', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(88, 'Progenitor Cells', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(89, 'Reactive armor', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(90, 'Resonating Separators', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(91, 'Robotics', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(92, 'Superconductors', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(93, 'Synthetic meat', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(94, 'Tantalum', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(95, 'Tea', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(96, 'Terrain Enrichment Sys. ', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(97, 'Tobacco', NULL);
INSERT INTO `goods` (`id`, `name`, `description`) VALUES(98, 'Wine', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `description`) VALUES(1, 'I Bootis A - Chango Dock', NULL);
INSERT INTO `locations` (`id`, `name`, `description`) VALUES(2, 'Eranin - Azeban City', NULL);
INSERT INTO `locations` (`id`, `name`, `description`) VALUES(3, 'Asellus Prime - Beagle 2 Landing', NULL);
INSERT INTO `locations` (`id`, `name`, `description`) VALUES(4, 'LP 98-132 - Freeport', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

DROP TABLE IF EXISTS `prices`;
CREATE TABLE IF NOT EXISTS `prices` (
  `good_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `price_buy` int(10) unsigned NOT NULL,
  `price_sell` int(10) unsigned DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Commodity - Location relation';

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(50, 1, 1082, 1082, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(50, 2, 1086, 1086, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(50, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(50, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(51, 1, 1, 1, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(51, 2, 0, 0, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(51, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(51, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(52, 1, 1395, 1395, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(52, 2, 1042, 1042, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(52, 3, 1329, 1329, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(52, 4, 1239, 1239, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(53, 1, 288, 288, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(53, 2, 289, 289, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(53, 3, 150, 150, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(53, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(54, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(54, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(54, 3, 129, 129, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(54, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(55, 1, 3927, 3927, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(55, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(55, 3, 3085, 3085, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(55, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(56, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(56, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(56, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(56, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(57, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(57, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(57, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(57, 4, 1830, 1830, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(58, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(58, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(58, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(58, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(59, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(59, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(59, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(59, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(60, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(60, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(60, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(60, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(61, 1, 1393, 1393, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(61, 2, 1048, 1048, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(61, 3, 1360, 1360, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(61, 4, 1239, 1239, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(62, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(62, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(62, 3, 940, 940, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(62, 4, 914, 914, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(63, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(63, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(63, 3, 550, 550, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(63, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(64, 1, 7090, 7090, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(64, 2, 7115, 7115, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(64, 3, 6595, 6595, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(64, 4, 6588, 6588, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(65, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(65, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(65, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(65, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(66, 1, 1904, 1904, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(66, 2, 2341, 2341, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(66, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(66, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(67, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(67, 2, 550, 550, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(67, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(67, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(68, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(68, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(68, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(68, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(69, 1, 360, 360, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(69, 2, 645, 645, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(69, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(69, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(70, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(70, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(70, 3, 102, 102, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(70, 4, 103, 103, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(71, 1, 305, 305, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(71, 2, 161, 161, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(71, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(71, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(72, 1, 9862, 9862, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(72, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(72, 3, 9702, 9702, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(72, 4, 9119, 9119, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(73, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(73, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(73, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(73, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(74, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(74, 2, 56, 56, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(74, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(74, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(75, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(75, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(75, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(75, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(76, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(76, 2, 659, 659, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(76, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(76, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(77, 1, 1690, 1690, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(77, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(77, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(77, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(78, 1, 4476, 4476, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(78, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(78, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(78, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(79, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(79, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(79, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(79, 4, 624, 624, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(80, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(80, 2, 62, 62, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(80, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(80, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(81, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(81, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(81, 3, 1787, 1787, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(81, 4, 1942, 1942, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(82, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(82, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(82, 3, 13478, 13478, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(82, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(83, 1, 7090, 7090, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(83, 2, 7115, 7115, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(83, 3, 7116, 7116, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(83, 4, 7110, 7110, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(84, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(84, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(84, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(84, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(85, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(85, 2, 4491, 4491, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(85, 3, 3863, 3863, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(85, 4, 4115, 4115, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(86, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(86, 2, 199, 199, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(86, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(86, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(87, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(87, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(87, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(87, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(88, 1, 7090, 7090, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(88, 2, 7115, 7115, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(88, 3, 7061, 7061, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(88, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(89, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(89, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(89, 3, 1958, 1958, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(89, 4, 2209, 2209, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(90, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(90, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(90, 3, 4921, 4921, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(90, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(91, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(91, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(91, 3, 1539, 1539, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(91, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(92, 1, 7090, 7090, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(92, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(92, 3, 7116, 7116, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(92, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(93, 1, 232, 232, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(93, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(93, 3, 233, 233, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(93, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(94, 1, 4192, 4192, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(94, 2, 0, 0, '2014-08-08 08:33:41');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(94, 3, 4149, 4149, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(94, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(95, 1, 1582, 1582, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(95, 2, 1203, 1203, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(95, 3, 1405, 1405, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(95, 4, 1414, 1414, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(96, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(96, 2, 4675, 4675, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(96, 3, 4614, 4614, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(96, 4, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(97, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(97, 2, 4675, 4675, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(97, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(97, 4, 5158, 5158, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(98, 1, 0, 0, '2014-08-08 08:33:28');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(98, 2, 256, 256, '2014-08-08 12:38:32');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(98, 3, 0, 0, '2014-08-08 08:02:57');
INSERT INTO `prices` (`good_id`, `location_id`, `price_buy`, `price_sell`, `ts`) VALUES(98, 4, 0, 0, '2014-08-08 08:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `roads`
--

DROP TABLE IF EXISTS `roads`;
CREATE TABLE IF NOT EXISTS `roads` (
  `location_id_from` int(10) unsigned NOT NULL,
  `location_id_to` int(10) unsigned NOT NULL,
  `distance` decimal(10,2) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Location - location relations';

--
-- Dumping data for table `roads`
--

INSERT INTO `roads` (`location_id_from`, `location_id_to`, `distance`) VALUES(1, 2, 1.00);
INSERT INTO `roads` (`location_id_from`, `location_id_to`, `distance`) VALUES(2, 1, 1.00);
INSERT INTO `roads` (`location_id_from`, `location_id_to`, `distance`) VALUES(2, 4, 2.00);
INSERT INTO `roads` (`location_id_from`, `location_id_to`, `distance`) VALUES(3, 4, 1.00);
INSERT INTO `roads` (`location_id_from`, `location_id_to`, `distance`) VALUES(4, 2, 2.00);
INSERT INTO `roads` (`location_id_from`, `location_id_to`, `distance`) VALUES(4, 3, 1.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `goods`
--
ALTER TABLE `goods`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
 ADD UNIQUE KEY `good_location` (`good_id`,`location_id`), ADD KEY `location` (`location_id`), ADD KEY `good` (`good_id`);

--
-- Indexes for table `roads`
--
ALTER TABLE `roads`
 ADD UNIQUE KEY `location_id_from_to` (`location_id_from`,`location_id_to`), ADD KEY `locations_to` (`location_id_to`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `goods`
--
ALTER TABLE `goods`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=99;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `prices`
--
ALTER TABLE `prices`
ADD CONSTRAINT `locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `goods` FOREIGN KEY (`good_id`) REFERENCES `goods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roads`
--
ALTER TABLE `roads`
ADD CONSTRAINT `locations_to` FOREIGN KEY (`location_id_to`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `location_from` FOREIGN KEY (`location_id_from`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
