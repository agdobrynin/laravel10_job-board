## Job board

Демо проект - "Объявления о вакансиях".

Реализована возможность регистрации пользователей приложения -
соискатель работы и работодатель размещающий вакансии.
Каждая регистрация подтверждается через email сообщение.
Реализована фильтрация списка вакансий по критериям, пагинация
результатов поиска. Для соискателя работы реализована подача
заявки на вакансию с прикреплением (загрузкой файла) сопроводительного
письма. Работодатель имеет интерфейс для создания объявлений
о васкансии, редактировании и удалении своих объявлений, 
а так же для просмотра списка заявок на вакансию и просмотра прикрепленных
сопроводительных писем от претендентов.

Реализован функционал оповещения работодателя новой заявке
на вакансию через оповещение через email.

---

- 🐘 Php 8.2 + Laravel 10
- 🌊 Tailwind CSS + Blade templates + AlpineJs
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

3. Настроить storage link для загруженных файлов
    ```shell
    ./vendor/bin/sail artisan storage:link
    ```
4. Собрать фронт
    ```shell
    ./vendor/bin/sail npm install
    ```
    ```shell
    ./vendor/bin/sail npm run build
    ```
5. Запустить воркер (worker) обрабатывающий задачи из очереди сообщений
    ```shell
   ./vendor/bin/sail artisan queue:work
   ```
### Запуск тестов

```shell
./vendor/bin/sail test
```

### Доступные сайты в dev окружении

|                Host                | Назначение                                                   |
|:----------------------------------:|:-------------------------------------------------------------|
|          http://localhost          | сайт приложения                                              |
|       http://localhost:8025        | Mailpit - вэб интерфейс для отладки отправки email сообщения |

