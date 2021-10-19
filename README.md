[comment]: <> (<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>)

[comment]: <> (<p align="center">)

[comment]: <> (<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>)

[comment]: <> (<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>)

[comment]: <> (<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>)

[comment]: <> (<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>)

[comment]: <> (</p>)

## About:

The project is supposed to handle a mini restaurant reservation, and ordering process.

## Requirements:

- PHP ^7.4.
- all Laravel 8 required PHP extensions.
- PostgreSQL DB.

## Involved Entities:

- Customers
- Tables
- Items
- Promo Codes
- Reservations
- Orders

## Description and flow:
the app handles a full customer registration process( register and verify OTP), after login, user is authenticated to available resources using Laravel Sanctum.

a logged in customer shall make a request to reserve a table [FROM], [TO]. reservation has a one of 3 statuses:
- RESERVATION_ACTIVE -> in which a customer can make an order.
- RESERVATION_SETTLED -> in which all customer's orders regarding this reservation are marked as paid. 
- RESERVATION_WAITING -> in which a customer is put on the restaurant's waiting list. 

to make an order, the customer is provided a list of items based on the reservation period and availability regarding quantity.

with a valid reservation, a customer can make as many orders as he demands, as long as he uses a valid set of items, optionally combined with a valid set of promo codes.

a customer can finally check out any of his made orders, acknowledged back with a summary of it.
the app automatically mark the involved reservation as: RESERVATION_SETTLED when there's no not paid order left.
