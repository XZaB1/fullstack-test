## Запуск проекта

### 1. Распаковать проект

```bash
cd /[path]/[to]/fullstack-test-main
```

### 2. Создать `.env` для Docker Compose 

В корне проекта присутствует файл `.env` со значениями - его и взял как основу в дальнейшем:

```ini
MYSQL_ROOT_PASSWORD=cod31gn1t3
MYSQL_DATABASE=codeigniter_db
UID=1000
```

> `UID` — идентификатор пользователя, под которым будет работать Apache
> внутри контейнера. Для Linux/macOS подойдёт `1000`, для Windows - тоже `1000`.

### 3. Собрать и запустить контейнеры

```bash
docker-compose up -d --build
```

Первый запуск может занять 5-10 минут: скачиваются образы, собирается
образ `web` (устанавливаются PHP-расширения, Composer).

### 4. Установить PHP-зависимости

```bash
docker-compose exec web bash
composer install
```

### 5. Настроить приложение (`.env` CodeIgniter)

Внутри контейнера (в `/var/www/html`):

```bash
cp env .env
```

Отредактировать `.env` — заполнить секцию `database.default.*` (Если не сохранились прописанные мною настройки): 

```ini
database.default.hostname = db
database.default.database = codeigniter_db
database.default.username = root
database.default.password = cod31gn1t3
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

> Хост `db` — имя сервиса MySQL в `docker-compose.yml`. Порт `3306` —
> внутренний порт контейнера (наружу проброшен как `3307`).

Выйти из контейнера:

```bash
exit
```

### 6. Накатить миграции

```bash
docker-compose exec web php spark migrate
```

## Доступы


| Приложение | http://localhost | — |
| Adminer (веб-клиент БД) | http://localhost:8080 | сервер `db`, имя `root` , пароль `cod31gn1t3`, БД `codeigniter_db` |


## Функционал

- **Список комментариев** с постраничным выводом (3 на страницу).
- **Добавление** комментария без перезагрузки страницы (AJAX + jQuery).
- **Удаление** комментария без перезагрузки страницы.
- **Сортировка** по:
  - `id`
  - `date`
- **Направление сортировки**: по возрастанию / по убыванию.
- **Пагинация** с выбором конкретной страницы.
- **Валидация email**:
  - на клиенте (jQuery + HTML5, с отображением ошибки под полем);
  - на сервере (правило `valid_email` в контроллере).
- **Адаптивная вёрстка** на Bootstrap 4 (проверено на мобильных ширинах iPhone 12 и SE, Pixel 10).

## Полезные команды

```bash
# статус контейнеров
docker-compose ps

# логи
docker-compose logs web
docker-compose logs db

# зайти в контейнер web
docker-compose exec web bash

# запустить миграции
docker-compose exec web php spark migrate

# откатить миграции
docker-compose exec web php spark migrate:rollback

# список spark-команд
docker-compose exec web php spark list

# остановить контейнеры (данные БД сохранятся в ./db_data)
docker-compose down

# остановить и удалить volume с БД (полный сброс)
docker-compose down -v
```

## Возможные проблемы

**Порт 3306 занят.** Если у вас локально запущен MySQL/MariaDB,
`docker-compose up` упадёт с ошибкой про порт. Решение: в `docker-compose.yml`
у сервиса `db` заменить `3307:3306` на любой свободный внешний порт
(например, `3308:3306`) или остановить локальный MySQL.

**Composer не может скачать пакеты.** Если у вас VPN / прокси с блокировкой
`repo.packagist.org`, `composer install` может частично фейлиться. Проект
содержит `composer.lock`, поэтому при наличии кэша установка проходит.

**Права на `writable/`.** Если CodeIgniter ругается на запись в `writable/`:

```bash
docker-compose exec web chmod -R 777 writable
```