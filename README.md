# krasnobaeva — photo & video

Сайт фотографа Тетяни Краснобаєвої. Зібрано на **Next.js 16** (App Router, TypeScript, Tailwind CSS 4, shadcn/ui).

## ⚡ Швидкий старт через Docker (рекомендується)

Потрібен лише встановлений **Docker** (з плагіном Compose v2) — нічого більше не ставиться локально.

```bash
# 1. Клонувати репозиторій
git clone https://github.com/franya1111/n.franya.dev.git
cd n.franya.dev

# 2. Підняти контейнер (збирається образ і запускається на порту 3000)
docker compose up --build -d

# 3. Відкрити в браузері
#    http://localhost:3000

# Зупинити й видалити контейнер:
docker compose down

# Подивитись логи:
docker compose logs -f
```

Щоб змінити порт (наприклад, на 8080) — відредагуй `docker-compose.yml`:

```yaml
ports:
  - "8080:3000"
```

База даних (SQLite) зберігається у Docker volume `krasnobaeva-data` — тобто між перезапусками контейнера всі дані лишаються на місці.

## 🛠 Запуск без Docker (для розробки)

Потрібні **Node.js 20+** та **Bun**.

```bash
# 1. Встановити залежності
bun install

# 2. Скопіювати .env.example у .env і за потреби відредагувати
cp .env.example .env

# 3. Запустити dev-сервер
bun run dev
# Сайт буде на http://localhost:3000

# Production-збірка:
bun run build
bun run start
```

## 🚀 Деплой на Vercel / Render / Railway / Netlify

Будь-який сервіс, що підтримує Next.js, підійде:

### Vercel (найпростіше)
1. Зайти на https://vercel.com
2. «Add New Project» → обрати цей GitHub-репозиторій
3. Frame Preset: **Next.js** (визначиться автоматично)
4. Натиснути **Deploy** — готово через ~1 хвилину

### Render / Railway
1. Створити новий Web Service
2. Підключити GitHub-репозиторій
3. Build command: `bun install && bun run build`
4. Start command: `bun run start`
5. Порт: `3000`

## 📦 Структура проекту

```
├── src/
│   ├── app/                     # Next.js App Router
│   │   ├── layout.tsx           # Шрифти + метадані
│   │   ├── page.tsx             # Головна сторінка (з view-станом)
│   │   ├── globals.css          # Дизайн-система
│   │   └── api/
│   ├── components/
│   │   ├── ui/                  # shadcn/ui компоненти
│   │   └── site/                # Сторінкові секції сайту
│   │       ├── header.tsx       # Шапка + навігація
│   │       ├── hero.tsx
│   │       ├── about.tsx
│   │       ├── services.tsx     # 6 квадратних карток категорій
│   │       ├── reviews.tsx      # Автопрокрутка відгуків
│   │       ├── booking-form.tsx # Анкета бронювання
│   │       ├── faq.tsx
│   │       ├── contacts.tsx
│   │       ├── footer.tsx
│   │       ├── category-detail-view.tsx  # Сторінка категорії з цінами
│   │       └── loader.tsx
│   ├── lib/
│   │   ├── site-data.ts         # Усі дані сайту (категорії, ціни, відгуки, FAQ)
│   │   ├── db.ts                # Prisma-клієнт
│   │   └── utils.ts
│   └── hooks/
├── public/
│   └── images/                  # Усі зображення
├── prisma/
│   └── schema.prisma            # Схема БД
├── Dockerfile                   # Багатоетапна збірка
├── docker-compose.yml           # Локальний запуск: docker compose up
├── .dockerignore
├── .env.example                 # Приклад змінних середовища
├── next.config.ts               # output: "standalone" — оптимізовано для Docker
├── tailwind.config.ts
└── package.json
```

## ✏️ Як змінити контент

Усі тексти, ціни, пакети, відгуки та FAQ знаходяться в одному файлі:

```
src/lib/site-data.ts
```

— відредагуй і зберігши, перезавантаж сторінку.

## 🖼 Як замінити фотографії

Усі зображення лежать у `public/images/`. Щоб замінити — просто поклади файл з тим самим ім'ям або онови шлях у `site-data.ts`.

## 🔒 Безпека

- Після першого запуску з репозиторієм **обов'язково відклич GitHub Personal Access Token**, яким я заливав код (Settings → Developer settings → Personal access tokens → Revoke).
- Файл `.env` у репозиторій **не потрапляє** (він у `.gitignore`). За потреби створи `.env` локально з `.env.example`.

## 📞 Контакти

Тетяна Краснобаєва — фотограф  
Instagram: [@krasnobaeva.ph](https://www.instagram.com/krasnobaeva.ph/)  
Telegram: [@krasnobaevaph](https://t.me/krasnobaevaph)
