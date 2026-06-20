# Sistem Manajemen Parkir

Laravel 13 + Livewire 4 + MySQL

## Setup Cron Job (Production)

Tambahkan baris berikut ke crontab server (`crontab -e`):

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Untuk test manual:

```bash
php artisan parking:flag-stale-transactions
```
