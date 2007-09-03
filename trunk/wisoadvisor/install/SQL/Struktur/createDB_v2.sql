
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







CREATE TABLE `advisor__lectures` (
  `alid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `sws` int(10) unsigned default NULL,
  `ects` float default NULL,
  `sem_angebot` enum('ws','ss','both') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`alid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=113 ;

INSERT INTO `advisor__lectures` VALUES (1, 'Unternehmensplanspiel', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (2, 'Unternehmen, Märkte, Volkswirtschaften', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (3, 'Unternehmer und Unternehmen', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (4, 'Buchführung', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (5, 'IT und E-Business', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (6, 'Internet-Praktikum', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (7, 'Mathematik I', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (8, 'Mathematik II', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (9, 'Statistik', 6, 7.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (10, 'Absatz', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (11, 'Jahresabschluss', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (12, 'Produktion, Logistik, Beschaffung', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (13, 'Makroökonomie', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (14, 'Mikroökonomie', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (15, 'Wirtschaft und Staat', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (16, 'Privat- und Handelsrecht I', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (17, 'Privat- und Handelsrecht II', 2, 2.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (18, 'Öffentliches Recht: Staat und Verwaltung', 2, 2.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (19, 'Öffentliches Recht: Europarecht', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (20, 'Sprachen (1)', 2, 2.5, 'both');
INSERT INTO `advisor__lectures` VALUES (21, 'Sprachen (2)', 2, 2.5, 'both');
INSERT INTO `advisor__lectures` VALUES (22, 'Sprachen (3)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (23, 'Präsentationsfähigkeiten', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (24, 'Einführung in wissenschaftliches Arbeiten', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (25, 'Praxis der emp. Wirtschaftsforschung', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (26, 'Kostenrechnung und Controlling', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (27, 'Internationale Unternehmensführung', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (28, 'Investition und Finanzierung', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (29, 'Planspiel / Fallstudienseminar', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (30, 'BWL Vertiefungsblock I (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (31, 'BWL Vertiefungsblock I (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (32, 'BWL Vertiefungsblock II (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (33, 'BWL Vertiefungsblock II (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (34, 'BWL Vertiefungsblock III (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (35, 'BWL Vertiefungsblock III (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (36, 'Vertiefungsblock I (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (37, 'Vertiefungsblock I (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (38, 'Seminar zur Bachelorarbeit', 2, 3, 'both');
INSERT INTO `advisor__lectures` VALUES (39, 'Bachelorarbeit', 8, 12, 'both');
INSERT INTO `advisor__lectures` VALUES (40, 'Außenwirtschaft', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (41, 'Ökonomie des öffentlichen Sektors', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (42, 'Arbeitsmarktpolitik', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (43, 'Wettbewerbstheorie und -politik', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (44, 'VWL Vertiefungsblock I (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (45, 'VWL Vertiefungsblock I (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (46, 'VWL Vertiefungsblock II (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (47, 'VWL Vertiefungsblock II (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (50, 'Vertiefungsblock II (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (51, 'Vertiefungsblock II (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (52, 'AWI: IT-gestützte Unternehmensführung', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (53, 'AWI: E-Business Management', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (54, 'AWI: IT-Management', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (55, 'SWI: Technologie- und Projektmanagement (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (56, 'SWI: Technologie- und Projektmanagement (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (57, 'SWI: Innovations- und Wertschöpfungsmanagement (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (58, 'SWI: Innovations- und Wertschöpfungsmanagement (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (59, 'SWI: Prozess-, Service- und Informationsmanagement (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (60, 'SWI: Prozess-, Service- und Informationsmanagement (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (61, 'Sprachen (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (62, 'Sprachen (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (63, 'Grundlagen der Wirtschafts- und Betriebspädagogik', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (64, 'Präsentation und Moderation I', 2, 2.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (65, 'Präsentation und Moderation II', 3, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (66, 'Berufliche Weiterbildung', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (67, 'Berufspädagogisches Seminar', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (68, 'Schulpraktische Studien / Erkundungsprojekt', 2, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (69, 'Doppelpflichtwahlfach (1)', 6, 7.5, 'both');
INSERT INTO `advisor__lectures` VALUES (70, 'Doppelpflichtwahlfach (2)', 2, 2.5, 'both');
INSERT INTO `advisor__lectures` VALUES (71, 'Doppelpflichtwahlfach (3)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (72, 'Soziologie I', 4, 7.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (73, 'Soziologie II', 4, 7.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (74, 'Internationale und transnationale Beziehungen', 4, 7.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (75, 'Regionen im internationalen System', 4, 7.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (76, 'Grundlagen und Anwendungsfelder der Sozialpsychologie', 2, 2.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (77, 'Einführung in die empirische Sozialforschung I', 4, 7.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (78, 'Einführung in die empirische Sozialforschung II', 4, 7.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (79, 'Computergestützte Datenanalyse', 2, 2.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (80, 'Absatz / Makroökonomie', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (81, 'Investition / Mikroökonomik', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (82, 'Produktion / Wirtschaft und Staat', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (83, 'Planspiel Sozialökonomik', 2, 2.5, 'ws');
INSERT INTO `advisor__lectures` VALUES (84, 'Präsentation und Moderation', 2, 2.5, 'ss');
INSERT INTO `advisor__lectures` VALUES (85, 'Empirische Methoden und Statistik I', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (86, 'Empirische Methoden und Statistik II', 2, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (87, 'Personal und Organisation I', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (88, 'Personal und Organisation II', 2, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (89, 'Kommunikation und Massenmedien I', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (90, 'Kommunikation und Massenmedien II', 2, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (91, 'SozÖk Vertiefungsblock I (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (92, 'SozÖk Vertiefungsblock I (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (93, 'SozÖk Vertiefungsblock II (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (94, 'SozÖk Vertiefungsblock II (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (95, 'Sprachen II für Sozialökonomik (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (96, 'Sprachen II für Sozialökonomik (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (97, 'Europäisches und internationales Recht', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (98, 'Internationale Kommunikation', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (99, 'Angelsächsischer oder romanischer Schwerpunkt I', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (100, 'Angelsächsischer oder romanischer Schwerpunkt II', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (101, 'Europäisierung und Globalisierung I', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (102, 'Europäisierung und Globalisierung II', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (105, 'Internationales Recht I', 4, 5, 'ss');
INSERT INTO `advisor__lectures` VALUES (106, 'Sprachen II für IBS', 4, 5, 'ws');
INSERT INTO `advisor__lectures` VALUES (107, 'Int. Vertiefungsblock I (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (108, 'Int. Vertiefungsblock I (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (109, 'Int. Vertiefungsblock II (1)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (110, 'Int. Vertiefungsblock II (2)', 4, 5, 'both');
INSERT INTO `advisor__lectures` VALUES (111, 'Auslandsblock I', 8, 10, 'both');
INSERT INTO `advisor__lectures` VALUES (112, 'Auslandsblock II', 8, 10, 'both');








-- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------




CREATE TABLE `advisor__modules` (
  `modid` int(10) unsigned NOT NULL auto_increment,
  `majid` int(10) unsigned default NULL,
  `mgrpid` int(10) unsigned default NULL,
  `alid` int(10) unsigned default NULL,
  `default_semester` int(10) unsigned default NULL,
  `assessment` enum('true','false') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`modid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


-- 
-- Stammdaten für Schwerpunkt BWL (majid=1)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  1, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  3, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  4, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  7, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  8, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 10, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 11, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 12, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 13, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 14, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 15, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 16, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 17, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 18, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 1, 19, 4, 'false');

-- Schluesselqualifikation (mgrpid = 2)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 2, 20, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 2, 21, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 2, 22, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 2, 23, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 2, 24, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 2, 25, 4, 'false');

-- Kernbereich (mgrpid = 3)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 3, 26, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 3, 27, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 3, 28, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 3, 29, 6, 'false');

-- Vertiefungsbereich (mgrpid = 4)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 30, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 31, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 32, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 33, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 34, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 35, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (1, 4, 39, 6, 'false');


-- 
-- Stammdaten für Schwerpunkt VWL (majid=2)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  1, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  3, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  4, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  7, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  8, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 10, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 11, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 12, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 13, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 14, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 15, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 16, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 17, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 18, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 1, 19, 4, 'false');
                                                                                                     
-- Schluesselqualifikation (mgrpid = 2)                                                              
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 2, 20, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 2, 21, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 2, 22, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 2, 23, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 2, 24, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 2, 25, 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 3, 40, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 3, 41, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 3, 42, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 3, 43, 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 44, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 45, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 46, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 47, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 50, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 51, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (2, 4, 39, 6, 'false');


-- 
-- Stammdaten für Schwerpunkt WI (majid=4)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  1, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  3, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  4, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  7, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  8, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 10, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 11, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 12, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 13, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 14, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 15, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 16, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 17, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 18, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 1, 19, 4, 'false');
                                                                                                     
-- Schluesselqualifikation (mgrpid = 2)                                                              
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 2, 20, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 2, 21, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 2, 22, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 2, 23, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 2, 24, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 2, 25, 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 3, 52, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 3, 53, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 3, 54, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 3, 29, 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 55, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 56, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 57, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 58, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 59, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 60, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (4, 4, 39, 6, 'false');


-- 
-- Stammdaten für Schwerpunkt WiPaed, StR I (majid=3)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  1, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  3, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  4, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  7, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  8, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 10, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 11, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 12, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 13, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 14, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 15, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 16, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 17, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 18, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 1, 19, 4, 'false');
                                                                                                                                        
-- Schluesselqualifikation (mgrpid = 2)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 2, 61, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 2, 62, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 2, 25, 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 3, 63, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 3, 64, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 3, 65, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 3, 66, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 3, 67, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 3, 68, 5, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 30, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 31, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 32, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 33, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 50, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 51, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (3, 4, 39, 6, 'false');


-- 
-- Stammdaten für Schwerpunkt WiPaed, StR II (majid=5)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  1, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  3, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  4, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  7, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  8, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 10, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 11, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 12, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 13, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 14, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 15, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 16, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 17, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 18, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 1, 19, 4, 'false');
                                                                                                                                        
-- Doppelpflichtwahlfach (mgrpid = 5)                                                                                                 
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 5, 69, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 5, 70, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 5, 71, 5, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 3, 63, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 3, 64, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 3, 65, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 3, 66, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 3, 67, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 3, 68, 5, 'false');
                                                                                                                                      
-- Vertiefungsbereich (mgrpid = 4)                                                                                                     
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 30, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 31, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 32, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 33, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 50, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 51, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (5, 4, 39, 6, 'false');



-- 
-- Stammdaten für Schwerpunkt SozOek, Verhalten
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 72, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 73, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 74, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 75, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 76, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 77, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 78, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 79, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 80, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 81, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 82, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 19, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 1, 18, 3, 'false');
                                                                                                                                        
-- Schluesselqualifikationen (mgrpid = 2)                                                                                                   
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 2, 83, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 2, 84, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 2, 61, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 2, 62, 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 3, 85, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 3, 86, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 3, 87, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 3, 88, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 3, 89, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 3, 90, 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 91, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 92, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 93, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 94, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (6, 4, 39, 6, 'false');


-- 
-- Stammdaten für Schwerpunkt SozOek, International (majid=7)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 72, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 73, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 74, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 75, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 76, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 77, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 78, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 79, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 80, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 81, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 82, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 19, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 1, 18, 3, 'false');
                                                                                                                                       
-- Schluesselqualifikationen (mgrpid = 2)                                                                                                
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 2, 83, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 2, 84, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 2, 61, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 2, 62, 4, 'false');
                                                                                                                                       
-- Kernbereich (mgrpid = 3)                                                                                                            
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 95, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 96, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 97, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 98, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 99, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 100, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 101, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 3, 102, 6, 'false');

                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 4, 91, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 4, 92, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 4, 36, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 4, 37, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (7, 4, 39, 6, 'false');


-- 
-- Stammdaten für Schwerpunkt International Business Studies (majid=8)
-- 

-- Pflichtbereich (mgrpid = 1)
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  1, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  2, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  3, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  4, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  5, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  6, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  7, 1, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  8, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1,  9, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 10, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 11, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 12, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 13, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 14, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 15, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 16, 2, 'true');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 17, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 18, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 1, 19, 4, 'false');
                                                                                                     
-- Schluesselqualifikation (mgrpid = 2)                                                              
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 2, 20, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 2, 21, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 2, 22, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 2, 23, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 2, 24, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 2, 25, 4, 'false');
                                                                                                                                        
-- Kernbereich (mgrpid = 3)                                                                                                             
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 3, 40, 3, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 3, 105, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 3, 106, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 3, 27, 6, 'false');
                                                                                                                                        
-- Vertiefungsbereich (mgrpid = 4)                                                                                                      
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 107, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 108, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 109, 4, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 110, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 111, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 112, 5, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 38, 6, 'false');
INSERT INTO `advisor__modules` (`majid`, `mgrpid`, `alid`, `default_semester`, `assessment`) VALUES (8, 4, 39, 6, 'false');





-- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





CREATE TABLE `advisor__schedule` (
  `schid` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned default NULL,
  `modid` int(10) unsigned default NULL,
  `mark_planned` float default NULL,
  `mark_real` float default NULL,
  `semester` enum('ws','ss') collate latin1_general_ci default NULL,
  `sem_year` int(10) unsigned default NULL,
  PRIMARY KEY  (`schid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;