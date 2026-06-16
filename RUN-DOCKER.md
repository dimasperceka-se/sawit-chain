# Run PalmOilTrace with Docker (easy mode)

This runs the **whole app + database** with one command — no PHP, MySQL, or
config needed on the machine. Just Docker.

## 1. Install Docker
Install **Docker Desktop**: https://www.docker.com/products/docker-desktop/
(Windows / Mac / Linux). Open it once so it's running.

## 2. Start it
Open a terminal **in this project folder** and run:

```bash
docker compose up -d --build
```

The **first** run builds the images and loads ~1 GB of demo data — this takes
several minutes. Watch progress with:

```bash
docker compose logs -f db      # press Ctrl+C to stop watching
```

When the database log shows *"ready for connections"* and stops importing, it's done.

## 3. Open the app
Go to **http://localhost:8080**

Log in with the demo account:

| Username | Password |
|---|---|
| `partner.demo` | `Password1234!` |

(Other demo users: `mill.demo`, `sme.demo`, `fa.demo` — same password.)

## Everyday commands
```bash
docker compose up -d        # start (after the first build)
docker compose down         # stop (keeps the data)
docker compose down -v      # stop AND erase the database (next start re-imports)
docker compose logs -f app  # view application logs
```

## Hot reload (editing code)
Hot reload is **on by default**: `docker-compose.override.yml` bind-mounts this
project folder into the container, so any change to **PHP, JS, or CSS** is live on
the next request / browser refresh — no rebuild, no restart.

```bash
docker compose up -d        # hot reload ON (override is merged automatically)
```

- Edit a file → just refresh the browser (Cmd/Ctrl+Shift+R to skip browser cache).
- OPcache is off in the image, so PHP files are re-read every request.
- `composer.json` / new PHP extensions still need a rebuild: `docker compose up -d --build`.
- Want the plain baked image with **no** hot reload? `docker compose -f docker-compose.yml up -d`.

## What's inside
- **palmoiltrace-app** — PHP 7.4 + Apache, the CodeIgniter app + `/api`, on port **8080**.
- **palmoiltrace-db** — MySQL 8 pre-loaded with the current demo data, on port **33106** (host).

## Notes
- Login uses an **offline bypass** (no AWS needed) because `APP_LOCAL_AUTH=1` is set
  in `docker-compose.yml`. This is for the local demo only.
- The data is a snapshot; changes you make persist in the `palmoiltrace-db-data`
  volume until you run `docker compose down -v`.
- Port already in use? Edit the `ports:` in `docker-compose.yml` (e.g. `8081:80`).
