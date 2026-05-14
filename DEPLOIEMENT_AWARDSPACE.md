# Deploiement sur AwardSpace

## Identifiants admin par defaut

- Email : `admin@gmail.com`
- Mot de passe : `admin123`

## Etapes

1. Dans AwardSpace, cree un site ou un sous-domaine.
2. Dans `Database Manager`, cree une base MySQL.
3. Note les 4 informations MySQL : host, database name, username, password.
4. Dans le dossier du site, copie `config.example.php` en `config.php`.
5. Remplis `config.php` avec les informations MySQL AwardSpace.
6. Upload tous les fichiers du site dans le dossier web de ton domaine.
7. Ouvre l'URL de ton site dans le navigateur.

Le code cree automatiquement les tables `utilisateurs`, `admin` et `campagnes` au premier chargement.

Si tu veux importer la base manuellement, utilise `database.sql` dans phpMyAdmin.
