# krasnobaeva — photo & video

Сайт фотографа Тетяни Краснобаєвої. Зібрано на **Next.js 16** (App Router, TypeScript, Tailwind CSS 4, shadcn/ui).

---

## 🚀 Запуск на твоєму сервері (Linux Mint + Nginx Proxy Manager)

У тебе вже працює схема: Cloudflare → NPM (порт 80) → контейнери по іменах у мережі `my-test_default`. Тому все зводиться до **3 кроків без жодної зміни портів**.

### Крок 1. Клонувати репозиторій у папку піддомену

```bash
cd ~/my-lamp-project/src
git clone https://github.com/franya1111/n.franya.dev.git
cd n.franya.dev
```

### Крок 2. Запустити Docker

```bash
docker compose up -d --build
```

Через ~30 секунд контейнер `krasnobaeva-web` підніметься і буде доступний у мережі NPM. Перевір:

```bash
docker compose ps          # статус
docker compose logs -f     # логи (Ctrl+C щоб вийти)
```

### Крок 3. Додати Proxy Host у Nginx Proxy Manager

1. Відкрий адмінку NPM: `http://твій-сервер:81`
2. **Hosts** → **Proxy Hosts** → **Add Proxy Host**
3. Заповни:
   - **Domain Names:** `n.franya.dev`
   - **Scheme:** `http`
   - **Forward Hostname / IP:** `krasnobaeva-web`
   - **Forward Port:** `3000`
   - **Block Common Exploits:** ✓
   - **Websockets Support:** ✓
4. Перейди на вкладку **SSL**:
   - **SSL Certificate:** `Request a new SSL Certificate`
   - **Force SSL:** ✓
   - **Email:** твій email для Let's Encrypt
5. Натисни **Save**

Готово! Через 10-30 секунд сайт буде живий:
```
https://n.franya.dev
```

> Якщо у тебе вже є wildcard-сертифікат `*.franya.dev` — обери його замість "Request a new SSL Certificate".

---

## 🔧 Корисні команди

```bash
# Логи в реальному часі
docker compose logs -f

# Статус контейнера
docker compose ps

# Перезапуск
docker compose restart

# Зупинити і видалити контейнер (дані в БД НЕ зникнуть — вони в volume)
docker compose down

# Оновити сайт після git pull або зміни коду
git pull
docker compose up -d --build

# Повністю стерти все, включно з базою (УВАГА: видалить усі дані)
docker compose down -v
```

---

## 🔄 Як оновлювати сайт надалі

```bash
cd ~/my-lamp-project/src/n.franya.dev
git pull
docker compose up -d --build
```

Через ~30 секунд нова версія буде жива на `n.franya.dev`. Proxy Host у NPM перепідключати не треба — він прив'язаний до імені контейнера.

---

## 🌐 Як влаштована мережа

```
                ┌──────────────────────────────────┐
                │  Твій сервер (Linux Mint)       │
                │                                  │
Internet ───────┼──► :80 NPM (my-test-app-1)       │
                │        │                         │
                │        ├─► krasnobaeva-web:3000  │  ← цей проект
                │        ├─► school-php:9000       │  ← sanya.franya.dev
                │        ├─► my-lamp-project-web   │  ← my-lamp-project
                │        └─► casino...             │  ← casino.franya.dev
                │                                  │
                └──────────────────────────────────┘
```

Всі контейнери підключені до Docker-мережі `my-test_default` (її створив NPM при установці). NPM за запитом `n.franya.dev` перенаправляє трафік на контейнер `krasnobaeva-web` порт `3000`.

---

## 🛠 Запуск без Docker (для локальної розробки)

```bash
# Потрібен Node.js 20+ і Bun
bun install
cp .env.example .env
bun run dev          # http://localhost:3000
```

---

## 📦 Структура проекту

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
│   │       ├── header.tsx
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
├── docker-compose.yml           # Конфіг для NPM
├── .env.example
├── next.config.ts               # output: "standalone"
└── package.json
```

## ✏️ Як змінити контент

Усі тексти, ціни, пакети, відгуки та FAQ — в одному файлі:

```
src/lib/site-data.ts
```

Відредагуй → збережи → `git add . && git commit -m "..."` → `git push` → на сервері `git pull && docker compose up -d --build`.

## 🖼 Як замінити фотографії

Зображення лежать у `public/images/`. Поклади файл з тим самим ім'ям або онови шлях у `site-data.ts`.

## 🔒 Безпека

- Після першого push у репозиторій **обов'язково відклич GitHub Personal Access Token** (Settings → Developer settings → Personal access tokens → Revoke).
- Файл `.env` у репозиторій **не потрапляє** (в `.gitignore`).
- Контейнер працює від непривілейованого користувача `nextjs`.
- Порт не відкритий назовні — доступ тільки через NPM.

## 📞 Контакти

Тетяна Краснобаєва — фотограф  
Instagram: [@krasnobaeva.ph](https://www.instagram.com/krasnobaeva.ph/)  
Telegram: [@krasnobaevaph](https://t.me/krasnobaevaph)
