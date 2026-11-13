-- Données de test (les mêmes que config/dev_dummy_data.json)

INSERT INTO Profil (pseudo, prenom, nom, email, nbTouites) VALUES
('E.Mousque', 'Eloun', 'Mousque', 'mousque.eloun@touiteur.fr', 3),
('PesosDu32', 'George', 'Pesos', 'george.pesos@dollar.us', 2),
('M.Sucreberg', 'Marc', 'Sucreberg', 'marc.sucreberg@example.test', 2),
('AdaLovebug', 'Ada', 'Lovebug', 'ada.lovebug@example.test', 3);

INSERT INTO Tag (idTag, libelle, descriptionTag) VALUES
(1, 'Edison', 'La marque de véhicules électrique d''Eloun Mousque.'),
(2, 'greve', ''),
(3, 'bug', 'Un petit problème qui attend souvent le commit pour apparaitre.'),
(4, 'cafe', 'La ressource indispensable au démarrage de toute équipe de dev.'),
(5, 'metaveste', 'Un monde virtuel ou même les réunions sont froides.'),
(6, 'algorithme', 'Une suite d''instructions qui decide parfois ce que vous allez regarder ensuite.');

INSERT INTO Touite (idTouite, pseudo, message, dateTouite) VALUES
(1, 'E.Mousque', 'J''ai lancé ma marque de véhicules électriques #Edison. Ils ont une autonomie incroyable : ils arrivent jusqu''à la prise la plus proche !', '2001-01-01 00:00:00'),
(2, 'PesosDu32', 'J''ai promis de faire une #greve jusqu''à ce que la machine à café arrête de distribuer de l''eau chaude goût clavier.', '2001-01-02 00:00:00'),
(3, 'E.Mousque', 'Mon véhicule #Edison est tellement silencieux que même mon voisin pense que je suis encore en télétravail.', '2001-01-03 00:00:00'),
(4, 'PesosDu32', 'Réunion à 8h : c''est moi qui l''ai organisée, mais je m''en suis rappelé à 9h.', '2001-01-04 00:00:00'),
(5, 'E.Mousque', 'J''ai demandé à mon GPS le chemin vers le succès. Il m''a répondu : faites demi-tour dès que possible. #Edison', '2001-01-05 00:00:00'),
(6, 'AdaLovebug', 'J''ai corrige un #bug tellement ancien qu''il avait probablement un compte retraite.', '2001-01-06 00:00:00'),
(7, 'M.Sucreberg', 'Le #cafe est pret. Le code aussi, mais lui a besoin de trois recompilations.', '2001-01-07 00:00:00'),
(8, 'AdaLovebug', 'Un nouveau #bug est apparu juste après que j''ai dit : ca devrait fonctionner.', '2001-01-08 00:00:00'),
(9, 'M.Sucreberg', 'Lancer des tests ça marche pas. Lancez vos ordinateurs à la place. #metaveste', '2001-01-09 00:00:00'),
(10, 'AdaLovebug', 'Bonne nouvelle : le #bug est parti. Mauvaise nouvelle : le projet ne marche tout simplement pas. #algorithme', '2001-01-10 00:00:00');

INSERT INTO TouiteTag (idTouite, idTag) VALUES
(1, 1), (2, 2), (3, 1), (5, 1), (6, 3), (7, 4), (8, 3), (9, 5), (10, 3), (10, 6);

-- pseudoAbonne suit pseudoSuivis
INSERT INTO Abonnes (pseudoSuivis, pseudoAbonne) VALUES
('PesosDu32', 'E.Mousque'),
('E.Mousque', 'PesosDu32'),
('E.Mousque', 'M.Sucreberg'),
('M.Sucreberg', 'AdaLovebug'),
('PesosDu32', 'AdaLovebug');

INSERT INTO AbonnesTag (pseudo, idTag) VALUES
('E.Mousque', 1),
('PesosDu32', 2),
('M.Sucreberg', 5),
('M.Sucreberg', 4),
('AdaLovebug', 3),
('AdaLovebug', 6);
