DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `SIO` int(1) NOT NULL DEFAULT '0',
  `NDRC` int(1) NOT NULL DEFAULT '0',
  `CG` int(1) NOT NULL DEFAULT '0',
  `entreprise` varchar(300) DEFAULT NULL,
  `mail` varchar(100) DEFAULT NULL,
  `fixe` varchar(10) DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  `adresse` varchar(200) DEFAULT NULL,
  `postal` varchar(5) DEFAULT NULL,
  `ville` varchar(20) DEFAULT NULL,
  `pays` varchar(10) DEFAULT NULL,
  `annee` varchar(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;