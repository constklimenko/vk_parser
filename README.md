# VK DATA

## Описание проекта

Приложение подключается к API ads.vk.com, собирает данные о рекламных объявленияx и хранит их в базе данных, предоставляя к ним доступ по своему API

## Начало работы

После скачивания проекта с репозитория, войдите в папку проекта через консоль и установите зависимости
```shell
composer install
```

### Запуск контейнера

Проект использует Laravel Sail.
По сути, Sail – это файл docker-compose.yml, который хранится в корне вашего проекта и набор скриптов sail, при помощи которых можно управлять docker-контейнерами, определёнными в docker-compose.yml.

По умолчанию команды Sail вызываются с помощью скрипта vendor/bin/sail:
```shell
./vendor/bin/sail up
```

Однако вместо того, чтобы многократно вводить vendor/bin/sail, вы можете создать псевдоним (alias) Shell:

```shell
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

Чтобы убедиться, что это всегда доступно, добавьте это в файл конфигурации оболочки в вашем домашнем каталоге, таком как ~/.zshrc или ~/.bashrc, а затем перезапустите вашу оболочку.

После настройки псевдонима Shell вы можете выполнять команды Sail, просто набрав sail. В остальных примерах из этой документации предполагается, что вы настроили этот псевдоним:
```shell
sail up
```
### Выполнение команд
При использовании Laravel Sail ваше приложение выполняется в контейнере Docker и изолировано от вашего локального компьютера. При помощи Sail можно запускать различные команды для вашего приложения, такие как произвольные команды PHP, команды Artisan, команды Composer и Node/NPM команды.
# Локальное выполнение команд Artisan ...
```shell
php artisan queue:work
```

# Выполнение команд Artisan в Laravel Sail ...
```shell
sail artisan queue:work
```

Более подробно о Sail можно узнать здесь https://laravel.su/docs/10.x/sail

### Миграции

После запуска контейнера нужно создать таблицы в базах данных. 
Для этого в консоли нужно выполнить

```shell
sail artisan migrate
sail artisan clickhouse-migrate
sail artisan db:seed
```

### Команда для создания нового пользователя

```shell
sail artisan app:create-user  {name} {admin?}
```
вместо {name} подставить имя пользователя

Необязательный параметр admin в самом конце указывает, что созданный пользователь является админом, его токен может использоваться для создания новых пользователей по API

Результат имеет вид "User $name created with token $token"

### Команда для запуска парсинга данных

```shell
sail artisan app:check-parser
```

## API

Более полное описание хранится в файле `openapi.self-contained.yaml`

