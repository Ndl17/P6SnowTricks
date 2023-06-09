# Projet 6 OpenClassrooms - Développez de A à Z le site communautaire SnowTricks

## Score de qualité du code :
[![SymfonyInsight](https://insight.symfony.com/projects/b43a3833-730b-4fa5-9fc5-13946ed90d06/big.svg)](https://insight.symfony.com/projects/b43a3833-730b-4fa5-9fc5-13946ed90d06)
## Informations :
Thème du site choisi : Acorn - Bootstrap 5
La version en ligne du site n’est pas encore disponible.

## Identifiants pour se connecter :

#### Utilisateur Verifié :
* Identifiant : admin
* Mot de Passe : 123456


#### Utilisateur Non Vérifié :
* Identifiant : user
* Mot de Passe : 12345

## Base SQL :
Pour importer la base de donnée, récupérez le fichier 'bdd_sql' dans le dossier config.

## Prérequis :
* PHP 8.0.26, Compser, Symfony 6. 
* Pour pouvoir recevoir les mail, vous devez installer MailHog.


## Installation :
* Etape 1 : Installez l’ensemble des fichier de ce repo dans le dossier web de votre environnement local.
* Etape 2 : Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier .env.local qui devra être crée à la racine du projet en réalisant une copie du fichier .env.
* Etape 3 :  Effectuez la commande "composer install" depuis le répertoire du projet cloné
* Etape 4 : Modifiez les constantes du fichier .env  selon les information de votre bdd: 
DATABASE_URL="mysql://username:password@127.0.0.1:3306/snowtricks?serverVersion=8&charset=utf8mb4"
* Etape 5 : modifiez également  MAILER_DSN="smtp:/user:pass@smtp.example.com:25" par MAILER_DSN=smtp://localhost:1025
* Etape 6 : Effectuez la commande php bin/console doctrine:database:create pour créer la base de données 
* Etape 7 : Pour recréer la structure de la bdd, lancez la commande suivante : php bin/console doctrine:migrations:migrate
* Etape 8 : Pour recréer le jeu de donnée: php bin/console doctrine:fixtures:load
* Etape 9 : Démarrez le projet en utilisant la commande suivante : php bin/console server:start 

NB: 
*Ne pas oublier le lancer mailhog pour la récupération mdp & validation de compte.
*Utiliser les identifiants plus haut pour accéder à certaines fonctionnalités particulière du site (ajout de figures, modifications, commentaires)

## Librairies utilisées :
FakerPhp
