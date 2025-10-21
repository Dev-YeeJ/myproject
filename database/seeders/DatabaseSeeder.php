<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Barangay Captain
        User::create([
            'username' => 'captain',
            'password' => Hash::make('password123'),
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'role' => 'barangay_captain',
            'email' => 'captain@calbueg.gov.ph',
            'contact_number' => '09171234567',
            'is_active' => true,
        ]);

        // Create Kagawad 1
        User::create([
            'username' => 'kagawad1',
            'password' => Hash::make('password123'),
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'role' => 'kagawad',
            'email' => 'kagawad1@calbueg.gov.ph',
            'contact_number' => '09181234567',
            'is_active' => true,
        ]);

        // Create Kagawad 2
        User::create([
            'username' => 'kagawad2',
            'password' => Hash::make('password123'),
            'first_name' => 'Pedro',
            'last_name' => 'Reyes',
            'role' => 'kagawad',
            'email' => 'kagawad2@calbueg.gov.ph',
            'contact_number' => '09191234567',
            'is_active' => true,
        ]);

        // Create SK Official
        User::create([
            'username' => 'sk_chair',
            'password' => Hash::make('password123'),
            'first_name' => 'Anna',
            'last_name' => 'Garcia',
            'role' => 'sk_official',
            'email' => 'sk@calbueg.gov.ph',
            'contact_number' => '09201234567',
            'is_active' => true,
        ]);

        // Create Secretary
        User::create([
            'username' => 'secretary',
            'password' => Hash::make('password123'),
            'first_name' => 'Rosa',
            'last_name' => 'Mendoza',
            'role' => 'secretary',
            'email' => 'secretary@calbueg.gov.ph',
            'contact_number' => '09211234567',
            'is_active' => true,
        ]);

        // Create Treasurer
        User::create([
            'username' => 'treasurer',
            'password' => Hash::make('password123'),
            'first_name' => 'Carlos',
            'last_name' => 'Ramos',
            'role' => 'treasurer',
            'email' => 'treasurer@calbueg.gov.ph',
            'contact_number' => '09221234567',
            'is_active' => true,
        ]);

        // Create Health Worker 1
        User::create([
            'username' => 'bhw1',
            'password' => Hash::make('password123'),
            'first_name' => 'Elena',
            'last_name' => 'Cruz',
            'role' => 'health_worker',
            'email' => 'bhw1@calbueg.gov.ph',
            'contact_number' => '09231234567',
            'is_active' => true,
        ]);

        // Create Health Worker 2
        User::create([
            'username' => 'bhw2',
            'password' => Hash::make('password123'),
            'first_name' => 'Linda',
            'last_name' => 'Torres',
            'role' => 'health_worker',
            'email' => 'bhw2@calbueg.gov.ph',
            'contact_number' => '09241234567',
            'is_active' => true,
        ]);

        // Create Tanod 1
        User::create([
            'username' => 'tanod1',
            'password' => Hash::make('password123'),
            'first_name' => 'Roberto',
            'last_name' => 'Flores',
            'role' => 'tanod',
            'email' => 'tanod1@calbueg.gov.ph',
            'contact_number' => '09251234567',
            'is_active' => true,
        ]);

        // Create Tanod 2
        User::create([
            'username' => 'tanod2',
            'password' => Hash::make('password123'),
            'first_name' => 'Manuel',
            'last_name' => 'Domingo',
            'role' => 'tanod',
            'email' => 'tanod2@calbueg.gov.ph',
            'contact_number' => '09261234567',
            'is_active' => true,
        ]);

        // ==========================================
        // RESIDENT PROFILING DATA
        // ==========================================

        // Create Households first
        $household1 = Household::create([
            'household_number' => 'HH-001',
            'address' => 'Block 1, Lot 5',
            'purok' => 'Purok 1',
            'total_members' => 3,
            'status' => 'complete'
        ]);

        $household2 = Household::create([
            'household_number' => 'HH-002',
            'address' => 'Block 2, Lot 10',
            'purok' => 'Purok 2',
            'total_members' => 1,
            'status' => 'complete'
        ]);

        $household3 = Household::create([
            'household_number' => 'HH-003',
            'address' => 'Block 3, Lot 15',
            'purok' => 'Purok 3',
            'total_members' => 1,
            'status' => 'incomplete'
        ]);

        // Create Residents matching your table
        Resident::create([
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'suffix' => 'Santos',
            'date_of_birth' => '1979-01-15',
            'age' => 45,
            'gender' => 'Male',
            'civil_status' => 'Married',
            'household_id' => $household1->id,
            'household_status' => 'Household Head',
            'address' => 'Block 1, Lot 5',
            'contact_number' => '09123456789',
            'email' => 'juan.santos@example.com',
            'occupation' => 'Teacher',
            'monthly_income' => 25000,
            'is_registered_voter' => true,
            'is_indigenous' => false,
            'is_pwd' => false,
            'is_senior_citizen' => false,
            'is_active' => true
        ]);

        Resident::create([
            'first_name' => 'Maria',
            'middle_name' => '',
            'last_name' => 'Santos',
            'suffix' => 'Garcia',
            'date_of_birth' => '1982-05-20',
            'age' => 42,
            'gender' => 'Female',
            'civil_status' => 'Married',
            'household_id' => $household1->id,
            'household_status' => 'Spouse',
            'address' => 'Block 1, Lot 5',
            'contact_number' => '09123456790',
            'email' => 'maria.garcia@example.com',
            'occupation' => 'Nurse',
            'monthly_income' => 30000,
            'is_registered_voter' => true,
            'is_indigenous' => false,
            'is_pwd' => false,
            'is_senior_citizen' => false,
            'is_active' => true
        ]);

        Resident::create([
            'first_name' => 'Pedro',
            'middle_name' => '',
            'last_name' => 'Garcia',
            'suffix' => '',
            'date_of_birth' => '1957-08-10',
            'age' => 67,
            'gender' => 'Male',
            'civil_status' => 'Widowed',
            'household_id' => $household2->id,
            'household_status' => 'Household Head',
            'address' => 'Block 2, Lot 10',
            'contact_number' => '09123456791',
            'email' => 'pedro.garcia@example.com',
            'occupation' => 'Retired',
            'monthly_income' => 8000,
            'is_registered_voter' => true,
            'is_indigenous' => false,
            'is_pwd' => false,
            'is_senior_citizen' => true,
            'is_active' => true
        ]);

        Resident::create([
            'first_name' => 'Ana',
            'middle_name' => '',
            'last_name' => 'Reyes',
            'suffix' => 'Villanueva',
            'date_of_birth' => '1989-03-12',
            'age' => 35,
            'gender' => 'Female',
            'civil_status' => 'Single',
            'household_id' => $household3->id,
            'household_status' => 'Household Head',
            'address' => 'Block 3, Lot 15',
            'contact_number' => '09123456792',
            'email' => 'ana.villanueva@example.com',
            'occupation' => 'Entrepreneur',
            'monthly_income' => 45000,
            'is_registered_voter' => true,
            'is_indigenous' => false,
            'is_pwd' => false,
            'is_senior_citizen' => false,
            'is_active' => true
        ]);

        Resident::create([
            'first_name' => 'Carlos',
            'middle_name' => '',
            'last_name' => 'Mendoza',
            'suffix' => '',
            'date_of_birth' => '1996-11-25',
            'age' => 28,
            'gender' => 'Male',
            'civil_status' => 'Single',
            'household_id' => $household1->id,
            'household_status' => 'Child',
            'address' => 'Block 1, Lot 5',
            'contact_number' => '09123456793',
            'email' => 'carlos.mendoza@example.com',
            'occupation' => 'IT Specialist',
            'monthly_income' => 35000,
            'is_registered_voter' => true,
            'is_indigenous' => false,
            'is_pwd' => false,
            'is_senior_citizen' => false,
            'is_active' => true
        ]);

        Resident::create([
            'first_name' => 'Sofia',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'suffix' => '',
            'date_of_birth' => '2008-07-18',
            'age' => 16,
            'gender' => 'Female',
            'civil_status' => 'Single',
            'household_id' => $household1->id,
            'household_status' => 'Child',
            'address' => 'Block 1, Lot 5',
            'contact_number' => '09123456794',
            'email' => 'sofia.delacruz@example.com',
            'occupation' => 'Student',
            'monthly_income' => 0,
            'is_registered_voter' => false,
            'is_indigenous' => false,
            'is_pwd' => false,
            'is_senior_citizen' => false,
            'is_active' => true
        ]);

        // Create Sample Residents (keeping original ones with resident role)
        User::create([
            'username' => 'resident1',
            'password' => Hash::make('password123'),
            'first_name' => 'Jose',
            'last_name' => 'Bautista',
            'role' => 'resident',
            'email' => 'jose.bautista@example.com',
            'contact_number' => '09271234567',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'resident2',
            'password' => Hash::make('password123'),
            'first_name' => 'Carmela',
            'last_name' => 'Villanueva',
            'role' => 'resident',
            'email' => 'carmela.villanueva@example.com',
            'contact_number' => '09281234567',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'resident3',
            'password' => Hash::make('password123'),
            'first_name' => 'Ricardo',
            'last_name' => 'Aquino',
            'role' => 'resident',
            'email' => 'ricardo.aquino@example.com',
            'contact_number' => '09291234567',
            'is_active' => true,
        ]);
    }
}