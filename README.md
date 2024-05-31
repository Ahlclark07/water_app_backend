## - Démarrez le serveur laravel
 
  ### Mise en place :
  Lancez le en suivant ces étapes :

1. Ouvrez le port 8000 ou autre port disponible souhaité sur votre machine (voir des tutos sur google).
2. Allez à la racine du projet laravel
3. Faites : composer install
4. Faites : npm install
5. Pour générer le fichier .env à partir de l'exemple : **cp .env.example .env**
6. Faites : **php artisan generate:key**
7. Le serveur se sert de sqlite donc pas de config necéssaire, faites : **php artisan migrate**
8. Pour démarrer le serveur à partir de votre ip local (192.168.1.101 par exemple et 7777 comme port ouvert) faites : **php artisan serve --host=192.168.1.101 --port=7777**
9. Enfin mettez cette ip dans lib/utils/laravel_backend.dart à la ligne 8 à l'intérieur de la variable base url
10. Lancez la commande **php artisan route:list** pour voir la liste des routes
## Liste des urls utiles :

| Type  | URL | Entrée  | Sortie | Description |
| ------------- | ------------- | ------------- | ------------- | ------------- |
| POST  | /login | id_compteur, password  | {user, token}  |Permet de se connecter |
| POST  | /register | id_compteur, password, nom, prenoms, id, tel  | {user, token}  |Permet de s'inscrire |
| POST  | /users/abonnement | titre, total, consommation  | {message, abonnement}  | Renvoi un message success, et l'objet abonnement qui est donc l'abonnement actuel |
| GET  | /users/abonnement | rien  | {abonnement}  |Permet de récupérer l'abonnement en cours |
| POST  | /users/consommation | date, consommation  | {message, consommation}  | Renvoi un message success, et la consommation du jour. |
| GET  | /users/consommation | rien  | {abonnement}  |Permet de récupérer la liste des consommations des 7 derniers jours. |
| POST  | /users/notifications | type, message  | {message}  | Renvoi un message success. A propos du type en entrée, il sert surtout à afficher des icones spécifiques dans l'app, mettre "recharge" s'il s'agit d'une recharge |
| GET  | /users/notifications | rien  | {notifications}  |Permet de récupérer la liste des 7 dernières notifications. |


                                
