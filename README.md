phpstancs
=========
Slouží k podstrčení phpStanu phpStormu jako phpcs

![example](resources/readmeImg/noticeExample.png)

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
### Nastavení phpcs
Vytvořte v root adresáři projektu (tam kde je composer.json) __stanCs.json__.  
Example:
```json5
{
  "stanLvl" : "max",
  "runCs": false
}
```
parametry
* "stanLvl" - phpstan analyse level (0,1,2,...,max)
* "runCs" - spustí i codeSniffer (bool)
## Authors
* Adam Mátl <code@matla.cz>
