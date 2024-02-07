1. Copier coller le fichier .env.dist et le renommer en .env
2. Configurer le fichier .env avec les informations relatives à son SGBD
3. Éxécuter dans le terminal un ```bash composer install``` à la racine du dossier
4. Éxécuter dans le terminal un ```bash composer update``` à la racine du dossier
5. Éxécuter dans le terminal : ```php bin/console doctrine:database:create```
6. Éxécuter dans le terminal : ```php bin/console make:migration```
7. Éxécuter dans le terminal : ```php bin/console doctrine:migrations:migrate```
8. Recopier dans votre SGBD le contenu du fichier 'yavaitquoiavant.sql' qui se trouve dqns le dossier 'docs' du repository, et éxécuter le script sql afin de charger de la data dans votre base de données.

