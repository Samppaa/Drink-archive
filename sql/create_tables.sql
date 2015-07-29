-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Users
(
id int PRIMARY KEY,
name varchar(60) NOT NULL,
password varchar(60) NOT NULL,
type int NOT NULL
);

CREATE TABLE Drinks
(
id int PRIMARY KEY,
name varchar(60) NOT NULL,
descrition varchar(255),
author int NOT NULL REFERENCES Users,
time_added date NOT NULL,
type varchar(60) NOT NULL
);

CREATE TABLE Ingredients
(
id int PRIMARY KEY,
name varchar(60) NOT NULL
);

CREATE TABLE Tags
(
id int NOT NULL PRIMARY KEY,
word varchar(60) NOT NULL
);

CREATE TABLE Drink_Ingredients
(
ingredient_id int NOT NULL REFERENCES Ingredients,
drink_id int NOT NULL REFERENCES Drinks,
amount varchar(10) NOT NULL,
PRIMARY KEY (ingredient_id, drink_id, amount)
);

CREATE TABLE Drink_Tags
(
tag_id int NOT NULL REFERENCES Tags,
drink_id int NOT NULL REFERENCES Drinks,
PRIMARY KEY (tag_id, drink_id)
);
