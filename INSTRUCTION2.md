# Serveur Central — Nouvelles Fonctionnalités

## Contexte

Ce document décrit les fonctionnalités à ajouter au serveur central existant du projet "Réseau Social de Promo". Le serveur existe déjà avec les routes de base (register, posts, feed, directory). Il faut ajouter les fonctionnalités ci-dessous **sans casser l'existant**.

Les étudiants interagissent avec le serveur uniquement via des appels API (Postman ou Http client Laravel). Le serveur ne peut pas contacter les apps des étudiants. Ce sont eux qui poussent tout.

L'authentification existante fonctionne par token Bearer généré au register, stocké dans la table `players`.

---

## 1. Likes

Les étudiants peuvent liker les posts des autres (et les leurs) via le serveur central.

### Nouvelle table `likes`

| Colonne    | Type      | Description                              |
|------------|-----------|------------------------------------------|
| id         | int       | PK auto-increment                        |
| post_id    | int       | FK vers posts                            |
| player_id  | int       | FK vers players (celui qui like)          |
| created_at | timestamp |                                          |

**Contrainte unique** : un joueur ne peut liker un même post qu'une seule fois (unique sur `post_id` + `player_id`).

### Nouvelles routes

#### `POST /api/posts/{id}/like`

Like un post. Si déjà liké, unlike (toggle).

**Headers :** `Authorization: Bearer <token>`

**Réponse like (200) :**
```json
{
    "status": "liked",
    "likes_count": 5
}
```

**Réponse unlike (200) :**
```json
{
    "status": "unliked",
    "likes_count": 4
}
```

**Réponse post inexistant (404) :**
```json
{
    "message": "Post introuvable."
}
```

### Modification du feed

Le `GET /api/feed` doit maintenant inclure le nombre de likes sur chaque post :

```json
{
    "id": 12,
    "author": "jean",
    "content": "Mon premier post",
    "likes_count": 5,
    "created_at": "2026-02-13T14:30:00Z"
}
```

### Modification de la page projetée

Afficher le nombre de likes à côté de chaque post (icône cœur + nombre).

---

## 2. Profils

Chaque étudiant peut pousser un profil vers le serveur central.

### Modifications de la table `players`

Ajouter les colonnes suivantes à la table `players` existante :

| Colonne    | Type         | Description                          |
|------------|--------------|--------------------------------------|
| bio        | text nullable | Courte biographie                   |
| avatar_url | string nullable | URL d'un avatar (optionnel)       |

### Nouvelles routes

#### `PUT /api/profile`

Met à jour le profil du joueur authentifié.

**Headers :** `Authorization: Bearer <token>`

**Body :**
```json
{
    "bio": "Étudiant en L2 info, passionné de Laravel",
    "avatar_url": "https://i.pravatar.cc/150?u=jean"
}
```

**Validation :**
- `bio` : optionnel, string, max 280 caractères
- `avatar_url` : optionnel, url valide

**Réponse (200) :**
```json
{
    "pseudo": "jean",
    "port": 8042,
    "bio": "Étudiant en L2 info, passionné de Laravel",
    "avatar_url": "https://i.pravatar.cc/150?u=jean",
    "posts_count": 7,
    "likes_received": 12,
    "created_at": "2026-02-13T10:00:00Z"
}
```

#### `GET /api/profiles`

Liste de tous les profils.

**Pas d'authentification requise.**

**Réponse (200) :**
```json
[
    {
        "pseudo": "jean",
        "bio": "Passionné de Laravel",
        "avatar_url": "https://i.pravatar.cc/150?u=jean",
        "posts_count": 7,
        "likes_received": 12
    },
    {
        "pseudo": "marie",
        "bio": null,
        "avatar_url": null,
        "posts_count": 3,
        "likes_received": 5
    }
]
```

#### `GET /api/profiles/{pseudo}`

Profil détaillé d'un joueur spécifique.

**Pas d'authentification requise.**

