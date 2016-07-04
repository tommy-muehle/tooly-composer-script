# tooly-composer-script

[![Build Status](https://travis-ci.org/tommy-muehle/tooly-composer-script.svg?branch=master)](https://travis-ci.org/tommy-muehle/tooly-composer-script)
[![Latest Stable Version](https://poser.pugx.org/tm/tooly-composer-script/v/stable)](https://packagist.org/packages/tm/tooly-composer-script)
[![Total Downloads](https://poser.pugx.org/tm/tooly-composer-script/downloads)](https://packagist.org/packages/tm/tooly-composer-script)
[![License](https://poser.pugx.org/tm/tooly-composer-script/license)](https://packagist.org/packages/tm/tooly-composer-script)

With the tooly [composer-script](https://getcomposer.org/doc/articles/scripts.md) aka hook you can manage needed phar files, such as phpunit, in your composer.json.
Every phar file will be saved in the [composer binary directory](https://getcomposer.org/doc/articles/vendor-binaries.md).

## Example

For an quick example look at the [composer.json](composer.json#L48-L57) here.
 
## Install

To use the script just do the following single command:

*Note: There are no further dependencies in this library. Only PHP.*

```
composer require tm/tooly-composer-script
```

Then add the script in the composer.json under *"scripts"* with the event names you want to trigger.
For example:

```
...
"scripts": {
    "post-install-cmd": "Tooly\\ScriptHandler::installPharTools",
    "post-update-cmd": "Tooly\\ScriptHandler::installPharTools"
  },
...
```

Look [here](https://getcomposer.org/doc/articles/scripts.md#event-names) for more informations about composer events.

## Usage

The composer.json scheme has a part "extra" which is used for the script.
Its described [here](https://getcomposer.org/doc/04-schema.md#extra).

In this part you can add your needed phar tools under the key "tools".

```
...
"extra": {
    ...
    "tools": {
      "phpunit": {
        "url": "https://phar.phpunit.de/phpunit-4.8.9.phar",
        "only-dev": true
      },
      "phpcpd": {
        "url": "https://phar.phpunit.de/phpcpd-2.0.4.phar",
        "only-dev": true
      }
    }
    ...
  }
...
```

After you add the name of the tool as key, you need only one further parameter. The *"url"*.
The url can be a link to a specific version, such as x.y.z, or a link to the latest version for this phar.

Every time you update or install with composer the phar tools are checked. You are asked if you want to overwrite
the existing phar if the remote and local phar has not the same checksum. 

The *"only-dev"* parameter is optional. Its default true and means that this phar is only needed in developing mode.
So the command ```composer [install|update] --no-dev``` ignores this phar tool.

## Contributing

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md) for information on how to contribute.
