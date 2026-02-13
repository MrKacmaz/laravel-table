## Laravel Table

Eloquent tabanli, filtreleme/siralama/arama ve pagination odakli tablo paketi.

### Kurulum

```bash
composer require mrkacmaz/laravel-table
```

Config dosyasini yayinlamak icin:

```bash
php artisan vendor:publish --tag=laravel-table-config
```

### Hizli Kullanim

```php
<?php

namespace App\Tables;

use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use LaravelTable\Core\Columns\DatabaseColumn;
use LaravelTable\Core\Table\Table;

class UsersTable extends Table
{
    protected function query(): Builder
    {
        return User::query();
    }

    protected function defineColumns(): array
    {
        return [
            DatabaseColumn::make('id')->sortable()->filterable(),
            DatabaseColumn::make('name')->searchable()->sortable(),
            DatabaseColumn::make('email')->searchable(),
        ];
    }
}
```

Controller:

```php
public function index()
{
    return response()->json(UsersTable::make()->response());
}
```

### Desteklenen Ozellikler

- Database ve relation kolonlari
- Arama (search)
- Siralama (sort)
- Filtreleme (`in`, `not_in`, `between`, karsilastirma operatorleri)
- Sayfalama ve metadata

### Gelistirme Komutlari

```bash
composer test
composer analyse
composer format
```

### Lisans

MIT
