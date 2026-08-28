# 🖥️ SYSTEM GUIDE — інфраструктура моїх серверів

Цей файл — довідник. Якщо штучний інтелект (або ти сам) починає новий проект під мій сервер,
потрібно відкрити цей файл і робити так, як тут написано. Тоді новий сайт запуститься
одразу, без налаштувань, без конфліктів портів.

---

## 📋 Що у мене є

- **Сервер:** Linux Mint (HP ZBook 15 G4)
- **Docker:** встановлений, працює через systemd
- **Cloudflare Tunnel:** підключений, DNS `*.franya.dev` дивиться на сервер
- **Reverse Proxy:** Nginx Proxy Manager (NPM) — контейнер `my-test-app-1`, порти `80, 81, 443`
- **DNS:** піддомени створюються просто створенням папки під `~/my-lamp-project/src/<піддомен>/`

---

## 🌐 Як працює схема

```
                ┌──────────────────────────────────────────────┐
                │  Твій сервер (Linux Mint)                   │
                │                                              │
Internet ───────┼──► :80 NPM (my-test-app-1)                   │
                │     (jc21/nginx-proxy-manager:latest)         │
                │        │                                     │
                │        │ роутить по імені домену             │
                │        │ через Docker-мережу my-test_default │
                │        │                                     │
                │        ├─► krasnobaeva-web:80                │
                │        ├─► school-php:9000 (через nginx)     │
                │        ├─► my-lamp-project-web               │
                │        └─► casino...                          │
                │                                              │
                └──────────────────────────────────────────────┘
```

**Ключове:**
1. **Жоден контейнер не пробросжує порт 80, 81, 443 назовні** — всі ці порти зайняті NPM.
2. Контейнер підключається до Docker-мережі `my-test_default` (її створив NPM).
3. NPM сам знаходить контейнер за ім'ям і роутить на порт, який він слухає всередині мережі.
4. SSL-сертифікати випускає NPM через Let's Encrypt (без Cloudflare Origin cert).

---

## 📂 Де лежать сайти

```
~/my-lamp-project/src/
├── n.franya.dev/         ← цей проект (krasnobaeva)
├── sanya.franya.dev/     ← Санин сайт (PHP + Postgres)
├── casino.franya.dev/    ← casino (PHP + MySQL)
└── ... (інші)
```

Кожна папка = окремий проект зі своїм `docker-compose.yml`.

---

## ⚙️ Шаблон `docker-compose.yml` для нового сайту

Цей шаблон підходить для **будь-якого** нового сайту (PHP, Next.js, Python — не важливо):

```yaml
services:
  web:
    image: nginx:alpine          # або твій образ
    container_name: <name>-web   # ім'я, яке NPM буде шукати
    ports:
      - "80"                     # без зазначення хост-порту!
    volumes:
      - ./:/var/www/html:ro
    restart: unless-stopped
    networks:
      - <name>-internal
      - my-test_default          # зовнішня мережа NPM

  # ... інші сервіси (php, db, тощо)

networks:
  <name>-internal:
    driver: bridge
  my-test_default:
    external: true               # мережа вже існує (її створив NPM)
```

**Головні правила:**
- `container_name:` обов'язковий — щоб NPM знав, куди роутити.
- `ports: - "80"` без `хост:контейнер` — порт не відкривається назовні, але NPM бачить через мережу.
- `my-test_default: external: true` — обов'язково, інакше docker створить нову мережу з тим самим іменем.

---

## 🚀 Алгоритм додавання нового сайту

1. **Створити репозиторій на GitHub** (наприклад, `newsite.franya.dev`).
2. **На сервері:**
   ```bash
   cd ~/my-lamp-project/src
   git clone https://github.com/franya1111/newsite.franya.dev.git
   cd newsite.franya.dev
   docker compose up -d --build
   ```
3. **У адмінці NPM** (`http://твій-сервер:81`):
   - **Hosts → Proxy Hosts → Add Proxy Host**
   - **Domain Names:** `newsite.franya.dev`
   - **Scheme:** `http`
   - **Forward Hostname:** `<name>-web` (ім'я контейнера)
   - **Forward Port:** `80` (або інший, який слухає контейнер)
   - **Block Common Exploits:** ✓
   - **Websockets Support:** ✓ (якщо потрібно)
   - **SSL tab → Request a new SSL Certificate + Force SSL**
   - **Save**

Через 10-30 секунд сайт живий: `https://newsite.franya.dev` ✅

---

## 🔍 Команди діагностики (якщо щось не працює)

```bash
# 1. Всі контейнери
docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Ports}}\t{{.Status}}"

# 2. Всі мережі
docker network ls

# 3. Що слухає порти 80, 81, 443
sudo ss -tlnp | grep -E ":(80|81|443) "

# 4. Логи конкретного проекту
cd ~/my-lamp-project/src/<піддомен>
docker compose logs -f

# 5. Конкретний контейнер у мережі NPM
docker network inspect my-test_default | grep -A 5 Containers

# 6. Чи бачить NPM контейнер
docker exec my-test-app-1 ping <container_name>
```

---

## 📦 Що вже працює (станом на створення цього файлу)

| Контейнер          | Образ                              | Порт(и)             | Сайт                          |
|--------------------|------------------------------------|---------------------|-------------------------------|
| my-test-app-1      | jc21/nginx-proxy-manager:latest    | 80, 81, 443         | NPM (reverse proxy)           |
| school-php         | sanyafranyadev-php (custom)        | 9000 (internal)     | sanya.franya.dev              |
| school-db          | postgres:16-alpine                 | 5432                | sanya.franya.dev БД           |
| my-lamp-project-web-1 | 93893211ad98 (nginx)            | 8080                | LAMP project                  |
| my-lamp-project-db-1 | mysql:8.0                         | 3306                | LAMP project БД               |
| my-lamp-project-phpmyadmin-1 | phpmyadmin:latest          | 8081                | phpMyAdmin                    |
| casinofranyadev-db-1 | mysql:8.0                         | 3307                | casino.franya.dev БД           |
| open-webui         | ghcr.io/open-webui/open-webui:main | 3000                | Open WebUI (AI)               |
| dockge-dockge-1    | louislam/dockge:1                  | 5001                | Dockge                        |

**Зверни увагу:**
- Порт **3000 зайнятий** `open-webui` — тому нові проекти НЕ можуть пробросити 3000 назовні.
- Порт **5432** зайнятий Postgres від sanya (саме тому casino використовує 3307).
- Усі веб-сайти ходять через NPM мережу `my-test_default`, ніяких прямих портів не потрібно.

---

## 🆕 Якщо AI (Claude/GPT/інший) запитує про мою систему

Просто покажи йому цей файл. Тут усе, що треба знати:
1. Linux Mint + Docker + Cloudflare Tunnel
2. NPM як reverse proxy на порт 80
3. Контейнери підключаються до `my-test_default` (external: true)
4. Container name = ім'я, яке вписуєш у NPM
5. Жодних прямих портів 80/443/3000/5432 — вони зайняті

---

## 📝 Як додати новий запис у таблицю працюючих проектів

Після успішного деплою нового сайту, додай рядок у таблицю вище (файл `SYSTEM_GUIDE/README.md`).

Шаблон:
```
| <container-name>  | <image>                            | <port>             | <site.franya.dev>             |
```

Це допоможе наступного разу не наступати на ті самі граблі з портами.
