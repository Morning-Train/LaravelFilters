# Laravel Filters
This package provides a series of filters that can be used to constrain
a Laravel DB Query based on request values.

## Installation

Via Composer

``` bash
$ composer require morningtrain/laravel-filters
```

## Included Filters
All filters can be used using the base Filter class at:
`MorningTrain\Laravel\Filters\Filter`. 
It should not be confused with `MorningTrain\Laravel\Filters\Filters\Filter` 
that are the base class all filters are extending.

### Basic
Filter by a basic variable static variable.

The following example look for `filter_request_parameter` 
in the request, and apply it to the query for the column of the same name.

```php
Filter::by('filter_request_parameter')
```

It corresponds to: 

```php
$q->where(
    'filter_request_parameter', 
    '=',
    request()->get('filter_request_parameter')
)
```

Like in the following example, it is possible to add a closure as the second parameter.

The closure will get the query as well as the filter value as parameters.
It makes it possible to apply some custom logic.

```php
Filter::by('filter_request_parameter', function($query, $value) {
    /// Custom logic for applying $value to $query
})
```

### Always
Always run a closure and apply something to the query.

```php
Filter::always(function($query) {

})
```

### With
Eager load relations on the query.

```php
Filter::with(['company', 'roles'])
```

### Paginate
Apply pagination to the query.
It is important that this filter is applied last.

It expects the request values `$page` and `$per_page`to be present.

```php
Filter::paginate()
```

### Order
The order filter can be configured to sort the data by specific columns or using scopes.

To specify the columns to sort by, call the `only` method on the filter.
The following example will look for `sort[id]` and `sort[created_at]` in the request.

The values of `sort[<column_name>]` can be either *asc* or *desc*.

```php
Filter::order()->only(['id', 'created_at'])
```

Calling the scope method, it is possible to also configure any scopes to be used.
It will be looking in the request for any sort keys that match the scope name.

```php
Filter::order()
    ->only(['id', 'created_at'])
    ->scopes(['orderByName'])
```

Default values can also easily be added:

```php
Filter::order()
    ->only(['id', 'created_at'])
    ->scopes(['orderByName'])
    ->defaultValue(['orderByName' => 'desc'])
```

## Credits
This package is developed and actively maintained by [Morningtrain](https://morningtrain.dk).

<!-- language: lang-none -->
     _- _ -__ - -- _ _ - --- __ ----- _ --_  
    (         Morningtrain, Denmark         )
     `---__- --__ _ --- _ -- ___ - - _ --_ ´ 
         o                                   
        .  ____                              
      _||__|  |  ______   ______   ______ 
     (        | |      | |      | |      |
     /-()---() ~ ()--() ~ ()--() ~ ()--() 
    --------------------------------------

