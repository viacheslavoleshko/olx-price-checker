# OLX price checker

## Description 

The OLX Price Checker is a Laravel-based application designed to monitor and update the prices of advertisements on the OLX platform. It periodically checks the prices of listed advertisements and notifies users of any changes.

## Features
*  Price Monitoring: Regularly checks the prices of advertisements on OLX.
*  Notifications: Sends email notifications to users when the price of an advertisement changes.
*  API and Web Scraping: Uses both the OLX API and web scraping to fetch advertisement data.
*  Queue Workers: Processes tasks asynchronously using Laravel's queue system.
*  Email Verification: Verifies user email addresses upon registration.

##  First Install

1) `git clone <path to remote repositiry> olx-price-checker`
2) `cd olx-price-checker`
3) `cp .env.example .env`
4) Change credentials in `.env` file if you need
5) `docker-compose build`
6) `docker-compose up  -d`
7) `docker ps`
8)  Find \<container-id\> `olx-price-checker-php-fpm-1`
9)  `docker exec -it  <container-id> bash`
10) `composer install`
11) `php artisan migrate  --seed`
12) `php artisan key:generate`
13) `exit`
14) `sudo chmod -R 777 storage`
15) `sudo chmod -R 777 bootstrap/cache/`

## Updating

1) `cd <path-to-folder-olx-price-checker>`
2) `git pull`
3) `docker ps`
4) Find \<container-id\> `olx-price-checker-php-fpm-1`
5) `docker exec -it  <container-id> bash`
6) `composer install` if you need
7) `php artisan migrate`
8) `queue:restart` to restart current queue workers after code updating


## Testing

To run PHPUnit tests: `php artisan test`

## Running the Scheduler

1) `php artisan schedule:work` to start scheduler

Queues will be working automatically

You can change `QUEUE_CONNECTION` in `.env` if you need

##  Mailing

To start mailing you need change credentials in `.env` file

 ```
 MAIL_MAILER
 MAIL_HOST
 MAIL_PORT
 MAIL_USERNAME
 MAIL_PASSWORD
 ```

You can use [mailtrap servise](https://mailtrap.io/) etc. for mail testing

## OLX Partner API integration

To work with API and get advert data from API you need pass your api token on `.env` file 

```
OLX_API_KEY="your_olx_access_token"
```

## Project support

> Request docs & OpenAPI: `/request-docs`

> Also you can interesting in: [MySQL database scheme](https://drawsql.app/teams/test-4184/diagrams/olx-price-checker)


- Register: `api/v1/auth/register`
- Login: `api/v1/auth/login`
- Logout: `api/v1/auth/logout`
- Get additional verification email: `api/v1/email/verification-notification`
- Subscribe to advert price update: `api/v1/advert/subscribe`
- Get subscribed adverts prices: `api/v1/advert/prices`
- Get specific advert prices: `api/v1/advert/{advert}/prices`

## Steps to use

1) Register using your `email` and `password`
2) Get email verification url in your mailbox
3) Put your `access_token` in header `-H "Authorization: Bearer access_token"`
4) Subscribe to advert price update using olx.ua url
5) If price will be updated, you get notified by mail
6) You can get price updating history using appropriate endpoints