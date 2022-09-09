# pianzhou/laravel-query-filter

## Install

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require pianzhou/laravel-query-filter
```

# Database Query Filter

Config app.php
```
Pianzhou\Laravel\Query\Filter\ServiceProvider::class,
```
Demo：
```
// demo one
DemoModel::filter($request->get('name'), function(int $value) {
        $this->where('name', 'like', $value);
    }, \Pianzhou\Laravel\Query\Filter\Filter::MODE_NULL);

// demo two
DemoModel::filters($request->only('column1', 'column2', 'column3'))->get();

// demo three
DemoModel::filters($request->only('name'), function (\Pianzhou\Laravel\Query\Filter\Filter $filter) {
    $filter->where('name', 'like')
        ->where('nickname', 'like');
})->get();

// demo four
DemoModel::filters($request->only('name'), function (\Pianzhou\Laravel\Query\Filter\Filter $filter) {
    $filter->when('name', function (int $value) {
            $this->where('name', 'like', $value);
        })
    ->when('nickname', function (string $value) {
        $this->where('nickname', 'like', $value);
    }, \Pianzhou\Laravel\Query\Filter\Filter::MODE_NULL);
})->get();
```
