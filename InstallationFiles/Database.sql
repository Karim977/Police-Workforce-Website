--
-- Table structure for table `People`
--

DROP TABLE IF EXISTS People;
CREATE TABLE People (
  `People_ID` int(11) AUTO_INCREMENT,
  `People_name` varchar(50) NOT NULL,
  `People_address` varchar(50) DEFAULT NULL,
  `People_YOB` year(4) DEFAULT NULL,
  `People_licence` char(16) NOT NULL,
  PRIMARY KEY (`People_ID`),
  UNIQUE KEY `People_licence` (`People_licence`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `People`
--

INSERT INTO People VALUES (1,'James Smith','23 Barnsdale Road, Leicester',1997,'SMITH92LDOFJJ829'),
(2,'Jennifer Allen','46 Bramcote Drive, Nottingham',1989,'ALLEN88K23KLR9B3'),
(3,'John Myers','323 Derby Road, Nottingham',1948,'MYERS99JDW8REWL3'),
(4,'James Smith','26 Devonshire Avenue, Nottingham',1993,'SMITHR004JFS20TR'),
(5,'Terry Brown','7 Clarke Rd, Nottingham',2002,'BROWND3PJJ39DLFG'),
(6,'Mary Adams','38 Thurman St, Nottingham',2003,'ADAMSH9O3JRHH107'),
(7,'Neil Becker','6 Fairfax Close, Nottingham',2005,'BECKE88UPR840F9R'),
(8,'Angela Smith','30 Avenue Road, Grantham',1970,'SMITH222LE9FJ5DS'),
(9,'Xene Medora','22 House Drive, West Bridgford',1966,'MEDORH914ANBB223');

--
-- Table structure for table `Offence`
--

DROP TABLE IF EXISTS Offence;
CREATE TABLE Offence (
  `Offence_ID` int(11) AUTO_INCREMENT,
  `Offence_description` varchar(500) NOT NULL,
  `Offence_maxFine` int(11) NOT NULL,
  `Offence_maxPoints` int(11) NOT NULL,
  PRIMARY KEY (`Offence_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Offence`
--

INSERT INTO Offence VALUES 
(1,'Speeding',1000,3),
(2,'Speeding on a motorway',2500,6),
(3,'Seat belt offence',500,0),
(4,'Illegal parking',500,0),
(5,'Drink driving',10000,11),
(6,'Driving without a licence',10000,0),
(7,'Driving without a licence',10000,0),
(8,'Traffic light offences',1000,3),
(9,'Cycling on pavement',500,0),
(10,'Failure to have control of vehicle',1000,3),
(11,'Dangerous driving',1000,11),
(12,'Careless driving',5000,6),
(13,'Dangerous cycling',2500,0);


--
-- Table structure for table `Vehicle`
--

DROP TABLE IF EXISTS Vehicle;
CREATE TABLE Vehicle (
  `Vehicle_ID` int(11) AUTO_INCREMENT,
  `Vehicle_make` varchar(20) NOT NULL,
  `Vehicle_model` varchar(20) NOT NULL,
  `Vehicle_colour` varchar(20) NOT NULL,
  `Vehicle_licence` char(7) NOT NULL,
  PRIMARY KEY (`Vehicle_ID`),
  UNIQUE KEY `Vehicle_licence` (`Vehicle_licence`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Vehicle`
--

INSERT INTO Vehicle VALUES
(12,'Ford', 'Fiesta','Blue','LB15AJL'),
(13,'Ferrari', '458','Red','MY64PRE'),
(14,'Vauxhall' ,'Astra','Silver','FD65WPQ'),
(15,'Honda', 'Civic','Green','FJ17AUG'),
(16,'Toyota', 'Prius','Silver','FP16KKE'),
(17,'Ford', 'Mondeo','Black','FP66KLM'),
(18,'Ford', 'Focus','White','DJ14SLE'),
(20,'Nissan', 'Pulsar','Red','NY64KWD'),
(21,'Renault', 'Scenic','Silver','BC16OEA'),
(22,'Hyundai', 'i30','Grey','AD223NG');

--
-- Table structure for table `Ownership`
--

DROP TABLE IF EXISTS Ownership;
CREATE TABLE Ownership (
  People_ID int(11) NOT NULL,
  Vehicle_ID int(11) NOT NULL,
  PRIMARY KEY (`People_ID`, `Vehicle_ID`),
  KEY `fk_ownership_vehicle` (`Vehicle_ID`),
  KEY `fk_ownership_people` (`People_ID`),
  CONSTRAINT `fk_ownership_people` FOREIGN KEY (`People_ID`) REFERENCES `People` (`People_ID`),
  CONSTRAINT `fk_ownership_vehicle` FOREIGN KEY (`Vehicle_ID`) REFERENCES `Vehicle` (`Vehicle_ID`),
  -- This forces the relationship between Vehicle and Ownership to be 1:1. Because a vehicle cannot be owned by multiple owners
  UNIQUE KEY `Vehicle_ID` (`Vehicle_ID`)

) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES
(3, 12),
(8, 20),
(4, 15),
(4, 13),
(1, 16),
(2, 14),
(5, 17),
(6, 18),
(7, 21);

--
-- Table structure for table `Login`
--

DROP TABLE IF EXISTS Login;
CREATE TABLE Login (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Login`
--

INSERT INTO Login VALUES 
(1,'mcnulty','plod123'),
(2,'moreland','fuzz42'),
(3, 'daniels', 'copper99');


--
-- Table structure for table `Incident`
--

DROP TABLE IF EXISTS Incident;
CREATE TABLE Incident (
  `Incident_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Vehicle_ID` int(11) NOT NULL,
  `People_ID` int(11) NOT NULL,
  `Incident_Date` date NOT NULL,
  `Incident_Report` varchar(500) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Offence_ID` int(11) NOT NULL,
  PRIMARY KEY (`Incident_ID`),
  KEY `fk_incident_vehicle` (`Vehicle_ID`),
  KEY `fk_incident_people` (`People_ID`),
  KEY `fk_incident_offence` (`Offence_ID`),
  KEY `fk_incident_reporter` (`User_ID`),
  CONSTRAINT `fk_incident_people` FOREIGN KEY (`People_ID`) REFERENCES `People` (`People_ID`),
  CONSTRAINT `fk_incident_offence` FOREIGN KEY (`Offence_ID`) REFERENCES `Offence` (`Offence_ID`),
  CONSTRAINT `fk_incident_reporter` FOREIGN KEY (`User_ID`) REFERENCES `Login` (`User_ID`),
  CONSTRAINT `fk_incident_vehicle` FOREIGN KEY (`Vehicle_ID`) REFERENCES `Vehicle` (`Vehicle_ID`),
-- This forces any user NOT to insert the SAME exact incident again OR if another user saw the same exact incident and both users tried to add the same incident, it will be added only once
  UNIQUE `unique_incident` (`Vehicle_ID`, `People_ID`, `Offence_ID`, `Incident_Date`, `Incident_Report`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Incident`
--

INSERT INTO Incident VALUES 
(1,15,4,'2017-12-01','40mph in a 30 limit',1,1),
(2,20,8,'2017-11-01','Double parked',2,4),
(3,13,4,'2017-09-17','110mph on motorway',2,1),
(4,14,2,'2017-08-22','Failure to stop at a red light - travelling 25mph',2,8),
(5,13,4,'2017-10-17','Not wearing a seatbelt on the M1',1,3);

--
-- Table structure for table `Fines`
--

DROP TABLE IF EXISTS Fines;
CREATE TABLE Fines (
  Fine_ID int(11) NOT NULL AUTO_INCREMENT,
  Fine_Amount int(11) NOT NULL,
  Fine_Points int(11) NOT NULL,
  Incident_ID int(11) NOT NULL,
  PRIMARY KEY (Fine_ID),
  KEY fk_fines_incident (Incident_ID),
  CONSTRAINT fk_fines_incident FOREIGN KEY (Incident_ID) REFERENCES Incident (Incident_ID),
  -- This forces the relationship between Incident and Fines to be 1:1. Because an Incident_ID resembles one specific situation having a specific vehicle, person, offence, etc..
  -- One person could commit another incident with the same vehicle and the same offence but then it would have another Incident_ID due to being present in another date
  UNIQUE KEY `Incident_ID` (`Incident_ID`)
);

--
-- Dumping data for table `Fines`
--

INSERT INTO Fines VALUES 
(1,2000,6,3),
(2,50,0,2),
(3,500,3,4);



/*
--
-- Table structure for table `Reporter`
--

DROP TABLE IF EXISTS Reporter;
CREATE TABLE Reporter (
  `Reporter_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Reporter_name` varchar(255) NOT NULL,
  `Reporter_job` varchar(255) NOT NULL,
  PRIMARY KEY (`Reporter_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Reporter`
--

INSERT INTO Reporter VALUES
(1,'Ben Shmidt','Administrator'),
(2,'Boris Walker','Deputy Chief Constable'),
(3,'John Smith','Assistant Chief Constable'),
(4,'Ben Truss','Chief Superintendent'),
(5,'Wall Leeds','Assistant Chief Constable');

*/



--
-- Table structure for table `Audit_trails`
--

DROP TABLE IF EXISTS Audit_Fines;
CREATE TABLE Audit_Fines (
  `Audit_Fines_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` TIMESTAMP NOT NULL,
  `User_ID` INT(11) NOT NULL,
  `Action` CHAR(6) NOT NULL,
  `NEW_Fine_Amount` INT(11) NOT NULL,
  `NEW_Fine_Points` INT(11) NOT NULL,
  `Incident_Report` VARCHAR(500),
  PRIMARY KEY (`Audit_Fines_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS Audit_Incident;
CREATE TABLE Audit_Incident (
  `Audit_Incident_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` TIMESTAMP NOT NULL,
  `User_ID` INT(11) NOT NULL,
  `Action` CHAR(6) NOT NULL,
  `OLD_Incident_Report` VARCHAR(500),
  `NEW_Incident_Report` VARCHAR(500),
  `OLD_Incident_Date` DATE,
  `NEW_Incident_Date` DATE,
  `OLD_Vehicle_licence` char(7),
  `NEW_Vehicle_licence` char(7),
  `OLD_Owner_licence` char(16),	
  `NEW_Owner_licence` char(16),
  `OLD_Offence_description` VARCHAR(500),
  `NEW_Offence_description` VARCHAR(500),
  PRIMARY KEY (`Audit_Incident_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS Audit_Login;
CREATE TABLE Audit_Login (
  `Audit_Login_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` TIMESTAMP NOT NULL,
  `User_ID` INT(11) NOT NULL,
  `Action` CHAR(6) NOT NULL,
  `OLD_username` VARCHAR(100),
  `NEW_username` VARCHAR(100),
  `OLD_password` VARCHAR(100),
  `NEW_password` VARCHAR(100),
  PRIMARY KEY (`Audit_Login_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS Audit_Vehicle;
CREATE TABLE Audit_Vehicle (
  `Audit_Vehicle_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` TIMESTAMP NOT NULL,
  `User_ID` INT(11) NOT NULL,
  `Action` CHAR(6) NOT NULL,
  `OLD_Vehicle_make` VARCHAR(20),
  `NEW_Vehicle_make` VARCHAR(20),
  `OLD_Vehicle_model` VARCHAR(20),
  `NEW_Vehicle_model` VARCHAR(20),
  `OLD_Vehicle_colour` VARCHAR(20),
  `NEW_Vehicle_colour` VARCHAR(20),
  `OLD_Vehicle_licence` CHAR(7),
  `NEW_Vehicle_licence` CHAR(7),
  PRIMARY KEY (`Audit_Vehicle_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS Audit_Owner;
CREATE TABLE Audit_Owner (
  `Audit_Owner_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` TIMESTAMP NOT NULL,
  `User_ID` INT(11) NOT NULL,
  `Action` CHAR(6) NOT NULL,
  `OLD_owner_name` VARCHAR(50),
  `NEW_owner_name` VARCHAR(50),
  `OLD_owner_address` VARCHAR(50),
  `NEW_owner_address` VARCHAR(50),
  `OLD_owner_YOB` YEAR(4),
  `NEW_owner_YOB` YEAR(4),
  `OLD_owner_licence` CHAR(16),
  `NEW_owner_licence` CHAR(16),
  PRIMARY KEY (`Audit_Owner_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS Audit_Ownership;
CREATE TABLE Audit_Ownership (
  `Audit_Ownership_ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` TIMESTAMP NOT NULL,
  `User_ID` INT(11) NOT NULL,
  `Action` CHAR(6) NOT NULL,
  `Owner_licence` char(16),
  `Vehicle_licence` char(7),
  PRIMARY KEY (`Audit_Ownership_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
