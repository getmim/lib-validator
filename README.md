# lib-validator

Adalah module yang bertugas mem-validasi suatu data. Module ini juga
yang digunakan oleh `lib-form` untuk validasi form.

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-validator
```

## Penggunaan

Module ini menyediakan satu library dengan nama `LibValidator\Library\Validator`
yang digunakan untuk mem-validasi data.

```php
use LibValidator\Library\Validator;

$rules = [
    'id' => [
        'rules' => [
            'required' => true
        ],
        'filters' => [
            'number' => true
        ]
    ],
    'name' => [
        'rules' => [
            'required' => true,
            'array' => true
        ],
        'children' => [
            'first' => [
                'rules' => [
                    'required' => true,
                    'text' => 'alnum'
                ]
            ],
            'last' => [
                'rules' => [
                    'required' => true,
                    'text' => 'alnum'
                ],
                'filters' => [
                    'string' => true
                ]
            ]
        ]
    ],
    'team' => [
        'rules' => [
            'array' => true
        ],
        'children' => [
            '*' => [
                'rules' => [
                    'array' => true,
                ],
                'children' => [
                    'name' => [
                        'required' => true
                    ]
                ]
            ]
        ]
    ]
];

$object = [
    'id' => '12',
    'name' => [
        'first' => 'Mim',
        'middle' => 'PHP',
        'last' => 'Framework'
    ],
    'team' => [
        ['name' => 'Worker'],
        ['name' => 'cURL']
    ]
];

list($result, $errors) = Validator::validate(objectify($rules), objectify($object));

// $result berisi informasi object setelah melewati validator
// dan filters
// #errors berisi informasi errors masing-masing fields.

```

Pada contoh di atas, masing-masing field memiliki nilai array yang
memiliki array key `rules`. Properti ini berisi daftar rules yang
akan di coba ke nilai nya.

Untuk indexed array, nilai `*` pada `children` property menandakan
rules-rules tersebut di test ke masing-masing data di dalam array
tersebut.

## Rules

Di bawah adalah daftar rule-rule validator yang dikenali sampai
saat ini.

### array

```php
// ...
    'array' => true
// ...
```

Validator untuk menentukan jika nilai adalah array. Nilai yang
diterima adalah `true` untuk menentukan nilai adalah array. `indexed`
untuk menentukan bahwa nilai adalah indexed array atau `assoc` untuk
menentukan jika nilai adalah array yang bukan indexed array.

### callback

```php
// ...
    'callback' => 'Class::method'
// ...
```

Menggunakan handler lain untuk validasi suatu nilai. Callback diharapkan
tidak mengembalikan nilai apapun jika valid, dan mengembalikan array error dan
tambahan parameter untuk locale.

```php
$errors = ['20.0', ['value' => $value]];
```

Fungsi callback akan di panggil dengan parameters sebagai berikut:

```php
Class::method($value, $options, $object, $field, $rules): ?array;
```

Dimana:

1. `$value` Nilai object yang akan di validasi
1. `$options` Nilai validator rule.
1. `$object` Semua data object yang sedang di validasi dalam satu level parent.
1. `$field` Nama properti dari `$object` yang akan di validasi.
1. `$rules` Semua rules yang sedang di validasi untuk `$value` ini.

### date

```php
// ...
    'date' => 'Y-m-d'
// ...
```

Validator untuk nilai date, dimana nilai dari rule ini adalah format
tanggal yang diterima.

### email

```php
// ...
    'email' => true
// ...
```

Validator untuk nilai email. Rule ini hanya menerima nilai `true`.

### in

```php
// ...
    'in' => ['a','b']
// ...
```

Validator untuk memastikan nilai yang dikirim adalah salah satu dari
array.

### ip

```php
// ...
    'ip' => true
    'ip' => '4|6'
// ...
```

Validator untuk memastikan nilai yang dikirim adalah valid ip. Nilai
ini menerima nilai `true` untuk menerima nilai valid ip, atau bisa juga
nilai 4 untuk menerima hanya IPv4, atau 6 untuk menerima hanya IPv6.

### length

```php
// ...
    'length' => [
        'min' => :int, // optional if max set.
        'max' => :int  // optional if min set.
    ]
