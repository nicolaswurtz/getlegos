# GetLEGOs

Petit outil PHP pour récupérer les notices de LEGO facilement avec leurs images miniatures, bien rangées dans des dossiers.

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