**Réponse (200) :**
```json
{
    "pseudo": "jean",
    "bio": "Passionné de Laravel",
    "avatar_url": "https://i.pravatar.cc/150?u=jean",
    "posts_count": 7,
    "likes_received": 12,
    "created_at": "2026-02-13T10:00:00Z"
}
```

**Réponse joueur inexistant (404) :**
```json
{
    "message": "Joueur introuvable."
}
```

### Modification de la page projetée

Ajouter un annuaire des profils accessible via un onglet ou une section dédiée sur la page projetée. Afficher l'avatar (ou un placeholder), le pseudo, la bio, et les stats (posts, likes reçus).

---

## 3. Posts avec catégories/tags

Les posts peuvent avoir un tag optionnel.

### Modification de la table `posts`

Ajouter une colonne :

| Colonne | Type            | Description                                    |
|---------|-----------------|------------------------------------------------|
| tag     | string nullable | Tag du post parmi une liste définie             |

**Tags autorisés** : `humeur`, `question`, `annonce`, `blague`, `code`, `random`

### Modification de la route `POST /api/posts`

Ajouter le champ `tag` optionnel au body :

```json
{
    "content": "Quelqu'un a compris les middlewares ?",
    "tag": "question"
}
```

**Validation :**
- `tag` : optionnel, doit être parmi `humeur`, `question`, `annonce`, `blague`, `code`, `random`

### Modification du feed

Le `GET /api/feed` inclut le tag et accepte un nouveau filtre :

```
GET /api/feed?tag=question
GET /api/feed?author=jean&tag=blague
```

Chaque post dans la réponse inclut le tag :

```json
{
    "id": 12,
    "author": "jean",
    "content": "Quelqu'un a compris les middlewares ?",
    "tag": "question",
    "likes_count": 3,
    "created_at": "2026-02-13T14:30:00Z"
}
```

### Modification de la page projetée

Afficher le tag sous forme de badge coloré à côté de chaque post. Couleurs suggérées :
- `humeur` → bleu
- `question` → orange
- `annonce` → rouge
- `blague` → vert
- `code` → violet
- `random` → gris

Ajouter un filtre par tag sur la page projetée (boutons cliquables en haut).

---

## 4. Commentaires

Les étudiants peuvent commenter les posts des autres via le serveur central.

### Nouvelle table `comments`

| Colonne    | Type      | Description                              |
|------------|-----------|------------------------------------------|
| id         | int       | PK auto-increment                        |
| post_id    | int       | FK vers posts                            |
| player_id  | int       | FK vers players (celui qui commente)      |
| content    | text      | Contenu du commentaire                   |
| created_at | timestamp |                                          |
| updated_at | timestamp |                                          |

### Nouvelles routes

#### `POST /api/posts/{id}/comments`

Ajouter un commentaire à un post.

**Headers :** `Authorization: Bearer <token>`

**Body :**
```json
{
    "content": "Trop bien ton post !"
}
```

**Validation :**
- `content` : requis, string, max 280 caractères

**Réponse (201) :**
```json
{
    "id": 5,
    "post_id": 12,
    "author": "marie",
    "content": "Trop bien ton post !",
    "created_at": "2026-02-13T15:00:00Z"
}
```

#### `GET /api/posts/{id}/comments`

Liste des commentaires d'un post, du plus ancien au plus récent.

**Pas d'authentification requise.**

**Réponse (200) :**
```json
[
    {
        "id": 5,
        "author": "marie",
        "content": "Trop bien ton post !",
        "created_at": "2026-02-13T15:00:00Z"
    },
    {
        "id": 8,
        "author": "pierre",
        "content": "Je suis d'accord !",
        "created_at": "2026-02-13T15:05:00Z"
    }
]
```

#### `DELETE /api/comments/{id}`

Supprimer un de ses propres commentaires.

**Headers :** `Authorization: Bearer <token>`

**Réponse (204) :** pas de body.

**Réponse pas le propriétaire (403) :**
```json
{
    "message": "Tu ne peux supprimer que tes propres commentaires."
}
```

