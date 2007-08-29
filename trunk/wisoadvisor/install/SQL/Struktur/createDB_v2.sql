
ALTER TABLE `advisor__user` CHANGE `majid` `majid` INT( 10 ) UNSIGNED NOT NULL DEFAULT '1'


-- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


CREATE TABLE `advisor__studies` (
  `stid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `generation` int(10) unsigned default NULL,
  `assess_proceed` int(10) unsigned default NULL,
  PRIMARY KEY  (`stid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

INSERT INTO `advisor__studies` VALUES (1, 'Wirtschaftswissenschaften', 2, 0);
INSERT INTO `advisor__studies` VALUES (2, 'International Business Studies', 2, 0);
INSERT INTO `advisor__studies` VALUES (3, 'Sozialökonomik', 2, 0);






-- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------






CREATE TABLE `advisor__majors` (
  `majid` int(10) unsigned NOT NULL auto_increment,
  `shortname` varchar(255) collate latin1_general_ci default NULL,
  `fullname` varchar(255) collate latin1_general_ci default NULL,
  `stid` int(10) unsigned default NULL,
  `veryshortname` varchar(25) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`majid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=9 ;

INSERT INTO `advisor__majors` VALUES (1, 'Betriebswirtschaftslehre', 'Wirtschaftswissenschaften (BWL)', 1, 'BWL');
INSERT INTO `advisor__majors` VALUES (2, 'Volkswirtschaftslehre', 'Wirtschaftswissenschaften (VWL)', 1, 'VWL');
INSERT INTO `advisor__majors` VALUES (3, 'Studienrichtung I', 'Wirtschaftswissenschaften (Wirtschaftspädagogik I)', 1, 'WiPäd I');
INSERT INTO `advisor__majors` VALUES (4, 'Wirtschaftsinformatik', 'Wirtschaftswissenschaften (Wirtschaftsinformatik)', 1, 'WI');
INSERT INTO `advisor__majors` VALUES (5, 'Studienrichtung II', 'Wirtschaftswissenschaften (Wirtschaftspädagogik II)', 1, 'WiPäd II');
INSERT INTO `advisor__majors` VALUES (6, 'Verhaltenswissenschaften', 'Sozialökonomik (Verhaltenswissenschaften)', 3, 'SozÖk Verh');
INSERT INTO `advisor__majors` VALUES (7, 'International', 'Sozialökonomik (International)', 3, 'SozÖk Int');
INSERT INTO `advisor__majors` VALUES (8, 'International Business Studies', 'International Business Studies', 2, 'IBS');






-- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------






CREATE TABLE `advisor__modulegroups` (
  `mgrpid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`mgrpid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

INSERT INTO `advisor__modulegroups` VALUES (1, 'Pflichtbereich');
INSERT INTO `advisor__modulegroups` VALUES (2, 'Schlüsselqualifikationsbereich');
INSERT INTO `advisor__modulegroups` VALUES (3, 'Kernbereich');
INSERT INTO `advisor__modulegroups` VALUES (4, 'Vertiefungsbereich');
INSERT INTO `advisor__modulegroups` VALUES (5, 'Doppelpflichtwahlfach');






-- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------






CREATE TABLE `advisor__modules` (
  `modid` int(10) unsigned NOT NULL auto_increment,
  `majid` int(10) unsigned default NULL,
  `mgrpid` int(10) unsigned default NULL,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `sws` int(10) unsigned default NULL,
  `ects` float unsigned default NULL,
  `angebot_semester` enum('ws','ss','both') collate latin1_general_ci default NULL,
  `default_semester` int(10) unsigned default NULL,
  `assessment` enum('true','false') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`modid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- 
-- Stammdaten für Schwerpunkt BWL
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Unternehmensplanspiel', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Unternehmer und Unternehmen', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Buchführung', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Mathematik I', 4, 5, 'both', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Mathematik II', 4, 5, 'both', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Absatz', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Jahresabschluss', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Makroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Mikroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Wirtschaft und Staat', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Privat- und Handelsrecht I', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Privat- und Handelsrecht II', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 4, 'false');

-- Schluesselqualifikation (mgrpid = 2)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 2, 'Sprachen (1)', 2, 2.5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 2, 'Sprachen (2)', 2, 2.5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 2, 'Sprachen (3)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 2, 'Präsentationsfähigkeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 2, 'Einführung in wissenschaftliches Arbeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 2, 'Praxis der emp. Wirtschaftsforschung', 4, 5, 'ss', 4, 'false');

-- Kernbereich (mgrpid = 3)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 3, 'Kostenrechnung und Controlling', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 3, 'Internationale Unternehmensführung', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 3, 'Investition und Finanzierung', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 3, 'Planspiel / Fallstudienseminar', 4, 5, 'ss', 6, 'false');