// ...
```

Validator untuk panjang suatu string.

### notin

```php
// ...
    'notin' => ['a','b']
// ...
```

Validator kebalikan dari `in`, rule ini memastikan nilai yang diinput
bukan salah satu dari list.

### numeric

```php
// ...
    'numeric' => true,
    'numeric' => [
        'min' => :int, // optional
        'max' => :int,  // optional
        'decimal' => :int // optional
    ]
// ...
```
Validator untuk nilai numeric. Rule ini bisa menerima nilai
`true` untuk validasi numeric saja, atau bisa juga array dengan
properti `min`, `max`, dan/atau `decimal` untuk menentukan minimal
nilai, maksimal nilai, dan `decimal` untuk banyaknya angka dibelakang
koma.

### object

```php
// ...
    'object' => true
// ...
```

Validator untuk mencek jika nilai adalah object.

### regex

```php
// ...
    'regex' => '!^.+$!'
// ...
```

Validator yang akan mencocokan nilai regex dengan nilai data yang dikirim.

### required

```php
// ...
    'required' => true
// ...
```

Validator untuk memastikan nilai harus ada dan bukan null. Nilai 0, dan
false akan dianggap valid.

### text

```php
// ...
    'text' => 'alnumdash'
// ...
```

Validator text. Rule ini menerima nilai:

1. `slug` untuk `a-z`, `0-9`, dan `-_`.
1. `alnumdash` untuk `a-z`, `A-Z`, `0-9`, dan `-`.
1. `alpha` untuk `a-z`, dan `A-Z`.
1. `alnum` untuk `a-z`, `A-Z`, dan `0-9`.

### url

```php
// ...
    'url' => true
    'url' => [
        'path' => true, // optional
        'query' => true | ['page', 'rpp'], // optional
    ]
// ...
```

Valiator untuk memastikan nilai yang dikirim adalah valid url.
Rule ini menerima nilai `true` untuk memastikan nilai adalah valid
`url`. Atau bisa juga array yang berisi beberapa sub-properti
untuk memastikan url memiliki kondisi-kondisi lain. Nilai sub-properti
`query` menerima nilai `true` yang memastikan url memiliki query
string, atau array yang memastikan query string memiliki query tersebut.

## Filters

Filters adalah fungsi-fungsi yang bertugas mengubah nilai suatu object.

Di bawah ini adalah daftar filters yang sudah ada di module init:

1. `array` Mengubah nilai menjadi array.
1. `boolean` Mengubah nilai menjadi boolean.
1. `float` Mengubah nilai menjadi float.
1. `lowercase` Mengubah nilai menjadi lowercase.
1. `integer` Mengubah nilai menjadi integer.
1. `object` Mengubah nilai menjadi object.
1. `string` Mengubah nilai menjadi string.
1. `ucwords` Mengimpelmentasikan fungsi `ucwords` ke nilai.
1. `uppercase` Mengubah nilai menjadi uppercase.

## Custom Rules

Developer di beri kebebasan untuk membuat rule mereka sendiri. Untuk membuat
custom rule, pastikan mendaftarkan pada konfigurasi module seperti di bawah:

```php
return [
    // ...
    'libValidator' => [
        'validators' => [
            'ifirst' => 'MyRule\\Rules\\Custom::ifirst'
        ]
    ]
    // ...
];
```

Kemudian buatkan class dengan static method untuk validator ini:

```php
namespace MyRule\Rules;

class Custom{
    static function ifirst($value, $options, $object, $field, $rules): ?array{
        if(substr($value, 0, 1) === 'i')
            return null;
        return ['20.0'];
    }
}
```

Parameter yang digunakan saat memanggil fungsi ini adalah:

1. `$value` Nilai yang perlu di validasi
1. `$options` Nilai rule options pada kofigurasi, pada contoh di bawah, nilai
ini menjadi `true`.
1. `$object` Object dimana nilai ini diambil.
1. `$field` Nama `$object` properti darimana nilai diambil.
1. `$rules` Daftar semua rules yang juga di test pada nilai ini.

Contoh penggunaan pada aplikasi adalah sebagai berikut:

```php
$rules = [
    'name' => [
        'rules' => [
            'ifirst' => true
        ]
    ]
];
```

## Custom Filters

Jika filter yang tersedia masih kurang, maka developer di perbolehkan
menambahkan filter buatannya sendiri. Pastikan membuat sebuah class dengan
static method yang mengembalikan nilai akhir yang akan dikirimkan ke 
controler. Contoh di bawah adalah contoh sederhana filter yang mengubah
suatu nilai menjadi integer:

```php
namespace MyFilter\Filter;

