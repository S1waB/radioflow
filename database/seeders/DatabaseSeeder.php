<?php

use App\Models\Guest;
use App\Models\User;
use App\Models\Team;
use App\Models\Task;
use App\Models\Radio;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Create General Admin Radio
        $adminRadio = Radio::create([
            'name' => 'Radio Flow Administration',
            'description' => 'System-wide administration',
            'phone_number' => '+21650000001',
            'address' => 'HQ Tunis',
            'Country' => 'Tunisia',
            'status' => 'active',
        ]);

        // 2️⃣ Seed global admin role
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'System Administrator',
            'hierarchy_level' => 1,
            'radio_id' => $adminRadio->id,
        ]);

        // 3️⃣ Create Admin User
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@radioflow.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'radio_id' => $adminRadio->id,
            'phone_number' => '+21650000000',
            'bio' => 'Super admin with full access',
        ]);

        // 4️⃣ Create Radio One & Two
        $radio1 = Radio::create([
            'name' => 'Radio One',
            'description' => 'Music and Talk shows',
            'phone_number' => '+21671111111',
            'address' => 'Tunis Center',
            'Country' => 'Tunisia',
            'status' => 'active',
        ]);

        $radio2 = Radio::create([
            'name' => 'Radio Two',
            'description' => 'Youth and Sports',
            'phone_number' => '+21672222222',
            'address' => 'Sfax Center',
            'Country' => 'Tunisia',
            'status' => 'active',
        ]);

        // 5️⃣ Seed roles for Radio One
        $rolesRadio1 = [
            ['directeur', 2, 'Director of Radio One'],
            ['animateur', 3, 'Radio Host'],
            ['journaliste', 4, 'Journalist'],
            ['tech staff', 5, 'Technical Staff'],
        ];

        foreach ($rolesRadio1 as $r) {
            Role::create([
                'name' => $r[0],
                'hierarchy_level' => $r[1],
                'description' => $r[2],
                'radio_id' => $radio1->id,
            ]);
        }

        // 6️⃣ Seed roles for Radio Two
        $rolesRadio2 = [
            ['directeur', 2, 'Director of Radio Two'],
            ['animateur', 3, 'Radio Host'],
            ['journaliste', 4, 'Journalist'],
            ['tech staff', 5, 'Technical Staff'],
        ];

        foreach ($rolesRadio2 as $r) {
            Role::create([
                'name' => $r[0],
                'hierarchy_level' => $r[1],
                'description' => $r[2],
                'radio_id' => $radio2->id,
            ]);
        }

        // 7️⃣ Create Managers
        $manager1 = User::create([
            'name' => 'Ali Manager',
            'email' => 'manager1@radio.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where(['radio_id' => $radio1->id, 'name' => 'directeur'])->value('id'),
            'radio_id' => $radio1->id,
            'phone_number' => '+21620000001',
            'bio' => 'Director of Radio One',
        ]);

        $manager2 = User::create([
            'name' => 'Mouna Manager',
            'email' => 'manager2@radio.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where(['radio_id' => $radio2->id, 'name' => 'directeur'])->value('id'),
            'radio_id' => $radio2->id,
            'phone_number' => '+21620000002',
            'bio' => 'Director of Radio Two',
        ]);

        $radio1->manager_id = $manager1->id;
        $radio1->save();
        $radio2->manager_id = $manager2->id;
        $radio2->save();

        // 8️⃣ Add other Users
        $user1 = User::create([
            'name' => 'Hichem Animateur',
            'email' => 'animateur@radio.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where(['radio_id' => $radio1->id, 'name' => 'animateur'])->value('id'),
            'radio_id' => $radio1->id,
            'phone_number' => '+21630000001',
            'bio' => 'Morning show host',
        ]);

        $user2 = User::create([
            'name' => 'Sami Journaliste',
            'email' => 'journaliste@radio.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where(['radio_id' => $radio2->id, 'name' => 'journaliste'])->value('id'),
            'radio_id' => $radio2->id,
            'phone_number' => '+21630000002',
            'bio' => 'Local news reporter',
        ]);

        $user3 = User::create([
            'name' => 'Fathi Tech',
            'email' => 'tech@radio.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where(['radio_id' => $radio1->id, 'name' => 'tech staff'])->value('id'),
            'radio_id' => $radio1->id,
            'phone_number' => '+21630000003',
            'bio' => 'Sound engineer',
        ]);

        // 9️⃣ Create Teams
        $team1 = Team::create(['name' => 'Morning Show Team', 'description' => 'Morning shows for Radio One', 'radio_id' => $radio1->id]);
        $team2 = Team::create(['name' => 'Tech Team', 'description' => 'Technical operations', 'radio_id' => $radio1->id]);
        $team3 = Team::create(['name' => 'News Team', 'description' => 'Local and international news', 'radio_id' => $radio2->id]);
        $team4 = Team::create(['name' => 'Sports Team', 'description' => 'Sports coverage team', 'radio_id' => $radio2->id]);
        $team5 = Team::create(['name' => 'Digital Team', 'description' => 'Digital & social media', 'radio_id' => $radio2->id]);

        // Attach team members
        $team1->users()->attach([$manager1->id, $user1->id]);
        $team2->users()->attach([$user3->id]);
        $team3->users()->attach([$manager2->id, $user2->id]);

        // 10️⃣ Create Tasks
        Task::create([
            'title' => 'Prepare Morning Script',
            'description' => 'Script for Monday morning show',
            'owner_id' => $manager1->id,
            'assigned_to' => $user1->id,
            'team_id' => $team1->id,
            'radio_id' => $radio1->id,
            'status' => 'todo',
            'deadline' => now()->addDay(),
        ]);

        Task::create([
            'title' => 'Check Equipment',
            'description' => 'Check microphones and mixers',
            'owner_id' => $manager1->id,
            'assigned_to' => $user3->id,
            'team_id' => $team2->id,
            'radio_id' => $radio1->id,
            'status' => 'pending',
            'deadline' => now()->addDays(2),
        ]);

        Task::create([
            'title' => 'Cover Local News',
            'description' => 'Report on city council',
            'owner_id' => $manager2->id,
            'assigned_to' => $user2->id,
            'team_id' => $team3->id,
            'radio_id' => $radio2->id,
            'status' => 'done',
            'deadline' => now()->subDay(),
        ]);
        // 11️⃣ Create Guests for Radio One
        $guestsRadio1 = [
            [
                'first_name' => 'Sofien',
                'last_name' => 'Ben Ali',
                'email' => 'sofien.guest1@radio.com',
                'phone_number' => '+21640000001',
                'address' => 'Tunis, Avenue Habib Bourguiba',
                'description' => 'Frequent music guest',
                'profile_photo' => 'default-profile.png'
            ],
            [
                'first_name' => 'Amina',
                'last_name' => 'Trabelsi',
                'email' => 'amina.guest2@radio.com',
                'phone_number' => '+21640000002',
                'address' => 'Tunis, Rue de la Liberté',
                'description' => 'Talk show guest',
                'profile_photo' => 'default-profile.png'
            ],
        ];

        foreach ($guestsRadio1 as $g) {
            Guest::create(array_merge($g, ['radio_id' => $radio1->id]));
        }

        // 12️⃣ Create Guests for Radio Two
        $guestsRadio2 = [
            [
                'first_name' => 'Khaled',
                'last_name' => 'Haddad',
                'email' => 'khaled.guest1@radio.com',
                'phone_number' => '+21650000001',
                'address' => 'Sfax, Boulevard 7 Novembre',
                'description' => 'Sports commentator',
                'profile_photo' => 'default-profile.png'
            ],
            [
                'first_name' => 'Lina',
                'last_name' => 'Mahmoud',
                'email' => 'lina.guest2@radio.com',
                'phone_number' => '+21650000002',
                'address' => 'Sfax, Avenue Habib Bourguiba',
                'description' => 'News segment guest',
                'profile_photo' => 'default-profile.png'
            ],
        ];

        foreach ($guestsRadio2 as $g) {
            Guest::create(array_merge($g, ['radio_id' => $radio2->id]));
        }

        $this->command->info('BOSS : Guests have been seeded for Radio One and Radio Two!');
        $this->command->info('BOSS :  Database seeded with radios, roles, users, teams, and tasks!');
    }
}
