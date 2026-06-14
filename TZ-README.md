# BarPro Premium — реализация ТЗ v1.0 «Event Premium»

Папка темы готова к загрузке в WordPress.

## Что добавлено / переделано

| Файл | Назначение |
|------|------------|
| `assets/css/tz.css` | Палитра ТЗ (#0F172A / #111827 / #1F2937, акцент #D4A373 / #C89B5B / #B8860B), glassmorphism, fade-in, hero parallax, lightbox |
| `assets/js/tz.js`  | IntersectionObserver fade-in, parallax, lightbox, фильтры, 5-шаговый калькулятор, конструктор коктейльной карты |
| `page-calculator.php`   | `/calculator` — Hero (видео) + 5 шагов из ТЗ → диапазон стоимости |
| `page-team.php`         | `/team` — Hero, основатель, карточки сотрудников (CPT `team_member`), преимущества |
| `page-cocktails.php`    | `/cocktails` — Hero, конструктор карты, фильтр (Все/Алко/Безалко/Авторские/Классические), карточки коктейлей |
| `archive-case_study.php`| `/cases` — фильтр + сетка |
| `single-case_study.php` | Страница кейса (задача → решение → что реализовано → галерея → итоги → CTA) |
| `page-catering.php`     | `/catering` — Hero, что входит, форматы, пакеты Start/Standard/Premium, masonry-галерея + lightbox |
| `page-bar-catering.php` | `/bar-catering` — Hero, проблемы → решение → что входит → кейсы → калькулятор → финальный CTA |
| `template-parts/tz-hero.php` | Универсальный Hero с parallax / fade-in |
| `template-parts/tz-cta-row.php` | Три кнопки «Получить расчёт / Позвонить / Telegram» |
| `template-parts/tz-sticky-cta.php` | Sticky CTA-бар на мобильных |
| `template-parts/tz-breadcrumbs.php` | Хлебные крошки + Schema.org BreadcrumbList |
| `inc/tz-bootstrap.php`  | Подключение ассетов, маршрутизация шаблонов, автосоздание страниц |
| `inc/tz-seo.php`        | Title/Description/Canonical/OG/Twitter Card/FAQ JSON-LD/preload Hero |
| `inc/tz-acf.php`        | ACF Pro поля — весь контент редактируется из админки |

## Установка

1. Распакуйте архив в `wp-content/themes/`.
2. Активируйте тему **BarPro Premium** (вкладка «Внешний вид → Темы»).
3. При первой активации автоматически создаются страницы: `/calculator`, `/team`, `/cocktails`, `/cases`, `/catering`, `/bar-catering`.
4. Установите и активируйте плагин **ACF Pro** (опционально, но рекомендуется) — без него страницы работают с дефолтным контентом.
5. Зайдите в «Настройка → Контакты BarPro» и укажите телефон/Telegram/WhatsApp — они подставятся во все CTA.

## Соответствие ТЗ

- **Стиль Event Premium**: палитра, типографика Cormorant + Jost, glassmorphism в карточках.
- **Анимации**: fade-in блоков (IntersectionObserver), parallax для hero, hover-эффекты, плавный скролл (`scroll-behavior: smooth`).
- **Запрещено**: автоплей видео со звуком (`muted` обязательно), мигающие элементы, агрессивные движения — учтён `prefers-reduced-motion`.
- **Адаптивность**: брейкпоинты 600 / 980 px. Sticky CTA на мобильных.
- **CTA**: на каждой странице — три кнопки «Получить расчёт» / «Позвонить» / «Написать в Telegram».
- **SEO**: title-tag, description, canonical, OpenGraph, Twitter Card, JSON-LD `LocalBusiness` (главная) + `FAQPage` (по полям ACF) + `BreadcrumbList` (хлебные крошки).
- **Производительность**: lazy-loading, preload hero, defer/async скриптов, минимум зависимостей, статичный CSS (без runtime CSS-in-JS).
- **WordPress**: ACF Pro, Custom Post Types (`cocktail`, `team_member`, `case_study`), редактор Gutenberg-compatible (контент страниц редактируется обычным редактором + через ACF).
- **PHP 8.3 / WP 6.8+**: код пишет в строгом стиле, без устаревших конструкций, всё проходит через `esc_*` / `wp_kses_*`.

## ACF-поля (быстрая справка)

### Калькулятор
- `calc_hero_title`, `calc_hero_subtitle`, `calc_hero_video` (URL .mp4), `calc_hero_image`.

### Команда
- `team_hero_image`, `founder_name`, `founder_role`, `founder_photo`, `founder_story` (WYSIWYG), `founder_mission`, `founder_philosophy`.
- CPT **team_member**: `role`, `experience`, `specialization`.

### Коктейли
- `cocktails_hero_image`.
- CPT **cocktail**: `ingredients`, `cocktail_categories` (checkbox если без таксономии).

### Кейтеринг
- `catering_hero_image`, `catering_gallery` (Gallery).

### Бар + Кейтеринг
- `bc_hero_image`.

### Кейсы
- CPT **case_study**: `case_date`, `case_guests`, `case_task`, `case_solution`, `case_done` (WYSIWYG), `case_result`, `case_gallery` (Gallery), `is_featured` (true/false).

### Универсальное SEO (page / cocktail / case_study)
- `seo_title`, `seo_description`, `faq_items` (repeater: `question` / `answer`) → автоматически становится FAQPage Schema.

## Критерии готовности (по ТЗ)

- [x] Адаптивность 1920 / 1366 / tablet / mobile.
- [x] SEO: Title, Description, OG, Twitter Card, Schema.org, Breadcrumbs, Canonical, FAQ Schema.
- [x] PHP 8.3 совместимость (нет deprecation).
- [x] WP 6.8+ совместимость.
- [x] Все CTA-кнопки (Расчёт / Звонок / Telegram) присутствуют.
- [x] Контент редактируется из админки (нет хардкода).
- [x] Нет пустых блоков (есть fallback-данные).
- [x] Готово к деплою на российский хостинг (нет внешних SaaS-зависимостей в runtime).
