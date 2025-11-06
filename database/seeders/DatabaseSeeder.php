<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate as Illuminate;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Medicine;
use App\Models\DocumentRequest;
use App\Models\DocumentType; // <-- IMPORTED
use App\Models\Template; // <-- IMPORTED

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
            // 'household_number' => 'HH-001', // <-- REMOVED (auto-generated)
            'household_name' => 'Cruz Household', // <-- ADDED
            'address' => 'Block 1, Lot 5',
            'purok' => 'Purok 1',
            'total_members' => 3,
            'status' => 'complete'
        ]);

        $household2 = Household::create([
            // 'household_number' => 'HH-002', // <-- REMOVED (auto-generated)
            'household_name' => 'Garcia Household', // <-- ADDED
            'address' => 'Block 2, Lot 10',
            'purok' => 'Purok 2',
            'total_members' => 1,
            'status' => 'complete'
        ]);

        $household3 = Household::create([
            // 'household_number' => 'HH-003', // <-- REMOVED (auto-generated)
            'household_name' => 'Reyes Household', // <-- ADDED
            'address' => 'Block 3, Lot 15',
            'purok' => 'Purok 3',
            'total_members' => 1,
            'status' => 'incomplete'
        ]);

        // Create Residents matching your table
        $resident1 = Resident::create([
            'first_name' => 'Mark',
            'middle_name' => '',
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
            'is_4ps' => true, 
            'is_active' => true,
            'precinct_number' => '0021A', // <-- ADDED
            'pwd_id_number' => null, // <-- ADDED
            'disability_type' => null, // <-- ADDED
        ]);

        $resident2 = Resident::create([
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
            'is_4ps' => true, 
            'is_active' => true,
            'precinct_number' => '0021A', // <-- ADDED
            'pwd_id_number' => null, // <-- ADDED
            'disability_type' => null, // <-- ADDED
        ]);

        $resident3 = Resident::create([
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
            'is_pwd' => true, // <-- MODIFIED
            'is_senior_citizen' => true,
            'is_4ps' => false, 
            'is_active' => true,
            'precinct_number' => '0030C', // <-- ADDED
            'pwd_id_number' => 'PWD-12345', // <-- ADDED
            'disability_type' => 'Physical - Mobility', // <-- ADDED
        ]);

        $resident4 = Resident::create([
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
            'is_4ps' => false, 
            'is_active' => true,
            'precinct_number' => '0045A', // <-- ADDED
            'pwd_id_number' => null, // <-- ADDED
            'disability_type' => null, // <-- ADDED
        ]);

        $resident5 = Resident::create([
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
            'is_4ps' => false, 
            'is_active' => true,
            'precinct_number' => '0021A', // <-- ADDED
            'pwd_id_number' => null, // <-- ADDED
            'disability_type' => null, // <-- ADDED
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
            'is_4ps' => true, 
            'is_active' => true,
            'precinct_number' => null, // <-- ADDED
            'pwd_id_number' => null, // <-- ADDED
            'disability_type' => null, // <-- ADDED
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
        
        // ==========================================
        // HEALTH & SOCIAL SERVICES DATA (MEDICINES)
        // ==========================================
        Medicine::create([
            'item_name' => 'Paracetamol',
            'brand_name' => 'Biogesic',
            'dosage' => '500mg',
            'quantity' => 50,
            'low_stock_threshold' => 10,
            'expiration_date' => '2025-12-31',
        ]);

        Medicine::create([
            'item_name' => 'Amoxicillin',
            'brand_name' => 'Generic',
            'dosage' => '250mg',
            'quantity' => 8,
            'low_stock_threshold' => 10,
            'expiration_date' => '2025-06-30',
        ]);

        Medicine::create([
            'item_name' => 'Loratadine',
            'brand_name' => 'Allerta',
            'dosage' => '10mg',
            'quantity' => 75,
            'low_stock_threshold' => 10,
            'expiration_date' => '2026-01-31',
        ]);

        Medicine::create([
            'item_name' => 'Salbutamol Nebule',
            'brand_name' => 'Ventolin',
            'dosage' => '2.5mg/mL',
            'quantity' => 20,
            'low_stock_threshold' => 5,
            'expiration_date' => '2025-08-31',
        ]);

        Medicine::create([
            'item_name' => 'Cough Syrup',
            'brand_name' => 'Robitussin',
            'dosage' => '120ml',
            'quantity' => 5,
            'low_stock_threshold' => 5,
            'expiration_date' => '2024-10-01', // This will show as 'Expired'
        ]);

        Medicine::create([
            'item_name' => 'Multivitamins',
            'brand_name' => 'Centrum',
            'dosage' => '1 strip',
            'quantity' => 9,
            'low_stock_threshold' => 10,
            'expiration_date' => '2025-11-30',
        ]);

        // ==========================================
        // DOCUMENT SERVICES DATA (NEWLY ADDED)
        // ==========================================

        // --- 1. Seed Document Types ---
        $docType1 = DocumentType::create([
            'name' => 'Barangay Clearance',
            'price' => 50.00,
            'requires_payment' => true,
            'is_active' => true,
        ]);
        
        $docType2 = DocumentType::create([
            'name' => 'Certificate of Residency',
        ]);

        $docType3 = DocumentType::create([
            'name' => 'Certificate of Indigency',
        ]);

        $docType4 = DocumentType::create([
            'name' => 'Business Permit',
        ]);

        $docType5 = DocumentType::create([
            'name' => 'Construction Permit',
        ]);

        // --- 2. Seed Templates ---
        Template::create([
            'document_type_id' => $docType1->id,
            'name' => 'Standard Barangay Clearance Template',
            'content' => 'This is to certify that [Resident Name] is a resident of...',
            'is_active' => true,
        ]);

        Template::create([
            'document_type_id' => $docType2->id,
        ]);

        // --- 3. Seed Document Requests (using the IDs from above) ---
        DocumentRequest::create([
            'resident_id' => $resident1->id, // Mark Cruz
            'document_type_id' => $docType1->id, // Barangay Clearance
            'tracking_number' => 'BC-2024-001',
            // 'document_type' => 'Barangay Clearance', // <-- REMOVED (use ID)
            'purpose' => 'Employment Requirements',
            'price' => 50,
            'priority' => 'Normal',
            'payment_status' => 'Unpaid',
            'status' => 'Pending',
            'created_at' => '2024-12-10 09:00:00',
        ]);

        DocumentRequest::create([
            'resident_id' => $resident2->id, // Maria Santos
            'document_type_id' => $docType2->id, // Certificate of Residency
            'tracking_number' => 'CR-2024-002',
            // 'document_type' => 'Certificate of Residency', // <-- REMOVED (use ID)
            'purpose' => 'Bank Account Opening',
            'price' => 50,
            'priority' => 'Normal',
            'payment_status' => 'Paid',
            'status' => 'Ready for Pickup',
            'created_at' => '2024-12-09 11:30:00',
        ]);

        DocumentRequest::create([
            'resident_id' => $resident3->id, // Pedro Garcia
            'document_type_id' => $docType3->id, // Certificate of Indigency
            'tracking_number' => 'CT-2024-003',
            // 'document_type' => 'Certificate of Indigency', // <-- REMOVED (use ID)
            'purpose' => 'Medical Assistance Application',
            'price' => 0,
            'priority' => 'Urgent',
            'payment_status' => 'Waived',
            'status' => 'Processing',
            'created_at' => '2024-12-08 14:15:00',
        ]);

        DocumentRequest::create([
            'resident_id' => $resident4->id, // Ana Reyes
            'document_type_id' => $docType4->id, // Business Permit
            'tracking_number' => 'BP-2024-004',
            // 'document_type' => 'Business Permit', // <-- REMOVED (use ID)
            'purpose' => 'Sari-sari Store Operation',
            'price' => 200,
            'priority' => 'Normal',
            'payment_status' => 'Paid',
            'status' => 'Under Review',
            'created_at' => '2024-12-07 10:00:00',
        ]);

        DocumentRequest::create([
            'resident_id' => $resident5->id, // Carlos Mendoza
            'document_type_id' => $docType5->id, // Construction Permit
            'tracking_number' => 'CP-2024-005',
            // 'document_type' => 'Construction Permit', // <-- REMOVED (use ID)
            'purpose' => 'House Extension',
            'price' => 500,
            'priority' => 'Normal',
            'payment_status' => 'Paid',
            'status' => 'Completed',
            'created_at' => '2024-12-01 16:45:00',
        ]);
    }
}