# Supervisor Setup

This project already uses:

- `BROADCAST_CONNECTION=reverb`
- `QUEUE_CONNECTION=database`

To keep Reverb and the queue worker running after boot, use Supervisor.

## Config File

Repo copy:

- `deploy/supervisor/myapp3-workers.conf`

Server location:

- Debian / Ubuntu: `/etc/supervisor/conf.d/myapp3-workers.conf`

## Before Enabling

Create the Supervisor log directory:

```bash
sudo mkdir -p /var/www/myapp3/storage/logs/supervisor
sudo chown -R www-data:www-data /var/www/myapp3/storage/logs/supervisor
```

If your PHP binary or app path is different, update these lines in the config:

- `directory=/var/www/myapp3`
- `command=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=8080`
- `command=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- `user=www-data`

## Install The Config

```bash
sudo cp /var/www/myapp3/deploy/supervisor/myapp3-workers.conf /etc/supervisor/conf.d/myapp3-workers.conf
```

## Reload Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start myapp3-reverb
sudo supervisorctl start myapp3-queue
```

## Check Status

```bash
sudo supervisorctl status
```

Expected programs:

- `myapp3-reverb`
- `myapp3-queue`

## Logs

Reverb:

- `/var/www/myapp3/storage/logs/supervisor/reverb.log`
- `/var/www/myapp3/storage/logs/supervisor/reverb-error.log`

Queue:

- `/var/www/myapp3/storage/logs/supervisor/queue.log`
- `/var/www/myapp3/storage/logs/supervisor/queue-error.log`

## Restart Commands

```bash
sudo supervisorctl restart myapp3-reverb
sudo supervisorctl restart myapp3-queue
```

## Boot Behavior

With `autostart=true` and `autorestart=true`, both services:

- start automatically when the server boots
- restart automatically if they stop or crash
