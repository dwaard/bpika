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

## Documentation

[Sensor API](https://www.meteobridge.com/wiki/index.php)

### Meteobridge setup
HTTP: ```every ## seconds/minutes``` should be aligned with the ```REQUEST_TIMEOUT_IN_MINUTES``` 
environment variable

URL: should be something like 
```
http://[app-url]/api/store?station=station_name&th_temp=[th*temp-avg10]&th_hum=[th*hum-avg10]
``` 
this syntax uses the Template mechanism (see: [Templates](https://www.meteobridge.com/wiki/index.php/Templates))

The ```avg10``` selector should aligned with the event interval. For this instance, the interval is 
 set to 10 minutes (i.e. when interval is 5 mins. use ```avg5``` instead).

Success: ```measurement.created```

### Supported parameters

- station_name: NOT A TEMPLATE PARAMETER. Should be filled in manually
- th_temp
- th_hum
- th_dew
