
![screenshot](https://github.com/Sagleft/PHP-blank-MVC/raw/master/controller/public_html/img/screenshot.png)

## Features

* separation of layout and code through twig-templates;
* similarity of MVC structure;
* .ENV via dotenv;
* meta tags for previews in social networks;
* something else;

## Requirements
* PHP 7.1;
* Apache 2.6;

# Project logic

1. Подключаются библиотеки через composer;
2. создается экземпляр Handler;
3. Handler создает экземпляры Environment, Logic и User;
4. Environment загружает данные из .env;
5. Handler рендерит страницу через Render;
6. Render обрабатывает запрошенный шаблон и через Twig выдает страницу.

## Installation

An example for a project called project:

```bash
git clone https://github.com/Sagleft/PHP-blank-MVC.git project
cd project
mkdir view/cache
chmod 777 view/cache
cp .env.example .env
cp composer.json.example composer.json
composer update
cd controller/public_html
cp example.htaccess .htaccess
```

Next, make changes to .env

Directory: ``` controller\public_html ```.


## Copyright

Copyright (c) 2019 Sagleft.
