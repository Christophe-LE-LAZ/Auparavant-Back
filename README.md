## O mais y'avait quoi avant ? :statue_of_liberty: O'but what did there use to be?

1. Copier coller le fichier .env.dist et le renommer en .env
2. Configurer le fichier .env avec les informations relatives à son SGBD
3. Éxécuter dans le terminal un ```bash composer install``` à la racine du dossier
4. Éxécuter dans le terminal un ```bash composer update``` à la racine du dossier
5. Éxécuter dans le terminal : ```php bin/console doctrine:database:create```
6. Éxécuter dans le terminal : ```php bin/console make:migration```
7. Éxécuter dans le terminal : ```php bin/console doctrine:migrations:migrate```
8. Recopier dans votre SGBD le contenu du fichier 'yavaitquoiavant.sql' qui se trouve dqns le dossier 'docs' du repository, et éxécuter le script sql afin de charger de la data dans votre base de données

If you ever speak only English... :point_down:

* Copy and paste the .env.dist file and rename it to .env
* Configure .env file with DBMS information (DBMS stands for Database Management System)
* Run ```bash composer install``` in the terminal at the root of the folder
* Run ```bash composer update``` in the terminal at the root of the folder
* Run in terminal: ```php bin/console doctrine:database:create```
* Run in terminal: ```php bin/console make:migration```
* Run in terminal: ```php bin/console doctrine:migrations:migrate```
* Copy the contents of the 'yavaitquoiavant.sql' file, located in the 'docs' folder of the repository, into your DBMS, and run the sql script to load data into your database
