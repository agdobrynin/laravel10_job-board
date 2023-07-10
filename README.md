## Job board

Демо проект - "Объявления о вакансиях".

Краткое описание функциональности проекта.

---

Приложение демонстрирует работу с... (дописать)

Первичные ключи в моделях используют UUID строки.

Тестирование приложения.

---

- 🐘 Php 8.2 + Laravel 10
- 🌊 Tailwind CSS + Blade templates
- 🐘 Postgres 15
- 🐳 Docker (Docker compose) + Laravel Sail
- ⛑ Тестирование PHPUnit

### Сборка образов докера

Настроить переменные окружения (если требуется изменить их):

```shell
cp .env.example .env
```

⚠ Если на машине разработчика установлен **php** и **composer** то можно выполнить команду:

```shell
composer install --ignore-platform-reqs
```

⚠ Если не установлен **php** и **composer** на машине разработчика то установить зависимости проекта можно так:

```shell
docker run --rm -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

на этом подготовка к работе с Laravel Sail закончена.

### Запуск проекта

Поднять docker контейнеры с помощью Laravel Sail
```shell
./vendor/bin/sail up -d
```

доступные команды по остановке или пересборке контейнеров можно узнать на странице
[Laravel Sail](https://laravel.com/docs/9.x/sail)
или выполните команду `./vendor/bin/sail` для получения краткой справки о доступных командах.


1.  Сгенерировать application key

    ```shell
    ./vendor/bin/sail artisan key:generate
    ```

2.  Выполнить миграции и заполнить таблицы тестовыми данными

    ```shell
    ./vendor/bin/sail artisan migrate --seed
    ```

### Запуск тестов

```shell
./vendor/bin/sail test
```

### Доступные сайты в dev окружении

|                Host                | Назначение                                                   |
|:----------------------------------:|:-------------------------------------------------------------|
|          http://localhost          | сайт приложения                                              |