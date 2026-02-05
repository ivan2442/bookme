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

## Užitočné príkazy pre správu súborov

Ak potrebujete len rýchlo premiestniť súbory von a späť:

**1. Presun von (pred aktualizáciou):**
```bash
mv storage/app/public ../storage_public_backup
```

**2. Presun späť (po aktualizácii):**
```bash
rm -rf storage/app/public && mv ../storage_public_backup storage/app/public && php artisan storage:link
```

## Aktualizácia projektu (Production)

Ak chcete aktualizovať projekt na produkcii a neprijsť o nahraté súbory (obrázky profilov a pod.), použite tento postup:

### Možnosť A: Rýchla aktualizácia (odporúčané)
Tento spôsob zachováva súbory a len stiahne zmeny z Gitu.
```bash
git pull origin main
composer install --no-dev
php artisan migrate --force
npm install
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Možnosť B: Kompletné preinštalovanie (Integrovaná záloha)
Ak potrebujete priečinok zmazať a znova naklonovať, použite tento skript, ktorý automaticky zálohuje a obnoví vaše súbory:

```bash
# 1. Presun do nadradeného priečinka (ak ste v 'web')
cd ..

# 2. ZÁLOHA: Presun nahratých súborov von z projektu pred zmazaním
mv web/storage/app/public ./storage_public_backup

# 3. Odstránenie starého kódu a stiahnutie nového
rm -rf web
git clone https://github.com/ivan2442/bookme.git bookme

# 4. Inštalácia v novom priečinku
cd bookme
composer install
php artisan key:generate
php artisan migrate --force
npm install
npm run build

# 5. Príprava cieľového priečinka (web)
cd ..
mv bookme web
cd web

# 6. OBNOVA: Vrátenie nahratých súborov zo zálohy späť
rm -rf storage/app/public
mv ../storage_public_backup storage/app/public

# 7. Finalizácia (linkovanie, práva a cache)
php artisan storage:link
chmod -R 755 storage/app/public
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Príkazy pre rýchle nasadenie (Copy-Paste)

Tento skript spustite priamo v priečinku `web`. Zabezpečí zálohu vašich súborov, stiahnutie nového kódu a kompletnú re-inštaláciu.

```bash
# 1. Záloha a presun do nadradeného priečinka
mv storage/app/public ../storage_public_backup
cd ../

# 2. Odstránenie starého webu a stiahnutie nového
rm -rf web
git clone https://github.com/ivan2442/bookme.git bookme
cd bookme

# 3. Inštalácia
composer install
php artisan key:generate
php artisan migrate --force
npm install
npm run build
rm public/storage
php artisan storage:link

# 4. Premenovanie priečinka späť na 'web'
cd ../
mv bookme web
cd web

# 5. Obnova nahratých súborov (storage)
rm -rf storage/app/public
mv ../storage_public_backup storage/app/public
rm public/storage
ln -s ../storage/app/public public/storage 

# 6. Práva a cache
chmod -R 755 storage/app/public
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
