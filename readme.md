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
## Usage

``` php
// have a set of data structure, like $user['groups'][0]['name']
$data = $user;
$map = new \voilab\mapping\Mapping();
$result = $map->map($user, [
    'id',
    'name' => 'login',
    'groups' => [[
        'id',
        'name'
    ]]
]);
print_r($result);
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