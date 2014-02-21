laravel-analytics v0.1.0
=================

Laravel filter to track Visitors and Page Views

## Tracks

* IP Address
* Platform - Requires browscap.ini to be installed
* Browser - Requires browscap.ini to be installed
* Browser Version - Requires browscap.ini to be installed
* Crawler - Requires browscap.ini to be installed
* Mobile - Requires browscap.ini to be installed
* CSS Version - Requires browscap.ini to be installed
* Lang
* Location - Requires pecl geoip plugin
* Geo - Requires pecl geoip plugin

## Installation

First update your `composer.json` to include the following:
````
"require": {
    "isuttell/laravel-sitemap" : "dev-master"
},
````
````
"repositories": [
    {
        "type": "vcs",
        "url" : "https://github.com/isuttell/laravel-sitemap.git"
    }
],
````
Then run `composer update`.  After composer finishes updating, add the following line to the `$providers` array in your `app/config/app.php` file.
````
'Isuttell\LaravelAnalytics\LaravelAnalyticsServiceProvider',
````

This package has two migrations you need to run before use. To do this run the following command:
````
php artisan migrate --bench="isuttell/laravel-analytics"
````


## Examples

### Attaching A Filter To A Controller Action
````
Route::get('/', array('before' => 'analytics', 'uses' => 'HomeController@index'));
````


### Attaching Multiple Filters Via Array
````
Route::get('/user', array('before' => array('auth', 'analytics'), function()
{
    // Do somethign nifty here
}));
````
