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
