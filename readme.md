# Voilab Mapping system

## Install

Via Composer

Create a composer.json file in your project root:
``` json
{
    "require": {
        "voilab/mapping": "1.*"
    }
}
```

``` bash
$ composer require voilab/mapping
```

## Sample dataset

``` php
// have a mapping instance
$mapping = new \voilab\mapping\Mapping();

// have a set of datas. This could also be an object with
// methods like getName(), getInterests(), etc.
$data = [
    'id' => 1,
    'name' => 'John',
    'email' => 'john@doe.ch',
    'interests' => [
        [
            'type' => 'intellect',
            'description' => 'Some book',
            'contact' => [
                'name' => 'Some author'
            ]
        ],
        [
            'type' => 'sport',
            'description' => 'Football'
        ]
    ],
    'bestFriend' => [
        'id' => 2,
        'name' => 'Fred'
        'age' => 30
    ],
    'work' => [
        'description' => 'Free worker',
        'section' => [
            'type' => 'Social'
        ]
    ]
];
```

## Examples
This piece of script helps you to obtain a constant output structure no matter how the input data is hydrated (array or object).

### Simple mapping
```php
$mapped = $mapping->map($data, [
    'id',
    'name'
]);

// results in
[
    'id' => 1,
    'name' => 'John'
]
```

### Function call mapping
```php
$mapped = $mapping->map($data, [
    'id',
    'email' => function ($data) {
        return $data['email'] == 'john@doe.ch'
            ? 'some.default@email.ch'
            : $data['email'];
    }
]);

// results in
[
    'id' => 1,
    'email' => 'some.default@email.ch'
]
```


### Change mapping key
```php
$mapped = $mapping->map($data, [
    'id',
    'personName' => 'name'
]);

// results in
[
    'id' => 1,
    'personName' => 'John'
]
```

### One-to-one or many-to-one relation mapping
```php
$mapped = $mapping->map($data, [
    'work' => [
        'section' => [
            'type'
        ]
    ]
]);

// results in
[
    'work' => [
        'section' => [
            'type' => 'Social'
        ]
    ]
]
```

### One-to-many or many-to-many relation mapping
```php
$mapped = $mapping->map($data, [
    'interests' => [[
        'description'
    ]]
]);

// results in
[
    'interests' => [
        ['description' => 'Some book'],
        ['description' => 'Football']
    ]
]
```

### Complex relation mapping
```php
$mapped = $mapping->map($data, [
    'interests' => [[
        'contact' => [
            'name'
        ]
    ]]
]);

// results in
[
    'interests' => [
        ['contact' => [
            'name' => 'Some author'
        ]],
        ['contact' => null]
    ]
]
```

### Change relation mapping key
```php
$mapped = $mapping->map($data, [
    \voilab\mapping\Mapping::rel('work', 'nextWork') => [
        'description'
    ]
]);

// results in
[
    'nextWork' => [
        'description' => 'Free worker'
    ]
]
```
As an alternative, you may use this notation below. Code should not change anytime soon.
```php
$mapped = $mapping->map($data, [
    'work as nextWork' => [
        'description'
    ]
]);
```


### Wildcard mapping
It's experimental with objects.
```php
$mapped = $mapping->map($data, [
    'name',
    'bestFriend' => [
        '*'
    ]
]);

// results in
[
    'name' => 'John',
    'bestFriend' => [
        'id' => 2,
        'name' => 'Fred'
        'age' => 30
    ]
]
```

### Other thoughts
#### Change hydrators
There are two default hydrators. One manage arrays (collection and relation) and the other manage objects (collection and relation).
If your data needs to be handled differently, you will need to create your own hydrators (which must extend \voilab\mapping\Hydrator) and then set them at construction time:
```php
$mapping = new \voilab\mapping\Mapping(
    new \my\object\Hydrator(),
    new \my\array\Hydrator()
);
```

## Plugins
### Introduction
#### Configuration
The plugin "Deep one-to-one or many-to-one relation mapping" is always active. If you want to add other plugins, just do this when initializing the mapping object:
```php
$mapping->addPlugin(new \voilab\mapping\plugin\FirstInCollection());
```

#### Disable plugin management
If you don't want to call any plugin (even the default one), simply set the plugin key separator to null.
```php
$mapping->setPluginKeySeparator(null);
```

### Deep one-to-one or many-to-one relation mapping
Goes through a tree of one-to-one or many-to-one relations, until it reaches the key (here: type for section and name for interests).
```php
$mapped = $mapping->map($data, [
    'sectionType' => 'work.section.type',
    'interests' => [[
        'contactName' => 'contact.name'
    ]]
]);

// results in
[
    'sectionType' => 'Social',
    'interests' => [
        ['contactName' => 'Some author']
        ['contactName' => null]
    ]
]
```

### Deep first one-to-many or many-to-many relation mapping
When encountering a one-to-many or many-to-many relation, it fetch the 1st element in the collection, and then tries to fetch the other relations, before accessing the key (here: name).
```php
$mapping->addPlugin(new \voilab\mapping\plugin\FirstInCollection());

$mapped = $mapping->map($data, [
    'firstInterestContactName' => 'interests[].contact.name'
]);

// results in
[
    'firstInterestContactName' => 'Some author'
]
```

## Testing

``` bash
$ phpunit
```

## Security

If you discover any security related issues, please email fabien@voilab.org instead of using the issue tracker.

## Credits

- [tafel](https://github.com/tafel)
- [voilab](https://github.com/voilab)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
