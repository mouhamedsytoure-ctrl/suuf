<?php

namespace Database\Seeders;

use App\Models\Activite;
use App\Models\Culture;
use App\Models\Exploitation;
use App\Models\Intrant;
use App\Models\OperationFinanciere;
use App\Models\Parcelle;
use App\Models\TestSol;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Utilisateurs de démonstration (mot de passe : password) ----
        $chef = User::firstOrCreate(
            ['email' => 'tolou@suuf.test'],
            ['name' => 'Tolou Ibou', 'password' => 'password', 'telephone' => '77 123 45 67', 'region' => 'Keur Mbaye']
        );
        $chef->syncRoles('chef_exploitation');

        $prod = User::firstOrCreate(
            ['email' => 'awa@suuf.test'],
            ['name' => 'Awa Diop', 'password' => 'password', 'region' => 'Darou Salam']
        );
        $prod->syncRoles('producteur');

        $agent = User::firstOrCreate(
            ['email' => 'moussa@suuf.test'],
            ['name' => 'Moussa Ba', 'password' => 'password', 'region' => 'Keur Mbaye']
        );
        $agent->syncRoles('agent_terrain');

        // ---- Exploitation du chef ----
        $exp = Exploitation::firstOrCreate(
            ['user_id' => $chef->id, 'nom' => 'Ferme Keur Mbaye'],
            ['region' => 'Keur Mbaye', 'surface_totale' => 32.45, 'description' => 'Exploitation maraîchère']
        );

        // ---- Parcelles ----
        $parcelles = [
            ['code' => 'P01', 'lieu' => 'Keur Mbaye', 'surface' => 1.25, 'type_sol' => 'Limoneux', 'source_eau' => 'Forage', 'statut' => 'Active'],
            ['code' => 'P02', 'lieu' => 'Keur Mbaye', 'surface' => 1.80, 'type_sol' => 'Limoneux', 'source_eau' => 'Forage', 'statut' => 'Récolte'],
            ['code' => 'P03', 'lieu' => 'Darou Salam', 'surface' => 2.50, 'type_sol' => 'Sablo-limoneux', 'source_eau' => 'Puits', 'statut' => 'Active'],
            ['code' => 'P04', 'lieu' => 'Darou Salam', 'surface' => 1.10, 'type_sol' => 'Sablo-limoneux', 'source_eau' => 'Puits', 'statut' => 'Active'],
            ['code' => 'P05', 'lieu' => 'Ndombo', 'surface' => 2.00, 'type_sol' => 'Argileux', 'source_eau' => 'Canal', 'statut' => 'Active'],
        ];
        $created = [];
        foreach ($parcelles as $p) {
            $created[$p['code']] = Parcelle::firstOrCreate(
                ['exploitation_id' => $exp->id, 'code' => $p['code']],
                $p + ['latitude' => 16.2415, 'longitude' => -15.8210]
            );
        }

        // ---- Cultures ----
        $cultures = [
            ['P01', 'Oignon', 'Violet de Galmi', '2024-03-12', '2024-06-18', 22, 'En cours'],
            ['P02', 'Tomate', 'Roma VF', '2024-02-02', '2024-06-12', 35, 'Récolte'],
            ['P03', 'Chou', 'Pommé KK', '2024-03-20', '2024-06-30', 40, 'En cours'],
            ['P04', 'Carotte', 'Nantaise', '2024-04-05', '2024-07-10', 30, 'En cours'],
            ['P05', 'Piment', 'Safi', '2024-03-15', '2024-06-25', 12, 'En cours'],
        ];
        foreach ($cultures as $c) {
            Culture::firstOrCreate(
                ['parcelle_id' => $created[$c[0]]->id, 'nom' => $c[1]],
                ['variete' => $c[2], 'date_semis' => $c[3], 'date_recolte_prevue' => $c[4], 'rendement_attendu' => $c[5], 'statut' => $c[6]]
            );
        }

        // ---- Activités ----
        $activites = [
            ['P01', 'Fertilisation', '2024-06-10', 'Moussa Ba', 45000, 'Terminé'],
            ['P03', 'Irrigation', '2024-06-09', 'Awa Diop', 12000, 'Terminé'],
            ['P05', 'Traitement phyto', '2024-06-08', 'Moussa Ba', 30000, 'Terminé'],
            ['P02', 'Récolte', '2024-06-07', 'Équipe A', 80000, 'En cours'],
        ];
        foreach ($activites as $a) {
            Activite::firstOrCreate(
                ['parcelle_id' => $created[$a[0]]->id, 'type' => $a[1], 'date' => $a[2]],
                ['responsable' => $a[3], 'cout' => $a[4], 'statut' => $a[5]]
            );
        }

        // ---- Intrants ----
        $intrants = [
            ['Urée 46%', 'Engrais', 'kg', 50, 100, 18000],
            ['NPK 15-15-15', 'Engrais', 'kg', 400, 100, 21000],
            ['Semence Oignon', 'Semence', 'kg', 12, 5, 9500],
            ['Mancozèbe', 'Phyto', 'L', 8, 10, 7200],
            ['Glyphosate', 'Phyto', 'L', 0, 5, 6800],
        ];
        foreach ($intrants as $i) {
            Intrant::firstOrCreate(
                ['exploitation_id' => $exp->id, 'nom' => $i[0]],
                ['categorie' => $i[1], 'unite' => $i[2], 'stock' => $i[3], 'seuil_alerte' => $i[4], 'prix_unitaire' => $i[5]]
            );
        }

        // ---- Tests de sol ----
        $tests = [
            ['P01', '2024-06-08', 6.4, 42, 1.1, 1.2, 28, 15, 120, 27, 'Bon'],
            ['P03', '2024-06-07', 5.6, 38, 0.8, 0.9, 18, 9, 90, 26, 'Acide'],
            ['P05', '2024-06-05', 7.1, 55, 2.4, 2.6, 35, 22, 160, 28, 'Salin'],
        ];
        foreach ($tests as $t) {
            TestSol::firstOrCreate(
                ['parcelle_id' => $created[$t[0]]->id, 'date' => $t[1]],
                [
                    'ph' => $t[2], 'humidite' => $t[3], 'salinite' => $t[4], 'conductivite' => $t[5],
                    'azote' => $t[6], 'phosphore' => $t[7], 'potassium' => $t[8], 'temperature' => $t[9],
                    'statut' => $t[10], 'latitude' => 16.2415, 'longitude' => -15.8210,
                ]
            );
        }

        // ---- Opérations financières ----
        $ops = [
            ['recette', 'Marché', 420000, '2024-06-08', 'Vente tomate'],
            ['recette', 'Marché', 2430000, '2024-06-01', 'Ventes diverses'],
            ['depense', 'Intrants', 45000, '2024-06-10', 'Achat urée'],
            ['depense', 'Matériel', 60000, '2024-06-06', 'Location tracteur'],
            ['depense', 'Main-d\'œuvre', 1140000, '2024-06-01', 'Salaires équipe'],
        ];
        foreach ($ops as $o) {
            OperationFinanciere::firstOrCreate(
                ['exploitation_id' => $exp->id, 'type' => $o[0], 'categorie' => $o[1], 'date' => $o[3], 'montant' => $o[2]],
                ['description' => $o[4]]
            );
        }

        $this->command->info('Démo SUUF créée : tolou@suuf.test / awa@suuf.test / moussa@suuf.test (mot de passe : password)');
    }
}
