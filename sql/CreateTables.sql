CREATE TABLE CarStar.Vendors (
	id INTEGER auto_increment NOT NULL,
	name varchar(100) NOT NULL,
	phone varchar(30) NULL,
	fax varchar(20) NULL,
	address varchar(100) NULL,
	city varchar(20) NULL,
	state varchar(5) NULL,
	zipcode varchar(10) NULL,
	preferred BOOL DEFAULT FALSE NOT NULL,
	electronic BOOL DEFAULT FALSE NOT NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
AUTO_INCREMENT=1;

ALTER TABLE CarStar.Vendors ADD accountNum INT DEFAULT NULL NULL;

CREATE TABLE CarStar.Technicians (
	id TINYINT auto_increment NOT NULL,
	Name VARCHAR(20) NOT NULL,
	`Role` TINYINT NULL,
	PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE Deliveries (
		id int AUTO_INCREMENT NOT NULL,
		RONum int,
		Location varchar(15),
		Technician varchar(20),
		ReceiveDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
		Vendor varchar(30),
		Notes varchar(50)
		PRIMARY KEY (id)
	);

CREATE TABLE Vehicles (
		id int AUTO_INCREMENT NOT NULL,
		RONum int,
		Customer varchar(30),
		Vehicle varchar(30),
		PRIMARY KEY (id)
	);

CREATE TABLE Parts (
		id int AUTO_INCREMENT NOT NULL,
		RONum int,
		partNumber varchar(20),
		description varchar(40),
		PRIMARY KEY (id)
	);

ALTER TABLE Parts
ADD COLUMN deliveryID int;

ALTER TABLE CarStar.Vendors ADD accountNum INT DEFAULT NULL NULL;
