# Cipher Implementation

## Content:
- **Installation**
- **Usage and Extending**
- **KPA**
- **Testing**

This package presents a basic cipher implementation using OOP practices. The main goal is to demonstrate a way how OOP can be helpful implementing such a mechanism.

## Installation
This project requires the presence of these softwares:
- [php](https://www.php.net/downloads.php)
- [composer, php's package manager](https://getcomposer.org/download/)

Then run the command in the root directory to install required packages:
``` bash
# if composer is locally installed
php .\composer.phar install

# if globally
composer install
```

## Usage and Extending
The `App\Provider\CipherServiceProvider` is a simple [factory](https://refactoring.guru/design-patterns/factory-method) which creates a cipher. The cipher's capability to decrypt/encrypt payload relies on its dependencies.

It depends on the `App\LookupTable\LookupTableDTO` which statically holds the **lookup table** and provides bi-directional access to its content. At this current state it assumes that the DTO is created form (indexed) array. Thus its method names are representing this behaviour. Because the DTO is tightly coupled to the *cipher factory* changing its method names requires refactoring in other parts of the codebase.

It also needs a **cryptographer** which implements the `CryptographerInterface`. This contains the concrete implementation of the  encryption/decryption logic.
> `App\LookupTable\LookupTableDTO` is tightly coupled with the `CryptographerInterface` too. Watch out for changes in DTO methods!

Additionally it can accept a **secret key**, but this can also be injected when calling its methods, since it is a good practice to provide a new secret key to each message.

## KPA
The `App\Attacker\KPAttacker` can be used with a **cryptographer** which implements the `App\Attacker\KPACryptanalyserInterface`. It also needs an array of **encrypted messages**, an instance of the `App\LookupTable\LookupTableDTO` and a **dictionary**.

## Testing
The `App\Cryptographer\BasicCryptographer` class is used to test both the `App\Provider\CipherServiceProvider` and the `App\Attacker\KPAttacker`. These tests can be found in the `./tests` directory.

> This project uses the [PHPUnit](https://docs.phpunit.de/en/12.1/) framework for testing.

To run all tests use this command:
`php .\vendor\bin\phpunit .\tests\ --color`