# Deploy — palmtwin.dimasperceka.com

Flow: **push to GitHub → server pulls → rebuild & restart (Docker) → nginx serves the domain.**

- Repo: `https://github.com/dimasperceka-se/palm-twin.git`
- App: Express serves the built client (`dist/public`) **and** the `/api` on one port (`5003`).
- Prod runtime: the existing `Dockerfile` (single image).

---

## Every deploy (the happy command)

From your machine, after `deploy.config` is set up once (below):

```bash
./deploy.sh                  # commit all changes, push, pull+redeploy on server
./deploy.sh "fix peatland"   # with a commit message
```

Windows: `deploy.bat` (runs `deploy.sh` via Git Bash), or run `bash deploy.sh` in Git Bash.

`deploy.sh` → commit + `git push origin main` → SSH to the server → `server-deploy.sh`
(`git pull` → `docker build` → restart container on `127.0.0.1:5003`).

---

## One-time local setup

```bash
cp deploy.config.example deploy.config   # then edit with your server details
```

`deploy.config` is gitignored. Fields: `SERVER_USER`, `SERVER_HOST`, `APP_DIR`, `DOMAIN`, `HOST_PORT`.

---

## One-time server setup

Assumes Ubuntu/Debian with `git`, `docker`, and `nginx` installed (+ SSH access).

```bash
# 1. Clone the repo to APP_DIR (must match deploy.config)
sudo mkdir -p /var/www && sudo chown "$USER" /var/www
git clone https://github.com/dimasperceka-se/palm-twin.git /var/www/palm-twin
cd /var/www/palm-twin

# 2. Production env (NOT committed) — your PostGIS connection string
cp .env.example .env
nano .env            # set DATABASE_URL=...   and keep PORT=5003

# 3. First build & run
HOST_PORT=5003 bash server-deploy.sh

# 4. nginx + HTTPS for the domain
sudo cp nginx/palmtwin.dimasperceka.com.conf /etc/nginx/sites-available/palmtwin.dimasperceka.com
sudo ln -s /etc/nginx/sites-available/palmtwin.dimasperceka.com /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d palmtwin.dimasperceka.com
```

**DNS:** point an `A` record for `palmtwin.dimasperceka.com` → the server's public IP before running certbot.

After that, every future release is just `./deploy.sh` from your machine.

---

## Alternative: run without Docker (PM2)

If the server has no Docker, run Node directly instead of `server-deploy.sh`:

```bash
git pull --ff-only
npm ci
npm run build
pm2 restart palm-twin || pm2 start npm --name palm-twin -- start
pm2 save
```

(Requires `node 20+` and `pm2 -g`; reads `.env` from the project dir.)