### Modification du feed

Ajouter le nombre de commentaires sur chaque post dans le feed :

```json
{
    "id": 12,
    "author": "jean",
    "content": "Mon premier post",
    "tag": "random",
    "likes_count": 5,
    "comments_count": 2,
    "created_at": "2026-02-13T14:30:00Z"
}
```

### Modification de la page projetée

Afficher le nombre de commentaires à côté de chaque post (icône bulle + nombre). Optionnel : possibilité de déplier les commentaires sous un post.

---

## 5. Système de follow

Les étudiants peuvent suivre d'autres étudiants et obtenir un feed personnalisé.

### Nouvelle table `follows`

| Colonne     | Type      | Description                                 |
|-------------|-----------|---------------------------------------------|
| id          | int       | PK auto-increment                           |
| follower_id | int       | FK vers players (celui qui suit)             |
| followed_id | int       | FK vers players (celui qui est suivi)        |
| created_at  | timestamp |                                             |

**Contrainte unique** : un joueur ne peut suivre un même joueur qu'une seule fois (unique sur `follower_id` + `followed_id`).
**Contrainte** : un joueur ne peut pas se suivre lui-même.

### Nouvelles routes

#### `POST /api/follow/{pseudo}`

Suivre un joueur. Si déjà suivi, unfollow (toggle).

**Headers :** `Authorization: Bearer <token>`

**Réponse follow (200) :**
```json
{
    "status": "following",
    "pseudo": "marie"
}
```

**Réponse unfollow (200) :**
```json
{
    "status": "unfollowed",
    "pseudo": "marie"
}
```

**Réponse se suivre soi-même (422) :**
```json
{
    "message": "Tu ne peux pas te suivre toi-même."
}
```

#### `GET /api/followers`

Liste des gens qui me suivent.

**Headers :** `Authorization: Bearer <token>`

**Réponse (200) :**
```json
[
    {"pseudo": "marie"},
    {"pseudo": "pierre"}
]
```

#### `GET /api/following`

Liste des gens que je suis.

**Headers :** `Authorization: Bearer <token>`

**Réponse (200) :**
```json
[
    {"pseudo": "jean"},
    {"pseudo": "alice"}
]
```

#### `GET /api/feed/personal`

Feed personnalisé : uniquement les posts des gens que je suis, du plus récent au plus ancien.

**Headers :** `Authorization: Bearer <token>`

**Réponse (200) :** même format que `GET /api/feed` mais filtré.

### Modification des profils

Ajouter `followers_count` et `following_count` aux réponses des routes profils :

```json
{
    "pseudo": "jean",
    "bio": "Passionné de Laravel",
    "posts_count": 7,
    "likes_received": 12,
    "followers_count": 5,
    "following_count": 3
}
```

---

## 6. Messagerie privée

Les étudiants peuvent s'envoyer des messages privés via le serveur central.

### Nouvelle table `messages`

| Colonne     | Type         | Description                              |
|-------------|--------------|------------------------------------------|
| id          | int          | PK auto-increment                        |
| sender_id   | int          | FK vers players (expéditeur)             |
| receiver_id | int          | FK vers players (destinataire)           |
| content     | text         | Contenu du message                       |
| read_at     | timestamp nullable | Date de lecture (null = non lu)     |
| created_at  | timestamp    |                                          |

### Nouvelles routes

#### `POST /api/messages`

Envoyer un message privé.

**Headers :** `Authorization: Bearer <token>`

**Body :**
```json
{
    "to": "marie",
    "content": "Salut, t'as réussi l'étape 3 ?"
}
```

**Validation :**
- `to` : requis, pseudo existant, différent de soi-même
- `content` : requis, string, max 500 caractères

**Réponse (201) :**
```json
{
    "id": 15,
    "from": "jean",
    "to": "marie",
    "content": "Salut, t'as réussi l'étape 3 ?",
    "created_at": "2026-02-13T15:30:00Z"
}
```

