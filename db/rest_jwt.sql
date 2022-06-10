-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2017 at 07:11 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30
use u1276530_suzuki;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dashboardive`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_user`
--
use u1276530_suzuki;
CREATE TABLE `m_customer` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) null,
  `photo_image_id` varchar(12) null,
  `full_name` varchar(200) null,
  `gender_id` varchar(2) null,
  `city_id` varchar(4) null,
  `phone_number` varchar(13) NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(250) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_user`
--
use u1276530_suzuki;
INSERT INTO `m_customer` (`nik`, `photo_image_id`, `full_name`, `gender_id`, `city_id`, `phone_number`, `username`, `password`, `level`) VALUES
('3205100206910005', '612295870834', 'Nandang Hermawan', 'L', '3205', '087724538083', 'nandang55', 'f865b53623b121fd34ee5426c792e5c33af8c227',  1)

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_user`
--
use u1276530_suzuki;
ALTER TABLE `m_customer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_user`
--
use u1276530_suzuki;
ALTER TABLE `m_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


use u1276530_suzuki;
ALTER TABLE `m_customer`
  ADD  `device_id` varchar(50) ;

use u1276530_suzuki;
SELECT * from m_customer
