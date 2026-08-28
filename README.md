# krasnobaeva — photo & video 📷

Сайт фотографа Тетяни Краснобаєвої. **PHP + Nginx** з повною адмінкою (контент, відгуки, FAQ, заявки, Telegram/email-сповіщення).

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

При першому старті:
- У `data/` копіюються seed-файли (categories, reviews, faqs, settings) з `data/seed/`
- Створюється Docker volume `krasnobaeva-data` — там зберігаються заявки, зміни через адмінку тощо

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

## 🔐 Адмінка

### Перший вхід

Відкрий у браузері:
```
https://n.franya.dev/admin/
```

При першому вході побачиш форму первинного налаштування:
- Придумай **ім'я користувача** (за замовчуванням `admin`)
- Придумай **пароль** (мін. 6 символів)
- Підтвердь пароль

Після цього ти одразу увійдеш у дашборд.

### Що можна робити в адмінці

| Сторінка | Що робить |
|----------|-----------|
| **Дашборд** | Статистика: кількість заявок, відгуків, категорій, FAQ + останні заявки |
| **Бронювання** | Всі заявки клієнтів. Можна позначати прочитаними, видаляти, прямо звідти дзвонити або писати в Telegram |
| **Категорії та ціни** | Редагування 6 категорій: назва, опис, фото, галерея, цінові пакети (по 3 на категорію), подарунки, додаткові послуги |
| **Відгуки** | Додавання/видалення відгуків клієнтів. Одразу з'являються на сайті |
| **FAQ** | Повний CRUD: додавати, редагувати, видаляти питання та відповіді |
| **Налаштування** | Контакти, соцсети, текст «Про мене», **Telegram Bot** для сповіщень, **Email** для сповіщень, зміна пароля |

---

## 📲 Налаштування Telegram-сповіщень

Коли клієнт заповнює форму бронювання на сайті — заявка автоматично:
1. Зберігається в `data/bookings.json`
2. Відправляється в **Telegram** (якщо налаштований bot)
3. Відправляється на **Email** (якщо вказаний у налаштуваннях)

### Як отримати Bot Token та Chat ID

1. Відкрий [@BotFather](https://t.me/BotFather) → /newbot → отримай `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`
2. Додай свого бота в чат або групу, напиши йому будь-що
3. Відкрий у браузері: `https://api.telegram.org/bot<ВАШ_ТОКЕН>/getUpdates`
4. У відповіді знайди `"chat":{"id":123456789}` — це твій Chat ID

Занеси ці дані в **адмінка → Налаштування → Telegram Bot**. Натисни «📲 Надіслати тестове повідомлення» — перевір, що все працює.

---

## 📧 Email-сповіщення

PHP `mail()` функція. На більшості хостингів працює одразу. Якщо листи не приходять — перевір:
1. В адмінці → Налаштування → Email (заповнений?)
2. На сервері встановлений MTA (postfix/exim)? Якщо ні — повідомлення просто не відправляться, але заявка все одно збережеться в адмінці

> **Tip:** Якщо потрібні гарантовані email-сповіщення — підключи SMTP через сторонній сервіс (Mailgun, SendGrid). Це можна додати пізніше.

---

## 🔄 Як оновлювати сайт

```bash
cd ~/my-lamp-project/src/n.franya.dev
git pull
# Перезапуск Docker НЕ потрібний — PHP читає файли в реальному часі
```

Якщо змінив `Dockerfile.php` або `nginx.conf`:

```bash
git pull
docker compose up -d --build
```

---

## 🛠 Корисні команди

```bash
docker compose ps          # статус
docker compose logs -f      # логи
docker compose restart     # перезапуск

# Бекап всіх даних (заявки, відгуки, налаштування):
docker cp krasnobaeva-php:/var/www/html/data ./backup-data-$(date +%Y%m%d)

# Повний сброс (УВАГА: видалить усе):
docker compose down -v
docker compose up -d --build
```

---

## 📦 Структура

```
n.franya.dev/
├── index.php              ← Головна
├── category.php           ← Сторінка категорії з цінами (?id=...)
├── admin/                 ← 🔐 Адмінка
│   ├── index.php          ← Дашборд
│   ├── login.php          ← Вхід / первинне налаштування
│   ├── bookings.php       ← Заявки клієнтів
│   ├── categories.php     ← Редагування категорій та цін
│   ├── reviews.php        ← Відгуки CRUD
│   ├── faqs.php           ← FAQ CRUD
│   ├── settings.php       ← Налаштування (контакти, Telegram, email, пароль)
│   ├── test-telegram.php  ← Тест Telegram
│   ├── includes/          ← auth.php, header.php, footer.php
│   └── assets/            ← admin.css, admin.js
├── api/
│   └── booking.php         ← POST endpoint: прийом заявок → Telegram + email
├── includes/
│   ├── data.php           ← Завантаження даних з JSON
│   ├── header.php
│   └── footer.php
├── data/                  ← ⚠️ НЕ в git (крім seed/)
│   ├── categories.json    ← Категорії та ціни (редагується через адмінку)
│   ├── reviews.json        ← Відгуки
│   ├── faqs.json           ← FAQ
│   ├── bookings.json       ← Заявки клієнтів
│   ├── settings.json       ← Налаштування сайту + Telegram
│   ├── admin.json          ← Пароль адміністратора (хеш)
│   └── seed/               ← Початкові дані (в git)
├── css/style.css          ← Дизайн
├── js/main.js             ← Інтерактив
├── img/                   ← Зображення
├── docker/
│   ├── Dockerfile.php     ← PHP 8.2 FPM + curl + стартовий скрипт
│   └── nginx.conf         ← Nginx (заборонено доступ до data/)
└── docker-compose.yml
```

---

## ✏️ Як змінити контент

**Через адмінку** — `https://n.franya.dev/admin/` — це основний спосіб. Всі зміни зберігаються в `data/*.json` і одразу появляються на сайті.

**Вручну** — відредагуй `data/seed/*.json` + зроби `git pull` (якщо хочеш скинути дані до дефолтних на новому сервері).

## 🖼 Як замінити фотографії

Зображення лежать у `img/`. Поклади файл з тим самим ім'ям або онови шлях в адмінці → Категорії.

---

## 🔒 Безпека

- **Пароль адміністратора** зберігається в `data/admin.json` у вигляді хешу `password_hash()` (bcrypt) — навіть якщо хтось отримає файл, пароль не відновити
- **Доступ до `data/`** заборонений через nginx (повертає 403)
- **CSRF-захист** на API бронювання (same-origin policy)
- **Валідація** всіх вхідних даних
- **Сесії** — стандартні PHP-сесії, зберігаються в `/tmp` всередині контейнера
- Після першого push у репозиторій **обов'язково відклич GitHub Personal Access Token**

## 📞 Контакти

Тетяна Краснобаєва — фотограф  
Instagram: [@krasnobaeva.ph](https://www.instagram.com/krasnobaeva.ph/)  
Telegram: [@krasnobaevaph](https://t.me/krasnobaevaph)
