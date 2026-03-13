# AtlasRoutes (API d’itinéraires touristiques)

API REST Laravel pour créer/consulter/modifier des itinéraires touristiques au Maroc, avec destinations + activités/plats/endroits, favoris (“à visiter”), recherche/filtrage, statistiques et tests.

## Démarrage (local)

```bash
cd AtlasRoutes
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Tests:

```bash
cd AtlasRoutes
php artisan test
```

## Démarrage (Docker + PostgreSQL)

```bash
docker compose up --build
```

L’API sera accessible sur `http://127.0.0.1:8080`.

## Endpoints principaux

- `POST /api/register`, `POST /api/login`, `POST /api/logout`
- `GET /api/itineraries`, `POST /api/itineraries`, `GET /api/itineraries/{id}`, `PUT /api/itineraries/{id}`, `DELETE /api/itineraries/{id}`
- `POST /api/itineraries/{id}/destinations` (owner), `PUT /api/destinations/{id}`, `DELETE /api/destinations/{id}`
- `POST /api/itineraries/{id}/favorite`, `DELETE /api/itineraries/{id}/favorite`, `GET /api/me/favorites`

### Query Builder (brief)

- `GET /api/itineraries/search?q=...`
- `GET /api/itineraries/filter?category=...&duration=...`
- `GET /api/itineraries/popular?limit=...`
- `GET /api/stats/itineraries-by-category`
- `GET /api/stats/users-by-month?year=...`
