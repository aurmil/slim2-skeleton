# Slim 2 Skeleton

## What's included?

* Slim 2
* Twig
* Monolog
* Symfony Yaml Component

## Installation

Run
```sh
composer require aurmil/slim2-skeleton
```

Or clone this repository and run
```sh
composer install
```

Make sure __var__ folder is writable by Web server and Apache (if using it) has __AllowOverride All__ for __public__ folder.

## Web server choice

This skeleton includes a __.htaccess__ file for Apache.

Feel free to read the [Slim documentation](http://docs.slimframework.com/routing/rewrite/) if your prefer to use another Web server like nginx.

## Configuration

Application configuration is stored in __/app/config.yml__ which is divided into 2 main parts: general settings and environment-specific settings.

Environment settings are grouped within sections. A section = an environment. Section name = value of __ENVIRONMENT__ env variable (default = __development__).

General settings are merged with environment-specific settings. The latter ones overwrite the first ones.

### Access config in PHP code

In __/app/bootstrap.php__, the whole configuration is in the __$config__ variable.

In a controller action, only the __App__ section of configuration is available through __$app->config('app')__.
```php
$tmp = $app->config('app')['my_custom_setting_key'];
```

### Access config in Twig template

Only the __App__ section of configuration is in the __config__ variable.

```twig
{{ config.my_custom_setting_key }}
```

## Application errors by email

By configuring __App.errors.send_email__ and __App.errors.email__, you can enable or disable sending email with Monolog when an error occurs.

## Meta tags

Every __key: value__ pair you add under __App.metas__ will be output in HTML head section as a meta tag.

### Title

Page title is a special case. Obviously, __title__ and __title_separator__ entries won't be output as meta tags like the other ones.

A page title is formed as follows:
* content of the __metaTitle__ block a template child could define
```twig
{% block metaTitle %}my custom page title{% endblock %}
```
* if __App.metas.title__ is not empty:
    * if __App.metas.title_separator__ is not empty: add the separator
    * add the config title
