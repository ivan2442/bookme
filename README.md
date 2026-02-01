# BookMe - Online Rezervačný Systém

BookMe je moderný online rezervačný systém postavený na frameworku Laravel. Tento návod vám pomôže spustiť projekt po jeho naklonovaní z GitHubu.

## Inštalácia a spustenie

Po naklonovaní repozitára (`git clone`) postupujte podľa týchto krokov v termináli:

### 1. Inštalácia PHP závislostí
```bash
composer install
```

### 2. Konfigurácia prostredia
Súbor `.env` nie je súčasťou repozitára z bezpečnostných dôvodov. Musíte si ho vytvoriť zo vzoru:
```bash
cp .env.example .env
```
Následne súbor `.env` otvorte a nastavte údaje k vašej databáze:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookme
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Vygenerovanie aplikačného kľúča
```bash
php artisan key:generate
```

### 4. Databázová migrácia
Spustite migrácie, aby sa vytvorili potrebné tabuľky:
```bash
php artisan migrate
```

### 5. Inštalácia a kompilácia frontendu
Tento projekt používa Vite. Nainštalujte závislosti a zostavte assety:
```bash
npm install
npm run build
```
*(Pre vývoj môžete použiť `npm run dev`)*

### 6. Nastavenie práv k priečinkom
Uistite sa, že Laravel môže zapisovať do potrebných priečinkov:
```bash
chmod -R 775 storage bootstrap/cache
```

## Lokálne spustenie
Pre spustenie vývojového servera:
```bash
php artisan serve
```
Aplikácia bude dostupná na `http://localhost:8000`.

## Rýchly prehľad príkazov (všetko za sebou)

Ak chcete spustiť všetko naraz po úprave `.env` súboru:

```bash
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
 
```
