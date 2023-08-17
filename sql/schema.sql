DROP DATABASE IF EXISTS contacts;

CREATE DATABASE contacts; 

USE contacts;

CREATE TABLE IF NOT EXISTS categories(
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL,
  created DATE DEFAULT NOW() NOT NULL,
  PRIMARY KEY (id)
);

INSERT INTO categories(name, created) VALUES('default', NOW());

CREATE TABLE IF NOT  EXISTS contacts(
  id int(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL,
  paternal_last_name VARCHAR(60) NULL,
  maternal_last_name VARCHAR(60) NULL,
  phone_number VARCHAR(20) NOT NULL,
  email VARCHAR(100) NULL,
  image VARCHAR(60) NULL,
  category_id INT(11) NOT NULL,
  PRIMARY KEY (id),
  KEY category_id (category_id),
  CONSTRAINT category_FK FOREIGN KEY (category_id) REFERENCES categories (id)
);