phpstancs
=========
Slouží k podstrčení phpStanu phpStormu jako phpcs

![notice example](resources/readmeImg/noticeExample.png)

![inspection example](resources/readmeImg/inspectionExample.png)

## Instalace

Do composer.jsom přidej 
```json5
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/matlad/phpstancs"
        }
    ],
```
a pak už jen
```bash
composer require matla/phpstancs 
```

## Použití 
### Nastavení phpStormu
File->Settings->Language & Frameworks -> PHP -> Quality Tools -> Code Sniffer
a nastavit cestu k vendor/matla/phpstancs/bin/phpcs  
![img](resources/readmeImg/setPhpStorm1.png)
taky je potřeba mýt povolenou inspekci od phpcs
![img2](resources/readmeImg/setPhpStorm2.png)
### Nastavení phpStanu a PhpStanCS
phpStan je možno nastavit pouze pomocí konfiguračního souboru phpstan.neon 
v root adresáři projektu (tam kde je composer.json)
pokud takovíto soubor existuje musí obsahovat 
```neon
includes:
	- vendor/matla/phpstancs/extension.neon

```  
PhpStanCS jde nastavit stejným souborem
rozšiřuje nastavení o parameter runCS (bool), který zapíná vypíná phpcs
```neon
parameters:
	runCS : true
```

## Authors
* Adam Mátl <code@matla.cz>
