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

## Mapping structure

``` php
$mapping = [
    // int key = (no key) same value for mapping and data array
    'id',
    // string key = set a different key for mapping
    'name' => 'familyname',
    // function = return the custom value. $data is either an object or
    // an array, depending on the resultset hydratation
    'address' => function (MyObject $data) {
        return $data->getNpa() . ' ' . $data->getCity();
    },
    // value with dots = fetch directely a child relation field. Last
    // element is field. Each other is a relation. If relation is a
    // collection, fetch the first one
    'user_first_group_id' => 'groups[].id',
    'coord_type_name' => 'coordinate.type.text1',
    // array = simple relation
    'politeness' => [
        'short' => 'text2',
        'long' => 'text1'
    ],
    // array = collection relations (double array)
    'groups' => [[
        'id',
        'name'
    ]],
    // change relation's data accessor
    'relation_a' => [
        \voilab\mapping\Mapping::RELATION_KEY => 'relationKeyA',
        'id'
    ],
    // wildcard to fetch all fields with relations. If it's an object,
    // it must implement an array iterator
    '*'
];
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
        return $data['email'] == 'john@doe.ch' ? 'some.default@email.ch' : $data['email'];
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

### Change relation mapping key
```php
$mapped = $mapping->map($data, [
    'nextWork' => [
        \voilab\mapping\Mapping::RELATION_KEY => 'work',
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

### Wildcard mapping
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

## Plugins
### One-to-one or many-to-one relation mapping
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

### One-to-many or many-to-many relation mapping
```php
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
