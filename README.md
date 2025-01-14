<p align="center">
    <img src="./art/logo.png" width="200" alt="Lynx Logo">
</p>

[![Tests](https://github.com/frikishaan/lynx/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/frikishaan/lynx/actions/workflows/tests.yml)


## About Lynx

Lynx is a self-hosted link shortner application. It  allows you to shorten, track, and manage links with ease.

It is built with SQLite, making it lightweight, easy to manage, and perfect for projects of any scale. No complex database setup required, it offers a hassle-free way to handle your links while ensuring reliability and performance.

## Features

- **Choice pages** - Create choice pages to allow users to navigate to multiple destinations from a single short URL, offering flexibility and boosting conversions.

- **Customizable slug** -Define meaningful, unique slugs for your short urls like _ac.me/help_ or _ac.me/shop_ etc.

- **Password protected links** - Secure your shortened links with passwords and ensure only authorized users can access the resource.

- **QR Code** - Automatically generate QR codes for every shortened link, simplifying access and sharing.

- **Teams** - Collaborate efficiently by creating teams, allowing shared access and management.

- **Custom domains** - Enhance branding by using multiple custom domains for your short links, providing flexibility and a customized experience for different audiences or campaigns.

- **Analytics** - Gain insights into link performance with detailed analytics, including click counts, geographic data, and device statistics.

## Requirements

- PHP >= 8.2
- Laravel >= 11.x

## Installing locally

Follow below steps to install it locally -

### Clone repository

```bash
git clone https://github.com/frikishaan/lynx.git
```
### Install dependencies

```bash
composer install

npm install

npm run build
```

### Run database migrations

```bash
php artisan migrate
```

### Optionally, seed the test data

```bash
php artisan db:seed
```

## License

[Lynx license](./LICENSE.md)
