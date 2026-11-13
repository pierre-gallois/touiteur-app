-- Création des tables de Touiteur (MySQL / MariaDB)
-- A importer dans phpMyAdmin, puis importer donnees.sql pour avoir les données de test

DROP TABLE IF EXISTS Note, TouiteTag, AbonnesTag, Abonnes, Touite, Image, Tag, Profil;

CREATE TABLE Profil (
    pseudo VARCHAR(20) NOT NULL,
    prenom VARCHAR(20) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    nbTouites INT NOT NULL DEFAULT 0,
    CONSTRAINT PK_Profil PRIMARY KEY (pseudo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Tag (
    idTag INT NOT NULL AUTO_INCREMENT,
    libelle VARCHAR(20) NOT NULL,
    descriptionTag VARCHAR(255),
    CONSTRAINT PK_Tag PRIMARY KEY (idTag),
    CONSTRAINT UQ_TagLibelle UNIQUE (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Image (
    idImage INT NOT NULL AUTO_INCREMENT,
    chemin VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    CONSTRAINT PK_Image PRIMARY KEY (idImage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Touite (
    idTouite INT NOT NULL AUTO_INCREMENT,
    pseudo VARCHAR(20) NOT NULL,
    message VARCHAR(235) NOT NULL,
    dateTouite DATETIME NOT NULL,
    idImage INT NULL,
    CONSTRAINT PK_Touite PRIMARY KEY (idTouite),
    CONSTRAINT FK_TouitePseudo FOREIGN KEY (pseudo) REFERENCES Profil (pseudo),
    CONSTRAINT FK_TouiteImage FOREIGN KEY (idImage) REFERENCES Image (idImage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- pseudoAbonne suit pseudoSuivis
CREATE TABLE Abonnes (
    pseudoSuivis VARCHAR(20) NOT NULL,
    pseudoAbonne VARCHAR(20) NOT NULL,
    CONSTRAINT PK_Abonnes PRIMARY KEY (pseudoSuivis, pseudoAbonne),
    CONSTRAINT FK_AbonnesSuivis FOREIGN KEY (pseudoSuivis) REFERENCES Profil (pseudo),
    CONSTRAINT FK_AbonnesAbonne FOREIGN KEY (pseudoAbonne) REFERENCES Profil (pseudo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE AbonnesTag (
    pseudo VARCHAR(20) NOT NULL,
    idTag INT NOT NULL,
    CONSTRAINT PK_AbonnesTag PRIMARY KEY (pseudo, idTag),
    CONSTRAINT FK_AbonnesTagPseudo FOREIGN KEY (pseudo) REFERENCES Profil (pseudo),
    CONSTRAINT FK_AbonnesTagTag FOREIGN KEY (idTag) REFERENCES Tag (idTag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE TouiteTag (
    idTouite INT NOT NULL,
    idTag INT NOT NULL,
    CONSTRAINT PK_TouiteTag PRIMARY KEY (idTouite, idTag),
    CONSTRAINT FK_TouiteTagTouite FOREIGN KEY (idTouite) REFERENCES Touite (idTouite),
    CONSTRAINT FK_TouiteTagTag FOREIGN KEY (idTag) REFERENCES Tag (idTag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note d'un utilisateur sur un touite (+1 approuvé, -1 rejeté), une seule fois par touite
CREATE TABLE Note (
    pseudo VARCHAR(20) NOT NULL,
    idTouite INT NOT NULL,
    valeur INT NOT NULL,
    CONSTRAINT PK_Note PRIMARY KEY (pseudo, idTouite),
    CONSTRAINT FK_NotePseudo FOREIGN KEY (pseudo) REFERENCES Profil (pseudo),
    CONSTRAINT FK_NoteTouite FOREIGN KEY (idTouite) REFERENCES Touite (idTouite),
    CONSTRAINT CK_NoteValeur CHECK (valeur = 1 OR valeur = -1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
