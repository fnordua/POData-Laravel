[![Build Status](https://travis-ci.org/Algo-Web/POData-Laravel.svg?branch=master)](https://travis-ci.org/Algo-Web/POData-Laravel)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Algo-Web/POData-Laravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Algo-Web/POData-Laravel/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/algo-web/podata-laravel/v/stable)](https://packagist.org/packages/algo-web/podata-laravel)
[![Latest Unstable Version](https://poser.pugx.org/algo-web/podata-laravel/v/unstable)](https://packagist.org/packages/algo-web/podata-laravel)
[![Total Downloads](https://poser.pugx.org/algo-web/podata-laravel/downloads)](https://packagist.org/packages/algo-web/podata-laravel)
[![Monthly Downloads](https://poser.pugx.org/algo-web/podata-laravel/d/monthly)](https://packagist.org/packages/algo-web/podata-laravel)
[![Daily Downloads](https://poser.pugx.org/algo-web/podata-laravel/d/daily)](https://packagist.org/packages/algo-web/podata-laravel)

# POData-Laravel
Composer Package to provide Odata functionality to Laravel
to install run
```
composer require algo-web/podata-laravel
```

Edit `config/app.php` and add this to providers section:

```php
AlgoWeb\PODataLaravel\Providers\MetadataProvider::class,
AlgoWeb\PODataLaravel\Providers\QueryProvider::class,
```

you then add the trait to the models you want to expose.

```php
    use \AlgoWeb\PODataLaravel\Models\MetadataTrait;
```
