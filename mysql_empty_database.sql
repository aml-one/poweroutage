--
-- Database: `power`
--

-- --------------------------------------------------------

--
-- Table structure for table `Health`
--

CREATE TABLE `Health` (
  `HKey` text NOT NULL,
  `HValue` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Health`
--

INSERT INTO `Health` (`HKey`, `HValue`) VALUES
('reader', ''),
('pm', '');

-- --------------------------------------------------------

--
-- Table structure for table `PowerLog`
--

CREATE TABLE `PowerLog` (
  `PId` varchar(35) NOT NULL,
  `PEvent` varchar(5) NOT NULL,
  `PTime` varchar(45) NOT NULL,
  `PValid` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `Health`
--
ALTER TABLE `Health`
  ADD UNIQUE KEY `HKey` (`HKey`) USING HASH;

--
-- Indexes for table `PowerLog`
--
ALTER TABLE `PowerLog`
  ADD UNIQUE KEY `Pid` (`PId`) USING BTREE;
COMMIT;
