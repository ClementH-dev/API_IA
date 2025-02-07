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
   git clone https://github.com/ClementH-dev/API_IA.git
   cd API_IA
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   - Renommer le fichier `.env.exemple` en `.env` Modifier les valeurs pour correspondre à votre environnement :
     ```ini
     DB_HOST=localhost
     DB_NAME=nom_de_la_base
     DB_USER=root
     DB_PASS=mot_de_passe
     ```

4. **Installer la base de donnée**
- Modifier les paramètre de connexion Mysql dans le fichier `install_db.php` pour que les valeurs correspondent a votre environnement :
      ```php
      $host = 'localhost'; 
      $user = 'root';
      $password = '';
      $database = 'chat_api'; 

5. **Lancer la commande d'installation**
      ```bash
        php install_db.php
      ```

6. **Lancer le serveur** (Ligne de commande)
   ```bash
   php -S localhost:8000 -t public
   ```

7. **Vérifier que tout fonctionne**
   Ouvrir `http://localhost:8000` dans votre navigateur.

## Authentification
L’API utilise **JWT (JSON Web Token)** pour sécuriser les endpoints protégés.

- Lors de la connexion, l’API renvoie un token JWT.
- Ce token doit être inclus dans chaque requête protégée via l'en-tête HTTP :
  ```http
  Authorization: Bearer VOTRE_TOKEN
  ```

## Endpoints
### Utilisateurs (`/users`)

#### **Créer un utilisateur**
- **Méthode** : `POST /users`
- **Middleware** : Validation, Sanitize
- **Corps de requête** :
  ```json
  {
    "nom": "string",
    "prenom": "string",
    "pseudo": "string",
    "telephone": "string",
    "mail": "string",
    "password": "string"
  }
  ```
- **Réponse** :
  ```json
  {
    "message": "Utilisateur enregistré avec succès"
  }
  ```

#### **Authentification utilisateur**
- **Méthode** : `POST /users/auth`
- **Middleware** : Validation, Sanitize
- **Identifiant possibles** : Email ou username
- **Corps de requête** :
  ```json
  {
    "identifier": "string",
    "password": "string"
  }
  ```
- **Réponse** :
  ```json
  {
    "token": "jwt_token"
  }
  ```

#### **Récupérer tous les utilisateurs**
- **Méthode** : `GET /users`
- **Middleware** : Auth
- **Réponse** :
  ```json
  [
    {
      "id": 1,
      "nom": "string",
      "prenom": "string",
      "telephone": "string",
      "mail": "string",
      "password": "string" (encrypt),
      "pseudo": "string",
      "created_at": "date",
      "updated_at": "date"  
    }
  ]
  ```

#### **Récupérer un utilisateur par ID**
- **Méthode** : `GET /users/{id}`
- **Middleware** : Auth
- **Réponse** :
  ```json
  {
    "id": 1,
      "nom": "string",
      "prenom": "string",
      "telephone": "string",
      "mail": "string",
      "password": "string" (encrypt),
      "pseudo": "string",
      "created_at": "date",
      "updated_at": "date" 
  }
  ```

#### **Mettre à jour un utilisateur**
- **Méthode** : `PUT /users/{id}`
- **Middleware** : Auth, Validation, Sanitize
- **Corps de requête** :
  ```json
  {
    "nom": "string",
    "prenom": "string",
    "pseudo": "string",
    "telephone": "string",
    "mail": "string",
  }
  ```
- **Réponse** :
  ```json
  {
    "message": "Utilisateur mis à jour."
  }
  ```

#### **Modifier le mot de passe**
- **Méthode** : `PUT /users/{id}/password`
- **Middleware** : Auth, Validation, Sanitize
- **Corps de requête** :
  ```json
  {
    "oldPassword": "string",
    "newPassword": "string"
  }
  ```
- **Réponse** :
  ```json
  {
    "message": "Mot de passe mis à jour."
  }
  ```

#### **Supprimer un utilisateur**
- **Méthode** : `DELETE /users/{id}`
- **Middleware** : Auth
- **Réponse** :
```json
  {
    "message": "Utilisateur supprimé."
  }
```

### Univers (`/univers`)

#### **Créer un univers**
- **Méthode** : `POST /univers`
- **Middleware** : Auth, Sanitize, Validate
- **Corps de requête** :
  ```json
  {
    "nom_univers": "string",
    "description_univers": "string" (précision sur l'univers)
  }
  ```
- **Réponse** :
```json
{
  "message": "L'univers a bien été crée.",
  "filepath": "./uploads/nom_univers/pictureUnivers_#2.png"
}
```

