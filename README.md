# Time Capsule Application

This Time Capsule application is a full-stack web application built with Laravel and Vue.js. It allows users to store messages in a digital time capsule, setting them to be opened at a future date. This application demonstrates proficiency in developing secure, scalable, and user-friendly applications using modern web technologies.

## Features

- **User Authentication**: Securely register and authenticate users.
- **Create Message Capsules**: Users can create message capsules with custom messages and set a future date for when they can be opened.
- **View Message Capsules**: Users can view their created message capsules, with capsules only being accessible after their scheduled opening time.
- **Edit and Update Capsules**: Users have the ability to edit and update the details of their message capsules before they are opened.
- **Security**: Enhanced security measures to ensure that message capsules can only be accessed by their creators and only after the scheduled opening time.

## Technology Stack

- **Backend**: Laravel
  - Used for handling API requests, user authentication, and database interactions.
- **Frontend**: Vue.js
  - Implements a dynamic and responsive user interface, managing state with Vuex and routing with Vue Router.
- **Database**: MySQL
  - Stores user and message capsule data.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP >= 7.3
- Composer
- Node.js and npm
- A MySQL database
```
DB_DATABASE=buyerbridge
DB_USERNAME=root
DB_PASSWORD=testing
```

### Installation

**1. Set up the Server Database**

To set up the server's database locally follow these steps:
1. Install docker: https://www.docker.com/products/docker-desktop/
2. Run: `docker-compose up -d`
3. install DBeaver (or other DB tool) to create a `buyerbridge` DB
4. create a MySQL connection
5. create local database named `buyerbridge`

**2. Set up the Backend**

Copy the .env.example file to a new file named .env, and adjust the database settings to match your environment:
```bash
composer install
cp .env.example .env
php artisan key:generate
```

Run the migrations and seed the database ( you can skip this ):
```bash
php artisan migrate
php artisan db:seed
```

Start the Laravel development server:
```bash
php artisan serve
```

**3. Set up the Frontend**

Install the dependencies:
```bash
npm install
```

Compile and hot-reload for development:
```bash
npm run watch
```

**4. Testing**

To run the automated tests for the application:
```bash
php artisan test
```

### Localization / i18n

The project supports localization / i18n, to translate the front-end use `lang/{code}/frontend.php` file.

### Structure

The front-end code is located in `resources/js`. The code is organized in different directories to make things more readable.

| Directory    | Description                           |
|--------------|---------------------------------------|
| views        | The home of views                     |
| + pages      | The home of the pages                 |
| + icons      | The home of the icons                 |
| + layouts    | The home of the global layouts        |
| + components | The home of the reusable components   |
| helpers      | The home of the helper utilites       |
| plugins      | The home of the plugins configuration |
| router       | The home of the router configuration  |
| services     | The home of the HTTP services         |
| stores       | The home of the Pinia stores          |
| stub         | The home of the static constants      |

#### Normal domain

```
APP_URL=http://mywebsite.com

SANCTUM_STATEFUL_DOMAINS=mywebsite.com
SESSION_DOMAIN=mywebsite.com
```

#### Localhost with port

```
APP_URL=http://localhost:8000

SANCTUM_STATEFUL_DOMAINS=localhost:8000
SESSION_DOMAIN=localhost
```