**Réponse destinataire inexistant (404) :**
```json
{
    "message": "Joueur introuvable."
}
```

#### `GET /api/messages`

Inbox : tous les messages reçus, du plus récent au plus ancien.

**Headers :** `Authorization: Bearer <token>`

**Réponse (200) :**
```json
[
    {
        "id": 15,
        "from": "marie",
        "content": "Oui c'était facile !",
        "read": false,
        "created_at": "2026-02-13T15:35:00Z"
    },
    {
        "id": 12,
        "from": "pierre",
        "content": "Tu viens manger ce midi ?",
        "read": true,
        "created_at": "2026-02-13T14:00:00Z"
    }
]
```

#### `GET /api/messages/{pseudo}`

Conversation complète avec un joueur spécifique (messages envoyés et reçus), du plus ancien au plus récent. Marque automatiquement les messages reçus comme lus.

**Headers :** `Authorization: Bearer <token>`

**Réponse (200) :**
```json
[
    {
        "id": 10,
        "from": "jean",
        "to": "marie",
        "content": "Salut, t'as réussi l'étape 3 ?",
        "created_at": "2026-02-13T15:30:00Z"
    },
    {
        "id": 15,
        "from": "marie",
        "to": "jean",
        "content": "Oui c'était facile !",
        "created_at": "2026-02-13T15:35:00Z"
    }
]
```

#### `GET /api/messages/unread/count`

Nombre de messages non lus.

**Headers :** `Authorization: Bearer <token>`

**Réponse (200) :**
```json
{
    "unread_count": 3
}
```

---

## Résumé des nouvelles tables

| Table      | Description                     |
|------------|---------------------------------|
| `likes`    | Likes sur les posts             |
| `comments` | Commentaires sur les posts      |
| `follows`  | Relations de suivi entre joueurs|
| `messages` | Messages privés                 |

## Résumé des modifications de tables existantes

| Table     | Colonnes ajoutées          |
|-----------|---------------------------|
| `players` | `bio`, `avatar_url`       |
| `posts`   | `tag`                     |

## Résumé de toutes les nouvelles routes

| Méthode  | Route                        | Auth   | Description                          |
|----------|------------------------------|--------|--------------------------------------|
| `POST`   | `/api/posts/{id}/like`       | Oui    | Like/unlike un post                  |
| `PUT`    | `/api/profile`               | Oui    | Mettre à jour son profil             |
| `GET`    | `/api/profiles`              | Non    | Liste de tous les profils            |
| `GET`    | `/api/profiles/{pseudo}`     | Non    | Profil d'un joueur                   |
| `POST`   | `/api/posts/{id}/comments`   | Oui    | Commenter un post                    |
| `GET`    | `/api/posts/{id}/comments`   | Non    | Commentaires d'un post               |
| `DELETE` | `/api/comments/{id}`         | Oui    | Supprimer son commentaire            |
| `POST`   | `/api/follow/{pseudo}`       | Oui    | Follow/unfollow un joueur            |
| `GET`    | `/api/followers`             | Oui    | Mes followers                        |
| `GET`    | `/api/following`             | Oui    | Ceux que je suis                     |
| `GET`    | `/api/feed/personal`         | Oui    | Feed des gens que je suis            |
| `POST`   | `/api/messages`              | Oui    | Envoyer un message privé             |
| `GET`    | `/api/messages`              | Oui    | Ma boîte de réception                |
| `GET`    | `/api/messages/{pseudo}`     | Oui    | Conversation avec un joueur          |
| `GET`    | `/api/messages/unread/count` | Oui    | Nombre de messages non lus           |

## Modification de la page projetée

La page projetée `GET /feed` doit être mise à jour pour afficher :
- Le nombre de likes (icône cœur) sur chaque post
- Le nombre de commentaires (icône bulle) sur chaque post
- Le tag sous forme de badge coloré sur chaque post
- Un onglet ou section "Annuaire" avec les profils des joueurs
- Des filtres par tag en haut de page (boutons cliquables)
