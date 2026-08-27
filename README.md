# krasnobaeva — photo & video

Сайт фотографа Тетяни Краснобаєвої. Зібрано на **Next.js 16** (App Router, TypeScript, Tailwind CSS 4, shadcn/ui).

---

## 🚀 Запуск на твоєму сервері (Linux Mint + Cloudflare Tunnel)

У тебе вже працює схема: піддомен `n.franya.dev` → Cloudflare Tunnel → папка на твоєму локальному сервері. Тому все зводиться до трьох кроків.

### Крок 1. Клонувати репозиторій у папку піддомену

```bash
# Перейди у папку, де ти зберігаєш сайти (заміни /var/www на свій шлях)
cd /var/www

# Клонуй репозиторій
git clone https://github.com/franya1111/n.franya.dev.git n.franya.dev

# Зайди у папку
cd n.franya.dev
```

### Крок 2. (Опціонально) Змінити порт, якщо 3000 зайнятий

```bash
# Подивись, чи вільний порт 3000
sudo ss -tlnp | grep :3000 || echo "Порт 3000 вільний ✅"

# Якщо зайнятий — створи файл .env і поміняй порт:
cp .env.example .env
nano .env
#   PORT=3001   # або інший вільний
```

### Крок 3. Запустити Docker

```bash
docker compose up -d --build
```

Це воно. Готово. Через ~30 секунд контейнер підніметься, і Cloudflare Tunnel побачить сайт за адресою:

```
https://n.franya.dev
```

> У конфігурації Cloudflare Tunnel має бути правило, яке перенаправляє `n.franya.dev` на `http://localhost:3000` (або той порт, який ти обрав). Якщо змінював порт — онови його і в Cloudflare.

---

## 🔧 Корисні команди

```bash
# Подивитись логи (в реальному часі)
docker compose logs -f

# Подивитись статус контейнера
docker compose ps

# Перезапустити контейнер
docker compose restart

# Зупинити і видалити контейнер (дані в БД не зникнуть — вони в volume)
docker compose down

# Перебудувати образ (після git pull або зміни коду)
git pull
docker compose up -d --build

# Повністю стерти все, включно з базою (УВАГА: видалить усі дані)
docker compose down -v
```

---

## 🔄 Як оновлювати сайт, коли я заливаю нові зміни

```bash
cd /var/www/n.franya.dev
git pull
docker compose up -d --build
```

Через ~30 секунд нова версія буде жива на `n.franya.dev`.

---

## 🛠 Запуск без Docker (для локальної розробки)

```bash
# Потрібен Node.js 20+ і Bun
bun install
cp .env.example .env
bun run dev          # http://localhost:3000
```

---

## 📦 Що всередині

```
├── src/
│   ├── app/                     # Next.js App Router
│   │   ├── layout.tsx           # Шрифти Forum + Nunito Sans
│   │   ├── page.tsx             # Головна сторінка
│   │   ├── globals.css          # Дизайн-система (золото + тёмный)
│   │   └── api/
│   ├── components/
│   │   ├── ui/                  # shadcn/ui компоненти
│   │   └── site/                # Секції сайту
│   │       ├── header.tsx       # Шапка + dropdown + burger
│   │       ├── hero.tsx
│   │       ├── about.tsx
│   │       ├── services.tsx     # 6 квадратних карток категорій
│   │       ├── reviews.tsx      # Автопрокрутка відгуків
│   │       ├── booking-form.tsx # Анкета бронювання → Telegram
│   │       ├── faq.tsx
│   │       ├── contacts.tsx
│   │       ├── footer.tsx
│   │       ├── category-detail-view.tsx  # Сторінка категорії з цінами
│   │       └── loader.tsx
│   ├── lib/
│   │   ├── site-data.ts         # Усі тексти, ціни, відгуки, FAQ
│   │   ├── db.ts                # Prisma-клієнт
│   │   └── utils.ts
│   └── hooks/
├── public/images/               # Усі зображення
├── prisma/schema.prisma
├── Dockerfile                   # Multi-stage build (Next.js standalone)
├── docker-compose.yml           # Конфіг для docker compose up
├── .env.example                 # Приклад змінних (PORT=3000)
├── next.config.ts               # output: "standalone"
└── package.json
```

## ✏️ Як змінити контент

Усі тексти, ціни, пакети, відгуки та FAQ — в одному файлі:

```
src/lib/site-data.ts
```

Відредагуй → збережи → `git add . && git commit -m "update prices"` → `git push` → на сервері `git pull && docker compose up -d --build`.

## 🖼 Як замінити фотографії

Зображення лежать у `public/images/`. Поклади файл з тим самим ім'ям або онови шлях у `site-data.ts`.

## 🔒 Безпека

- Після першого push у репозиторій **обов'язково відклич GitHub Personal Access Token** (Settings → Developer settings → Personal access tokens → Revoke).
- Файл `.env` у репозиторій **не потрапляє** (в `.gitignore`). Створюй `.env` локально з `.env.example`.

## 📞 Контакти

Тетяна Краснобаєва — фотограф  
Instagram: [@krasnobaeva.ph](https://www.instagram.com/krasnobaeva.ph/)  
Telegram: [@krasnobaevaph](https://t.me/krasnobaevaph)
