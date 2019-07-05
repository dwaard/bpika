# BPIKA

## Installation

```bash
$ cp .env.example .env
# Set right variables
$ composer install
$ php artisan migrate
```

## Testing

Once installed, send an empty GET request to `/api/ping`. It should return:

```json
{
    "message": "Hello wold!"
}
```
