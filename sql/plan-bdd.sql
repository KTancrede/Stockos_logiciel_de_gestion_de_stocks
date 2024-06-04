-- Création de la base de données
CREATE DATABASE stockos;

-- Utilisation de la base de données
USE stockos;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- ID utilisateur unique et clé primaire
    login VARCHAR(50) NOT NULL UNIQUE,               -- Login obligatoire et unique
    mot_de_passe VARCHAR(255) NOT NULL,              -- Mot de passe obligatoire
    nom VARCHAR(100),                                -- Nom optionnel
    prenom VARCHAR(100),                             -- Prénom optionnel
    email VARCHAR(100) UNIQUE,                       -- Email optionnel mais unique s'il est fourni
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP-- Date de création par défaut à l'heure actuelle
);

-- Table des fournisseurs
CREATE TABLE fournisseur(
    nom VARCHAR(30) PRIMARY KEY,                     -- Nom du fournisseur comme clé primaire
    email VARCHAR(100) UNIQUE,                       -- Email du fournisseur unique
    numero_telephone VARCHAR(20)                     -- Numéro de téléphone du fournisseur
);

-- Table des produits
CREATE TABLE produits(
    nom VARCHAR(30),                                 -- Nom du produit
    type VARCHAR(30),                                -- Type du produit
    marque VARCHAR(30),                              -- Marque du produit
    quantite NUMERIC(4,2),                           -- Quantité du produit
    fournisseur VARCHAR(30),                         -- Nom du fournisseur associé
    prix NUMERIC(6,2),                               -- Prix du lot de produit
    CONSTRAINT pk_stock PRIMARY KEY(nom, marque, quantite), -- Contrainte de clé primaire composée
    CONSTRAINT fk_emp FOREIGN KEY (fournisseur) REFERENCES fournisseur(nom) -- Contrainte de clé étrangère
);


