# krasnobaeva — photo & video 📷

Сайт фотографа Тетяни Краснобаєвої. **PHP + Nginx** (без Node.js, без БД — контент у PHP-файлах).

---

## 🚀 Швидкий запуск (3 кроки)

### Крок 1. Клонувати у свою папку сайтів

```bash
cd ~/my-lamp-project/src
git clone https://github.com/franya1111/n.franya.dev.git
cd n.franya.dev
```

### Крок 2. Запустити Docker

```bash
docker compose up -d --build
```

Через ~10 секунд контейнер `krasnobaeva-web` підніметься.

### Крок 3. Додати Proxy Host у NPM

1. Адмінка NPM: `http://твій-сервер:81`
2. **Hosts → Proxy Hosts → Add Proxy Host**
3. Заповнити:
   - **Domain Names:** `n.franya.dev`
   - **Scheme:** `http`
   - **Forward Hostname:** `krasnobaeva-web`
   - **Forward Port:** `80`
   - **Block Common Exploits:** ✓
   - **Websockets Support:** ✓
4. Вкладка **SSL** → `Request a new SSL Certificate` + `Force SSL`
5. **Save**

Готово! Через 10-30 секунд: `https://n.franya.dev` ✅

---

## 🔄 Як оновлювати сайт

```bash
cd ~/my-lamp-project/src/n.franya.dev
git pull
# Перезапуск не потрібний — PHP читає файли в реальному часі
```

Якщо змінив `Dockerfile.php` або `nginx.conf`:

```bash
docker compose up -d --build
```

---

## 🛠 Корисні команди

```bash
docker compose ps          # статус
docker compose logs -f     # логи (Ctrl+C — вихід)
docker compose restart     # перезапуск
docker compose down        # зупинити й видалити контейнер
```

---

## 🌐 Як влаштована мережа

```
                ┌──────────────────────────────────────────┐
                │  Твій сервер (Linux Mint)               │
                │                                          │
Internet ───────┼──► :80 NPM (my-test-app-1)               │
                │        │                                 │
                │        ├─► krasnobaeva-web:80  ← ЦЕЙ    │
                │        ├─► school-php:9000              │  sanya.franya.dev
                │        ├─► my-lamp-project-web           │  my-lamp-project
                │        └─► casino...                     │  casino.franya.dev
                │                                          │
                └──────────────────────────────────────────┘
```

Всі контейнери підключені до Docker-мережі `my-test_default` (її створив NPM при установці). NPM за запитом `n.franya.dev` перенаправляє трафік на контейнер `krasnobaeva-web` порт `80`.

---

## 📦 Структура

```
n.franya.dev/
├── index.php              ← Головна сторінка (hero, about, services, reviews, booking, faq, contacts)
├── category.php           ← Сторінка категорії з цінами (?id=individual / wedding / love-story / birthday / pregnancy / family)
├── includes/
│   ├── data.php           ← Усі дані (категорії, пакети цін, відгуки, FAQ)
│   ├── header.php         ← Шапка + навігація + mobile menu
│   └── footer.php         ← Футер
├── css/style.css          ← Дизайн-система (золото + тёмный, Forum + Nunito Sans)
├── js/main.js             ← Theme toggle, mobile menu, accordion, marquee, form → Telegram
├── img/                   ← Усі зображення
├── docker/
│   ├── Dockerfile.php     ← PHP 8.2 FPM Alpine
│   └── nginx.conf         ← Конфіг Nginx
└── docker-compose.yml     ← nginx:alpine + php-fpm + my-test_default network
```

---

## ✏️ Як змінити контент

Усі тексти, ціни, пакети, відгуки, FAQ — в одному файлі:

```
includes/data.php
```

Відредагуй → збережи → на сервері `git pull`. Перезапуск Docker не потрібний.

## 🖼 Як замінити фотографії

Зображення лежать у `img/`. Поклади файл з тим самим ім'ям або онови шлях у `includes/data.php`.

---

## 🔒 Безпека

- Після першого push у репозиторій **обов'язково відклич GitHub Personal Access Token** (Settings → Developer settings → Personal access tokens → Revoke).
- Контейнер працює від непривілейованого користувача всередині Docker.
- Порт не відкритий назовні — доступ тільки через NPM.

## 📞 Контакти

Тетяна Краснобаєва — фотограф  
Instagram: [@krasnobaeva.ph](https://www.instagram.com/krasnobaeva.ph/)  
Telegram: [@krasnobaevaph](https://t.me/krasnobaevaph)