class Custon
{
    static function int($value, $options, $object, $field, $filters){
        return (int)$value;
    }
}
```

Kemudian tambahkan filer tersebut ke konfigurasi module dengan cara berikut:

```php
return [
    // ...
    'libValidator' => [
        'filters' => [
            'int' => 'MyFilter\\Filter\\Custom::int'
        ]
    ]
    // ...
];
```

Fungsi ini akan di panggil oleh validator dengan parameters:

1. `$value` Nilai yang perlu di proses.
1. `$options` Nilai properti filters di konfigurasi. Pada contoh di bawah,
nilai dari `$options` adalah `true`.
1. `$object` Object darimana nilai ini diambil.
1. `$fields` Nama properti dari nilai ini pada `$object`.
1. `$filters` Daftar filters yang lain yang juga di implementasikan
ke nilai ini.

Filter tersebut kemudian bisa digunakan di validator dengan bentuk seperti:

```php
$rules = [
    'age' => [
        'rules' => [...],
        'filters' => [
            'int' => true
        ]
    ]
];
```

## Error Code

Masing-masing error harus memiliki error code. Dan di bawah ini adalah error
code yang sudah di daftarkan oleh module ini. Jika membuat custom error, pastikan
menggunakan error code yang belum ada di bawah. Dan masing-masing validator harus
menggunakan nilai error yang berbeda:

number  | rule      | description
--------|-----------|------------
1.0     | array     | Not an array
1.1     | array     | Not indexed array
1.2     | array     | Not assoc array
2.0     | date      | Not a date, or invalid format
3.0     | email     | Not an email
4.0     | in        | Not in array
5.0     | ip        | Not an IP
5.1     | ip        | Not an IPv4
5.2     | ip        | Not an IPv6
6.0     | length    | Too short
6.1     | length    | Too long
7.0     | notin     | Object in array
8.0     | numeric   | Not numeric
8.1     | numeric   | Too less
8.2     | numeric   | Too great
8.3     | numeric   | Decimal point not match
9.0     | object    | Not an object
10.0    | regex     | Not match
11.0    | required  | Not present
12.0    | text      | Not a text
12.1    | text      | Not a slug
12.2    | text      | Not an alnumdash
12.3    | text      | Not an alpha
12.4    | text      | Not an alnum
13.0    | url       | Not an URL
13.1    | url       | Don't have path
13.2    | url       | Don't have query
13.3    | url       | Required query not present

Module-module lain yang juga mendaftarkan error code adalah sebagai berikut:

number | rule        | module     | description
-------|-------------|------------|------------
14.0   | unique      | lib-model  | Not unique.
15.0   | upload-form | lib-upload | Upload form not found.
16.0.1 | upload-file | lib-upload | File size too small.
16.0.2 | upload-file | lib-upload | File size too big.
16.1   | upload-file | lib-upload | Mime type not accepted.
16.2   | upload-file | lib-upload | File extension not accepted.
16.3.1 | upload-file | lib-upload | File image width too small.
16.3.2 | upload-file | lib-upload | File image width too big.
16.4.1 | upload-file | lib-upload | File image height too small.
16.4.2 | upload-file | lib-upload | File image height too big.

Untuk menambahkan error code yang lain, pastikan menambahkan nilai
seperti di bawah pada konfigurasi module:

```php
// ...
    'libValidator' => [
        'errors' => [
            '20.0' => 'language.error.transaltion_key'
        ]
    ]
// ...
```

Selain itu, module juga diharapkan menambahkan locale nya sendiri.
Silahkan mengacu pada lib-locale untuk menambahkan locale untuk
error tersebut.

## Test

Jalankan perintah di bawah untuk unit test:

```bash
phpunit test
```