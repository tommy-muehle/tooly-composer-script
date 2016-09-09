# Run checks locally with Docker

To avoid development failures you can use this tiny suite which executes all needed checks in different PHP versions.

## Requirements

* Docker
* Docker-Compose

## Usage

```
$ chmod +x ./run.sh
$ ./run.sh
```

## Example output

On success

```
Run checks with PHP 5.6.25                                                      [ INFO ]
Run security-checker                                                            [  OK  ]
Check PSR2 codestyle                                                            [  OK  ]
Run copy paste detection                                                        [  OK  ]
Run tests                                                                       [  OK  ]
Run checks with PHP 7.0.10                                                      [ INFO ]
Run security-checker                                                            [  OK  ]
...
```

or on failure:

```
Run checks with PHP 5.6.25                                                      [ INFO ]
Run security-checker                                                            [  OK  ]
Check PSR2 codestyle                                                            [ FAIL ]
Failed command: docker-compose run php-5.6 php bin/phpcs.phar --standard=PSR2 ./src -v[ FAIL ]
```
