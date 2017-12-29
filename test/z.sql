DROP TABLE IF EXISTS `r_random_photos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `r_random_photos` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `state` char(3) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `seqno` int(11) NOT NULL default '0',
  `file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `r_random_photos`
--

/*!40000 ALTER TABLE `r_random_photos` DISABLE KEYS */;
INSERT INTO `r_random_photos` VALUES (36,'NSW','Old Casino',3,'old_casino03.jpg'),(37,'NSW','Captains Flat',4,'captains_flat05.jpg'),(38,'NSW','Compton Downs',1,'compton_downs15.jpg'),(39,'VIC','Echuca',4,'echuca04.jpg'),(40,'NSW','Caledonia',3,'caledonia02.jpg');
/*!40000 ALTER TABLE `r_random_photos` ENABLE KEYS */;


