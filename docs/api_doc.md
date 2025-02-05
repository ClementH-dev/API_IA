# Documentation de l'API IA

## Introduction
Bienvenue dans la documentation de l'API IA. Cette API permet d'envoyer des messages entre des personnages fictif et un utilisateur. Elle est construite en PHP avec une architecture MVC.

## Installation
### Prérequis
- PHP 8.0 ou plus
- Composer
- XAMPP ou tout autre serveur local
- MySQL pour la base de données

### Étapes d'installation
1. **Cloner le projet**
   ```bash
   git clone https://github.com/votre-repo/IA_API.git
   cd IA_API
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   - Copier le fichier `.env.example` et renommer en `.env`
   - Modifier les valeurs pour correspondre à votre environnement :
     ```ini
     DB_HOST=localhost
     DB_NAME=nom_de_la_base
     DB_USER=root
     DB_PASS=mot_de_passe
     ```

4. **Lancer le serveur** (Ligne de commande)
   ```bash
   php -S localhost:8000 -t public
   ```

5. **Vérifier que tout fonctionne**
   Ouvrir `http://localhost:8000` dans votre navigateur.

## Authentification
L’API utilise **JWT (JSON Web Token)** pour sécuriser les endpoints protégés.

- Lors de la connexion, l’API renvoie un token JWT.
- Ce token doit être inclus dans chaque requête protégée via l'en-tête HTTP :
  ```http
  Authorization: Bearer VOTRE_TOKEN
  ```

## Prochaines Sections
Nous allons maintenant documenter les endpoints (`users`, `univers`, `characters`, etc.). 🚀

