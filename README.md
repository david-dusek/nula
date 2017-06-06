# Nula

Webová prezentace architektonického ateliéru PLUS MINUS NULA. Prezentace je veřejně dostupná na [http://www.plusminusnula.cz](http://www.plusminusnula.cz).

## Instalace

1. naklonujte si tento repositář: `git clone git@github.com:david-dusek/nula.git`
2. naistalujte závislosti pomocí Composeru: `composer install`

## Přenos změn na hosting

### Nastavení

V rootu projektu

1. vytvořte si soubor s nastavením: `cp ./deployment/deployment.example.ini ./deployment/deployment.ini`
2. proveďte změny v souboru `./deployment/deployment.ini` podle svých potřeb (minimálně položky: `remote`, `user`, `password`)

### Přenos změn

V rootu projektu spusťte příkaz: `php app/vendor/bin/deployment deployment/deployment.ini`

