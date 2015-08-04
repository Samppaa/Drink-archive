-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Users
(
id SERIAL PRIMARY KEY,
name varchar(14) NOT NULL,
password varchar(16) NOT NULL,
type int NOT NULL
);

CREATE TABLE Drinks
(
id SERIAL PRIMARY KEY,
name varchar(25) NOT NULL,
description varchar(255),
author int NOT NULL REFERENCES Users,
time_added date NOT NULL,
type varchar(60) NOT NULL,
waiting_acceptance int NOT NULL DEFAULT 1
);

CREATE TABLE Ingredients
(
id SERIAL PRIMARY KEY,
name varchar(30) NOT NULL
);

CREATE TABLE Tags
(
id SERIAL PRIMARY KEY,
word varchar(30) NOT NULL
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
