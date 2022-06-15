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
DemoModel::filter($request->get('id'), function(int $value) {
        $this->where('id', 'like', $value);
    }, \Pianzhou\Laravel\Query\Filter\Filter::MODE_EMPTY);
    
    
DemoModel::filters($request->only('id'), function (\Pianzhou\Laravel\Query\Filter\Filter $filter) {
    $filter->when('id', function (int $value) {
            $this->where('id', 'like', $value);
        }, \Pianzhou\Laravel\Query\Filter\Filter::MODE_EMPTY)
    ->when('name', function (string $value) {
        $this->where('name', 'like', $value);
    }, \Pianzhou\Laravel\Query\Filter\Filter::MODE_EMPTY);
})->get();
```