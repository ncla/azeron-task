# Installation

Standard Laravel installation.

1. Pull the project with sub-modules included (for Docker to be included): `git clone https://github.com/ncla/azeron-task.git --recurse-submodules`
2. Copy .env.example in project root to .env: `cp .env.example .env`
3. If you plan to use browser to make requests to API, compile front-end: `yarn && yarn run prod`

## Docker setup

1. Navigate to `docker` directory `cd docker`
2. Copy Docker .env file: `cp .env.example .env`
3. `docker-compose up -d nginx mysql`
4. Access Docker: `docker-compose exec --user=laradock workspace bash`
5. `composer install`, `php artisan key:generate`, `php artisan migrate`

# Usage

You can register to create a user. To set user to an admin you'll have to connect to database, the default MYSQL credentials in .env.example should work. Edit `is_admin` column to `1` in `users` table.

To run tests, simply run `phpunit` within Docker container project directory.

To fire requests to the API, be logged in from `/home` and open browser developer tools console and create requests manually with these lines as examples:
```js
await window.axios.put('/calendar/add', {year: 2020, month: 8, day: 17});
await window.axios.patch('/calendar/edit', {calendar_id: 1, year: 2020, month: 8, day: 18});
await window.axios.delete('/calendar/delete', {data: {calendar_id: 1}});
await window.axios.post('/calendar/list', {
    order_by: {year: 'asc', month: 'desc'},
}).then((r) => console.log(r.data));
```
