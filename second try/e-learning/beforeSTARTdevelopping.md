Comment tes amis peuvent l’utiliser ?
Cloner le dépôt
Ils récupèrent le code source avec git clone.

Copier .env.example en .env.local
Ils créent un fichier .env.local à la racine du projet en copiant .env.example :

bash
Copier
Modifier
cp .env.example .env.local
Ou sous Windows PowerShell :

powershell
Copier
Modifier
copy .env.example .env.local
Modifier .env.local si besoin
Par exemple, changer la configuration de la base de données s’ils ont une autre configuration (mot de passe, port, host, etc.).

Installer les dépendances (si ce n’est pas déjà fait) :

bash
Copier
Modifier
composer install
Lancer les migrations (si base de données partagée, vérifier avec l’équipe avant d’appliquer) :

bash
Copier
Modifier
php bin/console doctrine:migrations:migrate
Lancer le serveur local :

bash
Copier
Modifier
symfony server:start
Pourquoi utiliser .env.local et pas .env ?
Le fichier .env est versionné dans git (commit), il sert de base commune.

.env.local est personnel, non versionné (dans .gitignore), chacun peut configurer son environnement sans impacter les autres.



composer require stripe/stripe-php   

composer require 