#### **Récupère un univers** 
- **Méthode** : `GET /univers/{id_univers}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "id": 1,
    "nom_univers": "string",
    "description_univers": "text",
    "image": "filepath"
    "id_utilisateur": 2,
    "created_at": "date",
    "updated_at": "date"
}
```

#### **Récupère tous les univers**
- **Méthode** : `GET /univers`
- **Middleware** : Auth
- **Corps de requête** :
  ```json
  {
    "nom_univers": "string",
    "description_univers": "string" (précision sur l'univers)
  }
  ```
- **Réponse** :
```json
  {
    "id": 1,
    "nom_univers": "string",
    "description_univers": "text",
    "image": "filepath",
    "id_utilisateur": 2,
    "created_at": "date",
    "updated_at": "date"
  }
```
#### **Suprime un univers**
- **Méthode** : `DELETE /univers/{id_univers}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "message": "Univers suprimé.",
}
```

### Character (`/characters`)

#### **Créer un character**
- **Méthode** : `POST /character/{id_univers}`
- **Middleware** : Auth, Sanitize, Validate
- **Corps de requête** :
  ```json
  {
    "nom_character": "string",
    "description_character": "string" (précision sur le character)
  }
  ```
- **Réponse** :
```json
{
  "message": "Le personnage a bien été crée.",
  "filepath": "./uploads/nom_univers/Nom-Personnage_#2.png"
}
```

#### **Récupère un character** 
- **Méthode** : `GET /characters/{id_character}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "id": 1,
    "nom_personnage": "string",
    "description_personnage": "text",
    "image": "filepath",
    "id_utilisateur": 2,
    "created_at": "date",
    "updated_at": "date"
}
```

#### **Récupère tous les characters**
- **Méthode** : `GET /characters/univers/{id_univers}`
- **Middleware** : Auth
- **Corps de requête** :
  ```json
  {
    "nom_univers": "string",
    "description_univers": "string" (précision sur l'univers)
  }
  ```
- **Réponse** :
```json
  {
    "id": 1,
    "nom_univers": "string",
    "description_univers": "text",
    "image": "filepath",
    "id_utilisateur": 2,
    "created_at": "date",
    "updated_at": "date"
  }
```
#### **Suprime un personnage**
- **Méthode** : `DELETE /characters/{id_character}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "message": "Personnage suprimé.",
}
```

### Chat (`/chats`)

#### **Créer un chat**
- **Méthode** : `POST /chats/{id_character}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "message": "Conversation créée",
}
```

#### **Récupère un chat** 
- **Méthode** : `GET /chat/{id_conversation}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  {
    "id": 2,
    "id_personnage": 3,
    "id_utilisateur": 2,
    "created_at": "date",
    "updated_at": "date",
    "nom_personnage": "string",
    "description_personnage": "text",
    "image": "filepath",
    "id_univers": 2,
    "nom_univers": "string",
    "description_univers": "text"
  }
}
```

#### **Récupère tous les chats**
- **Méthode** : `GET /chats`
- **Middleware** : Auth
- **Réponse** :
```json
  {
     {
      "id": 2,
      "id_personnage": 3,
      "id_utilisateur": 2,
      "created_at": "date",
      "updated_at": "date",,
      "id_univers": 2,
    }
  }
```
#### **Suprime un chat**
- **Méthode** : `DELETE /chats/{id_conversation}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "message": "Conversation supprimée.",
}
```

### Message (`/messages`)

#### **Envoyer un message**
- **Méthode** : `POST /messages`
- **Middleware** : Auth
- **Corps de requête** :
  ```json
  {
    "message": "string",
  }
  ```
- **Réponse** :
```json
  {
    "message": "Message envoyé avec succès.",
    "reponse": "text"
  }
```

#### **Récupère tout les messages d'un chat** 
- **Méthode** : `GET /messages/{id_conversation}`
- **Middleware** : Auth
- **Réponse** :
```json
{
    {
        "id": 1,
        "contenu": "text",
        "envoye_par_ia": 0,
        "id_conversation": 2,
        "created_at": "date",
        "updated_at": "date"
    },
    {
        "id": 2,
        "contenu": "text",
        "envoye_par_ia": 1,
        "id_conversation": 2,
        "created_at": "date",
        "updated_at": "date"
    }
}
```

#### **Mettre a jour le dernier message**
- **Méthode** : `PUT /message/{id_conversation}`
- **Middleware** : Auth
- **Réponse** :
```json
{
  "message": "Dernier message utilisateur mis à jour",
  "reponse": "text"

}
```