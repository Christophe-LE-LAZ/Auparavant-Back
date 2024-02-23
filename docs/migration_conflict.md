# Fix migration conflict

## Etape 1 : Supprimer toutes les tables

```bash
php bin/console doctrine:schema:drop --full-database --force
```

## Etape 2 : Supprimer tous les fichiers de migrations

Soit depuis VSCode aller dans le dossier de migrations et supprimer tous les fichiers qui commencent par Version...
Ou alors, depuis la racine du projet symfony :

```bash
rm -rf migrations/*
```

## Etape 3 : Relancer les migrations

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
