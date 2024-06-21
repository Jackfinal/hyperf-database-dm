# DM DB driver for Hyperf 3 via DM

## hyperf-database-dm

Hyperf-database-dm is an Dm Database Driver package for [Hyperf](https://www.hyperf.io/). Hyperf-database-dm is an extension of [Hyperf/Database](https://github.com/hyperf/database) that uses [DM](https://eco.dameng.com/document/dm/zh-cn/faq/faq-php.html#PHP-Startup-Unable-to-load-dynamic-library) extension to communicate with Dm. Thanks to @yajra.

## Documentations

- You will find user-friendly and updated documentation here: [Hyperf-database-dm Docs](https://github.com/jackfinal/hyperf-database-dm)
- All about dm and php:[The Underground PHP and Dm Manual](https://eco.dameng.com/document/dm/zh-cn/app-dev/php-php.html)

## Laravel Version Compatibility

 Laravel  | Package
:---------|:----------
 3.1.x    | 3.1.x

## Quick Installation

```bash
composer require jackfinal/hyperf-database-dm
```

## Configuration (OPTIONAL)

> You can set connection data in your `.env` files:

```ini
DB_DRIVER=dm
DB_HOST=192.168.45.132
DB_PORT=5236
DB_USERNAME=jack
DB_PASSWORD=1234
```

Then run your hyperf installation...

## NOTICE

You can use `$table->identity($column, $start = 1, $step = 1)` to relace `$table->bigIncrements('id');` Increment ID (primary key).

```
Schema::create('users', function (Blueprint $table) {
  $table->identity('id', 1, 1);
  $table->primary('id');
  $table->datetimes();
  $table->softDeletes();
  $table->comment('Table Comment');
  $table->mediumText('description');  
  $table->string('name', 100);
  $table->text('content');
});
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/jackfinal