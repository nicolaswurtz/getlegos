# GetLEGOs

Petit outil PHP **en ligne de commande (CLI)** pour récupérer les notices de LEGO facilement avec leurs images miniatures, bien rangées dans des dossiers.

Deux modes sont proposés, soit récupération ponctuelle (une boîte à la fois), soit récupération d'une liste complète de manière séquentielle pour pas trop charger le cdn de LEGO.
Un test est effectué sur la présence de la photo principale *id.photo.jpg* pour pouvoir reprendre en cas d'erreur.

> La récupération est réalisée sur le format V29 principalement (format A4).

### Syntaxe

Récupère la/les notices de la boîte 7939

	php getlegos.php 7939

Récupère les notices des boîtes dont le numéro figure en première colonne du fichier .csv

	php getlegos.php fichier.csv

### Sortie
GetLEGOs produit systématiquement un fichier out.csv (avec une ligne par fichier pdf avec tous les détails nécessaires pour créer par exemple un fichier html qui renvoie vers notices, images et numéros) qui est amendé à chaque lancement, et produit côté console les actions menées.
Pas ou peu de contrôle d'erreur, deal with it :)

### Dépendances
Via composer : https://climate.thephpleague.com/ (pour coloriser la sortie CLI)

	composer require league/climate

*Ceci n'a strictement rien à voir avec LEGO GROUP TM etc.*
*Sous licence WTFPL.*
