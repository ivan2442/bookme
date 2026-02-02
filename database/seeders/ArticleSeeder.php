<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first();

        Article::updateOrCreate(
            ['slug' => Str::slug('5 tipov ako získat viac rezervácií')],
            [
                'title' => '5 tipov, ako získať viac rezervácií',
                'excerpt' => 'Zistite, ako správne nastavený profil a fotky môžu zvýšiť počet vašich zákazníkov až o 40%.',
                'content' => "V dnešnej digitálnej dobe je váš online profil prvým kontaktom so zákazníkom. \n\n1. Kvalitné fotografie sú základ. \n2. Podrobný popis služieb pomáha predávať. \n3. Recenzie budujú dôveru. \n4. Rýchla odozva je kľúčová. \n5. Pravidelná aktualizácia dostupnosti.",
                'category' => 'Marketing',
                'author_id' => $author?->id,
                'published_at' => now(),
            ]
        );

        Article::updateOrCreate(
            ['slug' => Str::slug('preco prejst na online rezervacny system')],
            [
                'title' => 'Prečo prejsť na online rezervačný systém',
                'excerpt' => 'Telefonáty počas práce vás vyrušujú. Nechajte zákazníkov, nech sa objednajú sami.',
                'content' => "Online rezervácie šetria váš čas aj nervy. \n\nZákazníci oceňujú možnosť objednať sa kedykoľvek, aj o polnoci. Vy sa zatiaľ môžete sústrediť na svoju prácu. BookMe vám v tom pomôže.",
                'category' => 'Technológie',
                'author_id' => $author?->id,
                'published_at' => now()->subDays(2),
            ]
        );

        Article::updateOrCreate(
            ['slug' => Str::slug('novinky v systeme bookme pre rok 2026')],
            [
                'title' => 'Novinky v systéme BookMe pre rok 2026',
                'excerpt' => 'Pripravili sme pre vás automatické SMS pripomienky a integráciu s platobnými bránami.',
                'content' => "Snažíme sa BookMe neustále zlepšovať. \n\nTento rok prinášame SMS notifikácie, vylepšený dizajn kalendára a možnosť online platieb vopred, čo zníži počet nedostavení sa na termín.",
                'category' => 'Novinky',
                'author_id' => $author?->id,
                'published_at' => now()->subDays(5),
            ]
        );
    }
}
