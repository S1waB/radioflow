<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'System Administrator', 'hierarchy_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'directeur', 'description' => 'Radio Director', 'hierarchy_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Editor-in-Chief', 'description' => 'Editor in Chief', 'hierarchy_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'description' => 'Department Manager', 'hierarchy_level' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'journaliste', 'description' => 'Journalist', 'hierarchy_level' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'animateur', 'description' => 'Radio Host', 'hierarchy_level' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'chroniqueur', 'description' => 'Columnist', 'hierarchy_level' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tech staff', 'description' => 'Technical Staff', 'hierarchy_level' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Digital', 'description' => 'Digital Team', 'hierarchy_level' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Production', 'description' => 'Production Team', 'hierarchy_level' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create Admin User (no radio)
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@radioflow.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'admin')->value('id'),
            'phone_number' => '+216 50 111 222',
            'address' => 'Admin Headquarters, Tunis',
            'bio' => 'System administrator with full access rights',
        ]);

        // Create 2 Manager Users FIRST (so we can reference them in radio)
        $manager1 = User::create([
            'name' => 'Ahmed Radio1 Manager',
            'email' => 'manager1@radio.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'Manager')->value('id'),
            'phone_number' => '+216 20 123 456',
            'address' => 'Radio FM Plus Studio, Tunis',
            'bio' => 'Manager of Radio FM Plus with 10 years experience',
        ]);

        $manager2 = User::create([
            'name' => 'Fatma Radio2 Manager',
            'email' => 'manager2@radio.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'Manager')->value('id'),
            'phone_number' => '+216 99 888 777',
            'address' => 'Radio Jeunesse HQ, Sfax',
            'bio' => 'Young and dynamic manager leading Radio Jeunesse',
        ]);

        // Create Radios using manager_id
        $radio1Id = DB::table('radios')->insertGetId([
            'name' => 'Radio FM Plus',
            'description' => 'The leading music and news station in the region',
            'phone_number' => '+216 70 123 456',
            'address' => '123 Media Street, Tunis, Tunisia',
            'Country' => 'Tunisia',
            'status' => 'active',
            'manager_id' => $manager1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $radio2Id = DB::table('radios')->insertGetId([
            'name' => 'Radio Jeunesse',
            'description' => 'Youth-focused radio station with modern programming',
            'phone_number' => '+216 98 765 432',
            'address' => '456 Youth Avenue, Sousse, Tunisia',
            'Country' => 'Tunisia',
            'status' => 'active',
            'manager_id' => $manager2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Now update managers to link to their radio_id (if needed)
        $manager1->update(['radio_id' => $radio1Id]);
        $manager2->update(['radio_id' => $radio2Id]);

        // Add team members for Radio 1
        User::create([
            'name' => 'Leila Radio1 Animateur',
            'email' => 'animateur1@radio.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'animateur')->value('id'),
            'radio_id' => $radio1Id,
            'phone_number' => '+216 22 333 444',
            'address' => 'Sousse, Tunisia',
            'bio' => 'Radio host specializing in morning shows',
        ]);

        User::create([
            'name' => 'Samir Radio1 Tech',
            'email' => 'tech1@radio.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'tech staff')->value('id'),
            'radio_id' => $radio1Id,
            'phone_number' => '+216 55 666 777',
            'bio' => 'Technical engineer maintaining broadcast equipment',
        ]);

        // Add team members for Radio 2
        User::create([
            'name' => 'Youssef Radio2 Journaliste',
            'email' => 'journaliste2@radio.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'journaliste')->value('id'),
            'radio_id' => $radio2Id,
            'phone_number' => '+216 23 456 789',
            'bio' => 'Investigative journalist covering youth culture',
        ]);

        User::create([
            'name' => 'Nadia Radio2 Digital',
            'email' => 'digital2@radio.com',
            'password' => Hash::make('password'),
            'role_id' => DB::table('roles')->where('name', 'Digital')->value('id'),
            'radio_id' => $radio2Id,
            'phone_number' => '+216 29 876 543',
            'address' => 'Ariana, Tunisia',
            'bio' => 'Digital content creator and social media manager',
        ]);

        
        // Create sample radio demands
        DB::table('radio_demands')->insert([
            [
                'radio_name' => 'Radio Culture FM',
                'description' => 'New cultural radio station focusing on arts and heritage',
                'founding_date' => '2023-06-15',
                'manager_name' => 'Mohamed Ben Ali',
                'manager_email' => 'mohamed.culturefm@example.com',
                'manager_phone' => '+216 50 123 456',
                'status' => 'pending',
                'team_members' => json_encode([
                    [
                        'name' => 'Amina Belhaj',
                        'email' => 'amina.culture@example.com',
                        'phone' => '+216 22 111 222',
                        'role' => 'animateur'
                    ],
                    [
                        'name' => 'Karim Trabelsi',
                        'email' => 'karim.culture@example.com',
                        'phone' => '+216 98 765 432',
                        'role' => 'journaliste'
                    ],
                    [
                        'name' => 'Salma Abid',
                        'email' => 'salma.culture@example.com',
                        'phone' => '+216 55 444 333',
                        'role' => 'chroniqueur'
                    ],
                    [
                        'name' => 'Youssef Hammami',
                        'email' => 'youssef.culture@example.com',
                        'phone' => '+216 29 888 777',
                        'role' => 'tech staff'
                    ],
                    [
                        'name' => 'Houda Ferchichi',
                        'email' => 'houda.culture@example.com',
                        'phone' => '+216 23 456 789',
                        'role' => 'Digital'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'radio_name' => 'Radio Sport Plus',
                'description' => '24/7 sports coverage and live commentary',
                'founding_date' => '2023-07-01',
                'manager_name' => 'Ali Ben Amor',
                'manager_email' => 'ali.sportplus@example.com',
                'manager_phone' => '+216 98 123 456',
                'status' => 'in_process',
                'team_members' => json_encode([
                    [
                        'name' => 'Samir Gharbi',
                        'email' => 'samir.sport@example.com',
                        'phone' => '+216 20 111 222',
                        'role' => 'animateur'
                    ],
                    [
                        'name' => 'Fatma Ben Youssef',
                        'email' => 'fatma.sport@example.com',
                        'phone' => '+216 50 765 432',
                        'role' => 'journaliste'
                    ],
                    [
                        'name' => 'Houssem Trabelsi',
                        'email' => 'houssem.sport@example.com',
                        'phone' => '+216 22 444 333',
                        'role' => 'chroniqueur'
                    ],
                    [
                        'name' => 'Rami Ben Salah',
                        'email' => 'rami.sport@example.com',
                        'phone' => '+216 99 888 777',
                        'role' => 'tech staff'
                    ],
                    [
                        'name' => 'Nadia Khemiri',
                        'email' => 'nadia.sport@example.com',
                        'phone' => '+216 23 456 789',
                        'role' => 'Digital'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'radio_name' => 'Radio Business TN',
                'description' => 'Financial news and business analysis',
                'founding_date' => '2023-08-10',
                'manager_name' => 'Salma Ben Ahmed',
                'manager_email' => 'salma.businesstn@example.com',
                'manager_phone' => '+216 55 123 456',
                'status' => 'approved',
                'team_members' => json_encode([
                    [
                        'name' => 'Walid Ben Hassen',
                        'email' => 'walid.business@example.com',
                        'phone' => '+216 29 111 222',
                        'role' => 'animateur'
                    ],
                    [
                        'name' => 'Amira Ben Youssef',
                        'email' => 'amira.business@example.com',
                        'phone' => '+216 98 765 432',
                        'role' => 'journaliste'
                    ],
                    [
                        'name' => 'Karim Ben Amor',
                        'email' => 'karim.business@example.com',
                        'phone' => '+216 22 444 333',
                        'role' => 'chroniqueur'
                    ],
                    [
                        'name' => 'Yassine Hammouda',
                        'email' => 'yassine.business@example.com',
                        'phone' => '+216 50 888 777',
                        'role' => 'tech staff'
                    ],
                    [
                        'name' => 'Leila Ben Abdallah',
                        'email' => 'leila.business@example.com',
                        'phone' => '+216 23 456 789',
                        'role' => 'Digital'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'radio_name' => 'Radio Santé',
                'description' => 'Health and wellness radio station',
                'founding_date' => '2023-09-05',
                'manager_name' => 'Dr. Hatem Ben Salah',
                'manager_email' => 'hatem.radiohealth@example.com',
                'manager_phone' => '+216 70 123 456',
                'status' => 'rejected',
                'team_members' => json_encode([
                    [
                        'name' => 'Dr. Amina Ben Ali',
                        'email' => 'amina.health@example.com',
                        'phone' => '+216 22 111 222',
                        'role' => 'animateur'
                    ],
                    [
                        'name' => 'Dr. Samir Trabelsi',
                        'email' => 'samir.health@example.com',
                        'phone' => '+216 98 765 432',
                        'role' => 'journaliste'
                    ],
                    [
                        'name' => 'Dr. Fatma Ben Amor',
                        'email' => 'fatma.health@example.com',
                        'phone' => '+216 55 444 333',
                        'role' => 'chroniqueur'
                    ],
                    [
                        'name' => 'Youssef Ben Youssef',
                        'email' => 'youssef.health@example.com',
                        'phone' => '+216 29 888 777',
                        'role' => 'tech staff'
                    ],
                    [
                        'name' => 'Nadia Ben Hassen',
                        'email' => 'nadia.health@example.com',
                        'phone' => '+216 23 456 789',
                        'role' => 'Digital'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
