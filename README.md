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


### Enum


```php
Filter::enum()
```

### Paginate
Apply pagination to the query.
It is important that this filter is applied last.

It expects the request values `$page` and `$per_page`to be present.

```php
Filter::paginate()
```



### Order


```php
Filter::order()
```

### Search


```php
Filter::search()
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