-- Vertiefungsbereich (mgrpid = 4)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'BWL Vertiefungsblock I (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'BWL Vertiefungsblock I (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'BWL Vertiefungsblock II (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'BWL Vertiefungsblock II (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'BWL Vertiefungsblock III (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'BWL Vertiefungsblock III (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'Allg. Vertiefungsblock (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'Allg. Vertiefungsblock (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (1, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');


-- 
-- Stammdaten für Schwerpunkt VWL
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Unternehmensplanspiel', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Unternehmer und Unternehmen', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Buchführung', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Mathematik I', 4, 5, 'both', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Mathematik II', 4, 5, 'both', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Absatz', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Jahresabschluss', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Makroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Mikroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Wirtschaft und Staat', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Privat- und Handelsrecht I', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Privat- und Handelsrecht II', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 4, 'false');
                                                                                                                                        
-- Schluesselqualifikation (mgrpid = 2)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 2, 'Sprachen (1)', 2, 2.5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 2, 'Sprachen (2)', 2, 2.5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 2, 'Sprachen (3)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 2, 'Präsentationsfähigkeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 2, 'Einführung in wissenschaftliches Arbeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 2, 'Praxis der emp. Wirtschaftsforschung', 4, 5, 'ss', 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 3, 'Außenwirtschaft', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 3, 'Ökonomie des öffentlichen Sektors', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 3, 'Arbeitsmarktpolitik', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 3, 'Wettbewerbstheorie und -politik', 4, 5, 'ss', 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'VWL Vertiefungsblock I (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'VWL Vertiefungsblock I (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'VWL Vertiefungsblock II (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'VWL Vertiefungsblock II (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'Allg. Vertiefungsblock I (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'Allg. Vertiefungsblock I (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'Allg. Vertiefungsblock II (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'Allg. Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (2, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');


-- 
-- Stammdaten für Schwerpunkt WI
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Unternehmensplanspiel', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Unternehmer und Unternehmen', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Buchführung', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Mathematik I', 4, 5, 'both', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Mathematik II', 4, 5, 'both', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Absatz', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Jahresabschluss', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Makroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Mikroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Wirtschaft und Staat', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Privat- und Handelsrecht I', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Privat- und Handelsrecht II', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 4, 'false');
                                                                                                                                        
-- Schluesselqualifikation (mgrpid = 2)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 2, 'Sprachen (1)', 2, 2.5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 2, 'Sprachen (2)', 2, 2.5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 2, 'Sprachen (3)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 2, 'Präsentationsfähigkeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 2, 'Einführung in wissenschaftliches Arbeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 2, 'Praxis der emp. Wirtschaftsforschung', 4, 5, 'ss', 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 3, 'AWI: IT-gestützte Unternehmensführung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 3, 'AWI: E-Business Management', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 3, 'AWI: IT-Management', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 3, 'Planspiel / Fallstudienseminar', 4, 5, 'ss', 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'SWI: Technologie- und Projektmanagement (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'SWI: Technologie- und Projektmanagement (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'SWI: Innovations- und Wertschöpfungsmanagement (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'SWI: Innovations- und Wertschöpfungsmanagement (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'SWI: Prozess-, Service- und Informationsmanagement (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'SWI: Prozess-, Service- und Informationsmanagement (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'Allg. Vertiefungsblock (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'Allg. Vertiefungsblock (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (4, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');


-- 
-- Stammdaten für Schwerpunkt WiPaed, StR I
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Unternehmensplanspiel', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Unternehmer und Unternehmen', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Buchführung', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Mathematik I', 4, 5, 'both', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Mathematik II', 4, 5, 'both', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Absatz', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Jahresabschluss', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Makroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Mikroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Wirtschaft und Staat', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Privat- und Handelsrecht I', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Privat- und Handelsrecht II', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 4, 'false');
                                                                                                                                        
-- Schluesselqualifikation (mgrpid = 2)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 2, 'Sprachen (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 2, 'Sprachen (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 2, 'Praxis der emp. Wirtschaftsforschung', 4, 5, 'ss', 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 3, 'Grundlagen der Wirtschafts- und Betriebspädagogik', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 3, 'Präsentation und Moderation I', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 3, 'Präsentation und Moderation II', 3, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 3, 'Berufliche Weiterbildung', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 3, 'Berufspädagogisches Seminar', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 3, 'Schulpraktische Studien / Erkundungsprojekt', 2, 5, 'ws', 5, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'BWL Vertiefungsblock I (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'BWL Vertiefungsblock I (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'BWL Vertiefungsblock II (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'BWL Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'Allg. Vertiefungsblock I (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'Allg. Vertiefungsblock I (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'Allg. Vertiefungsblock II (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'Allg. Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (3, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');


-- 
-- Stammdaten für Schwerpunkt WiPaed, StR II
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Unternehmensplanspiel', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Unternehmer und Unternehmen', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Buchführung', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Mathematik I', 4, 5, 'both', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Mathematik II', 4, 5, 'both', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Absatz', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Jahresabschluss', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Makroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Mikroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Wirtschaft und Staat', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Privat- und Handelsrecht I', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Privat- und Handelsrecht II', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 4, 'false');
                                                                                                                                        
-- Doppelpflichtwahlfach (mgrpid = 5)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 5, 'Doppelpflichtwahlfach (1)', 6, 7.5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 5, 'Doppelpflichtwahlfach (2)', 2, 2.5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 5, 'Doppelpflichtwahlfach (3)', 4, 5, 'both', 5, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 3, 'Grundlagen der Wirtschafts- und Betriebspädagogik', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 3, 'Präsentation und Moderation I', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 3, 'Präsentation und Moderation II', 3, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 3, 'Berufliche Weiterbildung', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 3, 'Berufspädagogisches Seminar', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 3, 'Schulpraktische Studien / Erkundungsprojekt', 2, 5, 'ws', 5, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'BWL Vertiefungsblock I (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'BWL Vertiefungsblock I (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'BWL Vertiefungsblock II (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'BWL Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'Allg. Vertiefungsblock I (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'Allg. Vertiefungsblock I (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'Allg. Vertiefungsblock II (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'Allg. Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (5, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');



-- 
-- Stammdaten für Schwerpunkt SozOek, Verhalten
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Soziologie I', 4, 7.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Soziologie II', 4, 7.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Internationale und transnationale Beziehungen', 4, 7.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Regionen im internationalen System', 4, 7.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Grundlagen und Anwendungsfelder der Sozialpsychologie', 2, 2.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Einführung in die empirische Sozialforschung I', 4, 7.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Einführung in die empirische Sozialforschung II', 4, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Computergestützte Datenanalyse', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Absatz / Makroökonomie', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Investition / Mikroökonomik', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Produktion / Wirtschaft und Staat', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
                                                                                                                                        
-- Schluesselqualifikationen (mgrpid = 2)                                                                                                   
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 2, 'Planspiel Sozialökonomik', 2, 2.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 2, 'Präsentation und Moderation', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 2, 'Sprachen (1)', 4, 5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 2, 'Sprachen (2)', 4, 5, 'both', 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 3, 'Empirische Methoden und Statistik I', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 3, 'Empirische Methoden und Statistik II', 2, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 3, 'Personal und Organisation I', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 3, 'Personal und Organisation II', 2, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 3, 'Kommunikation und Massenmedien I', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 3, 'Kommunikation und Massenmedien II', 2, 5, 'ss', 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'SozÖk Vertiefungsblock I (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'SozÖk Vertiefungsblock I (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'SozÖk Vertiefungsblock II (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'SozÖk Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'Allg. Vertiefungsblock I (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'Allg. Vertiefungsblock I (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (6, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');


-- 
-- Stammdaten für Schwerpunkt SozOek, International
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Soziologie I', 4, 7.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Soziologie II', 4, 7.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Internationale und transnationale Beziehungen', 4, 7.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Regionen im internationalen System', 4, 7.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Grundlagen und Anwendungsfelder der Sozialpsychologie', 2, 2.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Einführung in die empirische Sozialforschung I', 4, 7.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Einführung in die empirische Sozialforschung II', 4, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Computergestützte Datenanalyse', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Absatz / Makroökonomie', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Investition / Mikroökonomik', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Produktion / Wirtschaft und Staat', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
                                                                                                                                        
-- Schluesselqualifikationen (mgrpid = 2)                                                                                                
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 2, 'Planspiel Sozialökonomik', 2, 2.5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 2, 'Präsentation und Moderation', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 2, 'Sprachen (1)', 4, 5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 2, 'Sprachen (2)', 4, 5, 'both', 4, 'false');
                                                                                                                                       
-- Kernbereich (mgrpid = 3)                                                                                                            
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Sprachen II für Sozialökonomik (1)', 4, 5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Sprachen II für Sozialökonomik (2)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Europäisches und internationales Recht', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Internationale Kommunikation', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Angelsächsischer oder romanischer Schwerpunkt I', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Angelsächsischer oder romanischer Schwerpunkt II', 4, 5, 'ss', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Europäisierung und Globalisierung I', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 3, 'Europäisierung und Globalisierung II', 4, 5, 'ss', 6, 'false');

                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 4, 'SozÖk Vertiefungsblock (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 4, 'SozÖk Vertiefungsblock (2)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 4, 'Allg. Vertiefungsblock (1)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 4, 'Allg. Vertiefungsblock (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (7, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');


-- 
-- Stammdaten für Schwerpunkt International Business Studies
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Unternehmensplanspiel', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Unternehmer und Unternehmen', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Buchführung', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'IT und E-Business', 4, 5, 'ws', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Internet-Praktikum', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Mathematik I', 4, 5, 'both', 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Mathematik II', 4, 5, 'both', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Statistik', 6, 7.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Absatz', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Jahresabschluss', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Makroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Mikroökonomie', 4, 5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Wirtschaft und Staat', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Privat- und Handelsrecht I', 2, 2.5, 'ss', 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Privat- und Handelsrecht II', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 1, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss', 4, 'false');
                                                                                                                                        
-- Schluesselqualifikation (mgrpid = 2)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 2, 'Sprachen (1)', 2, 2.5, 'both', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 2, 'Sprachen (2)', 2, 2.5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 2, 'Sprachen (3)', 4, 5, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 2, 'Präsentationsfähigkeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 2, 'Einführung in wissenschaftliches Arbeiten', 2, 2.5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 2, 'Praxis der emp. Wirtschaftsforschung', 4, 5, 'ss', 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 3, 'Außenwirtschaft', 4, 5, 'ws', 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 3, 'Internationales Recht I', 4, 5, 'ss', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 3, 'Sprachen II für IBS', 4, 5, 'ws', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 3, 'Internationale Unternehmensführung', 4, 5, 'ss', 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Int. Vertiefungsblock I (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Int. Vertiefungsblock I (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Int. Vertiefungsblock II (1)', 4, 5, 'both', 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Int. Vertiefungsblock II (2)', 4, 5, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Auslandsblock I', 8, 10, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Auslandsblock II', 8, 10, 'both', 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Seminar zur Bachelorarbeit', 2, 3, 'both', 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `name`, `sws`, `ects`, `angebot_semester`, `default_semester`, `assessment`) VALUES (8, 4, 'Bachelorarbeit', 8, 12, 'both', 6, 'false');
