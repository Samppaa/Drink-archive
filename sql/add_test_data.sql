-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO Users VALUES (0, 'Samppaa', 'Test', 3);
INSERT INTO Ingredients VALUES (0, 'Apple juice');
INSERT INTO Ingredients VALUES (1, 'Lemon');
INSERT INTO Drinks VALUES (0, 'Lemon apple drink', 'Description', 0, date '2015-Jan-08', 'Cocktail', 1);
INSERT INTO Drink_Ingredients VALUES (0, 0, '1l');
INSERT INTO Drink_Ingredients VALUES (1, 0, '1 piece');
INSERT INTO Tags VALUES (0, 'Omena juoma');
INSERT INTO Tags VALUES (1, 'Sitruuna juoma');
INSERT INTO Drink_Tags VALUES (0, 0);
INSERT INTO Drink_Tags VALUES (1, 0);