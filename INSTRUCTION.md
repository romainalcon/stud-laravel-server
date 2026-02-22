# Serveur Central — Réseau Social de Promo

## Contexte

Je suis enseignant et je donne un TP Laravel à des étudiants de Licence 2 en informatique. Chaque étudiant développe sa propre API de mini-blog sur un serveur partagé (réseau privé, ports 8000-8099). Ce projet est le **serveur central** que je mets en ligne à l'extérieur. Les étudiants poussent leurs posts vers moi et je projette un feed en temps réel sur l'écran de la salle.

## Stack

- **Laravel** (dernière version stable)
- **SQLite** comme base de données (simplicité de déploiement)
- Pas de système d'auth complexe (pas de Breeze/Sanctum), juste des tokens simples générés au register

## Architecture

Je suis un serveur passif. Je ne peux PAS contacter les apps des étudiants (elles sont sur un réseau privé). Ce sont eux qui poussent tout vers moi.

## Base de données — Tables à créer

### `players`

| Colonne    | Type      | Description                          |
|------------|-----------|--------------------------------------|
| id         | int       | PK auto-increment                    |
| pseudo     | string    | Unique, le nom de l'étudiant         |
| port       | int       | Port de son app (8000-8099)          |
| token      | string    | Token unique généré au register      |
| created_at | timestamp |                                      |
| updated_at | timestamp |                                      |

### `posts`

| Colonne    | Type      | Description                          |
|------------|-----------|--------------------------------------|
| id         | int       | PK auto-increment                    |
| player_id  | int       | FK vers players                      |
| content    | text      | Contenu du post                      |
| created_at | timestamp |                                      |
| updated_at | timestamp |                                      |

## Routes API

### `POST /api/register`

L'étudiant s'enregistre. Génère un token unique et le renvoie.

**Body attendu :**
```json
{
    "pseudo": "jean",
    "port": 8042
}
```

**Validation :**
- `pseudo` : requis, string, unique, max 30 caractères
- `port` : requis, integer, entre 8000 et 8099

**Réponse succès (201) :**
```json
{
    "token": "a1b2c3d4e5f6...",
    "message": "Bienvenue jean !"
}
```

**Réponse erreur pseudo déjà pris (422) :**
```json
{
    "message": "Ce pseudo est déjà pris."
}
```

---

### `POST /api/posts`

Un étudiant publie un post. Authentifié par token Bearer.

**Headers :**
```
Authorization: Bearer <token>
```

**Body attendu :**
```json
{
    "content": "Mon premier post sur le réseau !"
}
```

**Validation :**
- `content` : requis, string, max 500 caractères

**Réponse succès (201) :**
```json
{
    "id": 12,
    "author": "jean",
    "content": "Mon premier post sur le réseau !",
    "created_at": "2026-02-13T14:30:00Z"
}
```

**Réponse non authentifié (401) :**
```json
{
    "message": "Token invalide."
}
```

---

### `GET /api/feed`

Tous les posts de tout le monde, du plus récent au plus ancien. Possibilité de filtrer par auteur.

**Pas d'authentification requise.**

**Query parameters optionnels :**
- `author` (string) : filtre les posts d'un auteur spécifique

**Exemples :**
```
GET /api/feed               → tous les posts
GET /api/feed?author=jean   → uniquement les posts de jean
```

**Réponse (200) :**
```json
[
    {
        "id": 12,
        "author": "jean",
        "content": "Mon premier post sur le réseau !",
        "created_at": "2026-02-13T14:30:00Z"
    },
    {
        "id": 11,
        "author": "marie",
        "content": "Hello tout le monde",
        "created_at": "2026-02-13T14:28:00Z"
    }
]
```

---

### `GET /api/directory`

Liste de tous les joueurs enregistrés.

**Pas d'authentification requise.**

**Réponse (200) :**
```json
[
    {
        "pseudo": "jean",
        "port": 8042
    },
    {
        "pseudo": "marie",
        "port": 8015
    }
]
```

---

## Page HTML projetée

### `GET /feed`

Une page web (Blade) qui affiche le feed en temps réel. Cette page sera projetée sur l'écran de la salle de cours.

**Comportement :**
- Affiche tous les posts du plus récent au plus ancien
- Se rafraîchit automatiquement toutes les 5 secondes (simple polling JS avec `fetch` sur `/api/feed`)
- Design sympa et lisible de loin (gros texte, bon contraste)
- Chaque post affiche : le pseudo de l'auteur, le contenu, et le temps relatif ("il y a 2 min")
- Style type fil Twitter / feed social
- Pas besoin de framework JS, du vanilla JS suffit

## Authentification

Système simple basé sur des tokens :
- Au `POST /api/register`, le serveur génère un token avec `Str::random(64)`
- Ce token est stocké en base dans la table `players`
- Pour les routes protégées (`POST /api/posts`), le token est envoyé en header `Authorization: Bearer <token>`
- Un middleware custom ou une vérification dans le contrôleur récupère le player associé au token
- Pas de Sanctum, pas de Passport, juste une vérification manuelle simple

## Notes importantes

- Toutes les réponses sont en JSON sauf la route `GET /feed` qui renvoie du HTML
- Les erreurs de validation doivent renvoyer des messages clairs en JSON (422)
- Le token invalide renvoie 401
- Pas de pagination pour l'instant, on garde simple
- CORS : autoriser toutes les origines (les étudiants tapent depuis leurs serveurs)
- Le serveur doit être prêt à recevoir beaucoup de requêtes en peu de temps (30 étudiants qui postent)
