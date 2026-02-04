<x-mail::message>

<div style="text-align: center; padding: 20px 0;">
<img src="{{ asset('favicon.png') }}" width="80" height="80" alt="BookMe Logo">
</div>

# Potvrdenie registrácie prevádzky

Dobrý deň,

Vaša prevádzka **{{ $profile->name }}** bola úspešne zaregistrovaná v systéme BookMe.

Ako bonus získavate **prvé 3 mesiace používania systému úplne zadarmo**. Vaše bezplatné obdobie končí {{ $profile->trial_ends_at->format('d.m.Y') }}. Potom bude systém spoplatnený sumou 20 € mesačne.

Momentálne čaká na odobrenie administrátorom. Po schválení bude vaša prevádzka verejne dohľadateľná na našej hlavnej stránke.

**Dovtedy je však váš profil plne funkčný!** Môžete ho zdieľať so svojimi zákazníkmi cez tento unikátny odkaz:

<x-mail::button :url="route('profiles.show', $profile->slug)">
Zobraziť môj profil
</x-mail::button>

Váš rezervačný odkaz: [{{ route('profiles.show', $profile->slug) }}]({{ route('profiles.show', $profile->slug) }})

Môžete sa prihlásiť do svojho dashboardu a začať nastavovať služby, zamestnancov a pracovnú dobu:

<x-mail::button :url="route('auth.login')">
Prihlásiť sa do systému
</x-mail::button>

---

### 🛠 Návod na správu vašej prevádzky

Aby vaša prevádzka mohla začať naplno prijímať rezervácie, odporúčame nasledovný postup:

1. **Služby (sekcia Služby)**
Vytvorte si zoznam služieb, ktoré ponúkate. Pri každej službe zadajte názov, dĺžku trvania a cenu. Bez vytvorených služieb si zákazníci nebudú môcť vytvoriť rezerváciu.

2. **Zamestnanci (sekcia Zamestnanci)**
Pridajte členov vášho tímu. Každému zamestnancovi môžete následne priradiť konkrétne služby, ktoré vykonáva.

3. **Pracovná doba (sekcia Časy)**
Nastavte si časy, kedy ste pre klientov dostupní. Môžete definovať všeobecné otváracie hodiny alebo individuálne rozvrhy pre jednotlivých zamestnancov vrátane prestávok.

4. **Sviatky a uzávierky (sekcia Sviatky)**
Ak potrebujete jednorazovo zablokovať termín (napr. dovolenka alebo návšteva lekára), využite túto sekciu na vytvorenie blokácie.

5. **Vzhľad a nastavenia (sekcia Kalendár)**
Nahrajte si logo a banner prevádzky, napíšte krátky popis a upravte dĺžku rezervačných slotov (napr. každých 30 minút).

6. **Dashboard (Prehľad)**
Na hlavnej obrazovke uvidíte všetky nadchádzajúce rezervácie, interaktívny kalendár na vybraný deň a rýchle štatistiky. Rezervácie môžete presúvať, upravovať alebo označovať ako vybavené.

7. **Platby (Prehľad platieb)**
Detailné vyhodnotenie vašej prevádzky – počet rezervácií, odpracované hodiny a celkové tržby za vybrané obdobie.

---

Tešíme sa na spoluprácu!

S pozdravom,<br>
Tím {{ config('app.name') }}
</x-mail::message>
