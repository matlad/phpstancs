phpstancs
=========
Slouží k podstrčení phpStanu phpStormu jako phpcs

![example](resources/readmeImg/noticeExample.png)

## Instalace

Do composer.jsom přidej gi
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

## Authors
* Adam Mátl <code@matla.cz>
