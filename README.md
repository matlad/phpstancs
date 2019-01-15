phpStanCs
=========
This is small hack, that allows you to fob off the phpStan report into phpStorm as a PHP code sniffer.
This is not realy good solution, but i don't know Java and this wos easier for me.
This is for now, till plugin would be created. I heard somewhere, that somebody may working on it.

![notice example](resources/readmeImg/noticeExample.png)

![inspection example](resources/readmeImg/inspectionExample.png)

## Install

Into composer.jsom add 
```json5
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/matlad/phpstancs"
        }
    ],
```
and than run in console
```bash
composer require matla/phpstancs 
```

## Usage and settings 
### Nastavení phpStormu
Open File->Settings->Language & Frameworks -> PHP -> Quality Tools -> Code Sniffer
and set path to __vendor/matla/phpstancs/bin/phpcs__
![img](resources/readmeImg/setPhpStorm1.png)
You also need allowed inspection from phpcs
![img2](resources/readmeImg/setPhpStorm2.png)
### Nastavení phpStanu a PhpStanCS
phpStan is possible only by file phpstan.neon 
in root directory of project (location of composer.json).
If file like this exist, than must contain 
```neon
includes:
	- vendor/matla/phpstancs/extension.neon

```  
Settings for phpStanCs are in same file. 
It is parameter runCS (bool), which turn on/of phpcs inspection which is marget with php stan results.
```neon
parameters:
	runCS : true
```

## Authors
* Adam Mátl <code@matla.cz>
