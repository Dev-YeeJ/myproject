<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Medicine;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Template;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // USERS (Barangay Officials)
        // ==========================================

        User::updateOrCreate(
            ['username' => 'captain'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'role' => 'barangay_captain',
                'email' => 'captain@calbueg.gov.ph',
                'contact_number' => '09171234567',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'kagawad1'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'role' => 'kagawad',
                'email' => 'kagawad1@calbueg.gov.ph',
                'contact_number' => '09181234567',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'secretary'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Rosa',
                'last_name' => 'Mendoza',
                'role' => 'secretary',
                'email' => 'secretary@calbueg.gov.ph',
                'contact_number' => '09211234567',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'treasurer'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Carlos',
                'last_name' => 'Ramos',
                'role' => 'treasurer',
                'email' => 'treasurer@calbueg.gov.ph',
                'contact_number' => '09221234567',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'bhw1'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Elena',
                'last_name' => 'Cruz',
                'role' => 'health_worker',
                'email' => 'bhw1@calbueg.gov.ph',
                'contact_number' => '09231234567',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'tanod1'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Roberto',
                'last_name' => 'Flores',
                'role' => 'tanod',
                'email' => 'tanod1@calbueg.gov.ph',
                'contact_number' => '09251234567',
                'is_active' => true,
            ]
        );
        
        // --- THIS BLOCK IS NOW REMOVED ---
        // The ResidentObserver will create this user automatically
        /*
        User::updateOrCreate(
            ['username' => 'resident1'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Jaime',
                'last_name' => 'Yee',
                'role' => 'resident',
                'email' => 'resident1@calbueg.gov.ph',
                'contact_number' => '09155371154',
                'is_active' => true,
            ]
        );
        */

        // ==========================================
        // RESIDENT PROFILING DATA
        // ==========================================

        // --- Create Households ---
        $household1 = Household::updateOrCreate(
            ['household_number' => 'HH-001'],
            [
                'household_name' => 'Cruz Household',
                'address' => '123 Rizal St.',
                'purok' => 'Purok 1',
                'status' => 'incomplete' // Will be updated by resident seeder
            ]
        );

        $household2 = Household::updateOrCreate(
            ['household_number' => 'HH-002'],
            [
                'household_name' => 'Garcia Household',
                'address' => '456 Mabini St.',
                'purok' => 'Purok 2',
                'status' => 'incomplete'
            ]
        );

        $household3 = Household::updateOrCreate(
            ['household_number' => 'HH-003'],
            [
                'household_name' => 'Reyes Household',
                'address' => '789 Bonifacio St.',
                'purok' => 'Purok 3',
                'status' => 'incomplete'
            ]
        );
        
        $household4 = Household::updateOrCreate(
            ['household_number' => 'HH-004'],
            [
                'household_name' => 'Bautista Family',
                'address' => '101 Aguinaldo Ave.',
                'purok' => 'Purok 1',
                'status' => 'incomplete'
            ]
        );

        $household5 = Household::updateOrCreate(
            ['household_number' => 'HH-005'],
            [
                'household_name' => 'Soriano Residence',
                'address' => '202 Luna Blvd.',
                'purok' => 'Purok 2',
                'status' => 'incomplete'
            ]
        );

        $household6 = Household::updateOrCreate(
            ['household_number' => 'HH-006'],
            [
                'household_name' => 'Fernandez Household',
                'address' => '303 Del Pilar Cmpd.',
                'purok' => 'Purok 4',
                'status' => 'incomplete'
            ]
        );

        // --- Create Residents ---
        // (Using email as the unique identifier for updateOrCreate)

        // Household 1: Cruz Household
        $res1 = Resident::updateOrCreate(
            ['email' => 'mark.cruz@example.com'],
            [
                'first_name' => 'Mark', 'middle_name' => 'Santos', 'last_name' => 'Cruz', 'suffix' => '',
                'date_of_birth' => '1979-01-15', 'age' => 45, 'gender' => 'Male', 'civil_status' => 'Married',
                'household_id' => $household1->id, 'household_status' => 'Household Head',
                'address' => '123 Rizal St.', 'contact_number' => '09123456789',
                'occupation' => 'Teacher', 'monthly_income' => 25000,
                'is_registered_voter' => true, 'precinct_number' => '0021A',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );

        $res2 = Resident::updateOrCreate(
            ['email' => 'maria.cruz@example.com'],
            [
                'first_name' => 'Maria', 'middle_name' => 'Garcia', 'last_name' => 'Cruz', 'suffix' => '',
                'date_of_birth' => '1982-05-20', 'age' => 42, 'gender' => 'Female', 'civil_status' => 'Married',
                'household_id' => $household1->id, 'household_status' => 'Spouse',
                'address' => '123 Rizal St.', 'contact_number' => '09123456790',
                'occupation' => 'Nurse', 'monthly_income' => 30000,
                'is_registered_voter' => true, 'precinct_number' => '0021A',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );

        $res5 = Resident::updateOrCreate(
            ['email' => 'carlos.cruz@example.com'],
            [
                'first_name' => 'Carlos', 'middle_name' => 'Garcia', 'last_name' => 'Cruz', 'suffix' => '',
                'date_of_birth' => '2006-11-25', 'age' => 18, 'gender' => 'Male', 'civil_status' => 'Single',
                'household_id' => $household1->id, 'household_status' => 'Child',
                'address' => '123 Rizal St.', 'contact_number' => '09123456793',
                'occupation' => 'Student', 'monthly_income' => null,
                'is_registered_voter' => false, 'precinct_number' => null, // Just turned 18, not yet registered
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );

        $res6 = Resident::updateOrCreate(
            ['email' => 'sofia.cruz@example.com'],
            [
                'first_name' => 'Sofia', 'middle_name' => 'Garcia', 'last_name' => 'Cruz', 'suffix' => '',
                'date_of_birth' => '2010-07-18', 'age' => 14, 'gender' => 'Female', 'civil_status' => 'Single',
                'household_id' => $household1->id, 'household_status' => 'Child',
                'address' => '123 Rizal St.', 'contact_number' => '09123456794',
                'occupation' => 'Student', 'monthly_income' => null,
                'is_registered_voter' => false, 'precinct_number' => null,
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );

        // Household 2: Garcia Household (Senior PWD)
        $res3 = Resident::updateOrCreate(
            ['email' => 'pedro.garcia@example.com'],
            [
                'first_name' => 'Pedro', 'middle_name' => 'Ramos', 'last_name' => 'Garcia', 'suffix' => '',
                'date_of_birth' => '1957-08-10', 'age' => 67, 'gender' => 'Male', 'civil_status' => 'Widowed',
                'household_id' => $household2->id, 'household_status' => 'Household Head',
                'address' => '456 Mabini St.', 'contact_number' => '09123456791',
                'occupation' => 'Retired', 'monthly_income' => 8000,
                'is_registered_voter' => true, 'precinct_number' => '0030C',
                'is_pwd' => true, 'pwd_id_number' => 'PWD-12345', 'disability_type' => 'Physical - Mobility',
                'is_indigenous' => false, 'is_senior_citizen' => true, 'is_4ps' => false, 'is_active' => true,
            ]
        );

        // Household 3: Reyes Household (Single professional)
        $res4 = Resident::updateOrCreate(
            ['email' => 'ana.reyes@example.com'],
            [
                'first_name' => 'Ana', 'middle_name' => 'Villanueva', 'last_name' => 'Reyes', 'suffix' => '',
                'date_of_birth' => '1989-03-12', 'age' => 35, 'gender' => 'Female', 'civil_status' => 'Single',
                'household_id' => $household3->id, 'household_status' => 'Household Head',
                'address' => '789 Bonifacio St.', 'contact_number' => '09123456792',
                'occupation' => 'Entrepreneur', 'monthly_income' => 45000,
                'is_registered_voter' => true, 'precinct_number' => '0045A',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => false, 'is_active' => true,
            ]
        );

        // Household 4: Bautista Family
        Resident::updateOrCreate(
            ['email' => 'rodrigo.bautista@example.com'],
            [
                'first_name' => 'Rodrigo', 'middle_name' => 'Perez', 'last_name' => 'Bautista', 'suffix' => 'Sr.',
                'date_of_birth' => '1960-02-20', 'age' => 64, 'gender' => 'Male', 'civil_status' => 'Married',
                'household_id' => $household4->id, 'household_status' => 'Household Head',
                'address' => '101 Aguinaldo Ave.', 'contact_number' => '09181112222',
                'occupation' => 'Farmer', 'monthly_income' => 12000,
                'is_registered_voter' => true, 'precinct_number' => '0011B',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => true, 'is_senior_citizen' => true, 'is_4ps' => false, 'is_active' => true,
            ]
        );
        Resident::updateOrCreate(
            ['email' => 'linda.bautista@example.com'],
            [
                'first_name' => 'Linda', 'middle_name' => 'Magsaysay', 'last_name' => 'Bautista', 'suffix' => '',
                'date_of_birth' => '1963-04-15', 'age' => 61, 'gender' => 'Female', 'civil_status' => 'Married',
                'household_id' => $household4->id, 'household_status' => 'Spouse',
                'address' => '101 Aguinaldo Ave.', 'contact_number' => '09181112223',
                'occupation' => 'Housewife', 'monthly_income' => null,
                'is_registered_voter' => true, 'precinct_number' => '0011B',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => true, 'is_senior_citizen' => true, 'is_4ps' => false, 'is_active' => true,
            ]
        );

        // Household 5: Soriano Residence
        Resident::updateOrCreate(
            ['email' => 'emilio.soriano@example.com'],
            [
                'first_name' => 'Emilio', 'middle_name' => 'Del Pilar', 'last_name' => 'Soriano', 'suffix' => '',
                'date_of_birth' => '1995-09-01', 'age' => 29, 'gender' => 'Male', 'civil_status' => 'Single',
                'household_id' => $household5->id, 'household_status' => 'Household Head',
                'address' => '202 Luna Blvd.', 'contact_number' => '09193334444',
                'occupation' => 'Driver', 'monthly_income' => 18000,
                'is_registered_voter' => true, 'precinct_number' => '0022C',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => false, 'is_active' => true,
            ]
        );
        Resident::updateOrCreate(
            ['email' => 'josefina.soriano@example.com'],
            [
                'first_name' => 'Josefina', 'middle_name' => 'Del Pilar', 'last_name' => 'Soriano', 'suffix' => '',
                'date_of_birth' => '1998-12-10', 'age' => 25, 'gender' => 'Female', 'civil_status' => 'Single',
                'household_id' => $household5->id, 'household_status' => 'Member',
                'address' => '202 Luna Blvd.', 'contact_number' => '09193334445',
                'occupation' => 'Call Center Agent', 'monthly_income' => 22000,
                'is_registered_voter' => true, 'precinct_number' => '0022C',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => false, 'is_active' => true,
            ]
        );

        // Household 6: Fernandez Household (Single Mother, 4Ps)
        Resident::updateOrCreate(
            ['email' => 'gladys.fernandez@example.com'],
            [
                'first_name' => 'Gladys', 'middle_name' => 'Aquino', 'last_name' => 'Fernandez', 'suffix' => '',
                'date_of_birth' => '1992-06-05', 'age' => 32, 'gender' => 'Female', 'civil_status' => 'Single',
                'household_id' => $household6->id, 'household_status' => 'Household Head',
                'address' => '303 Del Pilar Cmpd.', 'contact_number' => '09175556666',
                'occupation' => 'Unemployed', 'monthly_income' => null,
                'is_registered_voter' => false, 'precinct_number' => null,
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );
        Resident::updateOrCreate(
            ['email' => 'prince.fernandez@example.com'],
            [
                'first_name' => 'Prince', 'middle_name' => 'Aquino', 'last_name' => 'Fernandez', 'suffix' => '',
                'date_of_birth' => '2015-01-20', 'age' => 9, 'gender' => 'Male', 'civil_status' => 'Single',
                'household_id' => $household6->id, 'household_status' => 'Child',
                'address' => '303 Del Pilar Cmpd.', 'contact_number' => null,
                'occupation' => 'Student', 'monthly_income' => null,
                'is_registered_voter' => false, 'precinct_number' => null,
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );
        Resident::updateOrCreate(
            ['email' => 'princess.fernandez@example.com'],
            [
                'first_name' => 'Princess', 'middle_name' => 'Aquino', 'last_name' => 'Fernandez', 'suffix' => '',
                'date_of_birth' => '2017-03-30', 'age' => 7, 'gender' => 'Female', 'civil_status' => 'Single',
                'household_id' => $household6->id, 'household_status' => 'Child',
                'address' => '303 Del Pilar Cmpd.', 'contact_number' => null,
                'occupation' => 'Student', 'monthly_income' => null,
                'is_registered_voter' => false, 'precinct_number' => null,
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => true, 'is_active' => true,
            ]
        );

        // --- NEW: Add the 'resident1' (Jaime Yee) as a Resident ---
        // The Observer will create his User account.
        $res_jaime = Resident::updateOrCreate(
            ['email' => 'resident1@calbueg.gov.ph'],
            [
                'first_name' => 'Jaime', 'middle_name' => '', 'last_name' => 'Yee', 'suffix' => '',
                'date_of_birth' => '1990-01-01', 'age' => 35, 'gender' => 'Male', 'civil_status' => 'Single',
                'household_id' => $household1->id, // Assigning to household 1
                'household_status' => 'Member',
                'address' => '123 Rizal St.', 'contact_number' => '09155371154',
                'occupation' => 'Technician', 'monthly_income' => 20000,
                'is_registered_voter' => true, 'precinct_number' => '0021B',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => false, 'is_active' => true,
            ]
        );


        // --- Recalculate Household Totals & Status ---
        // This is important to run AFTER all residents are seeded
        $allHouseholds = Household::all();
        foreach ($allHouseholds as $household) {
            $household->updateTotalMembers();
            $household->updateHouseholdStatus();
        }


        // ==========================================
        // HEALTH & SOCIAL SERVICES DATA (MEDICINES)
        // ==========================================
        
        Medicine::updateOrCreate(
            ['item_name' => 'Paracetamol', 'dosage' => '500mg'],
            [
                'brand_name' => 'Biogesic', 'quantity' => 50, 'category' => 'Pain Relief/Fever',
                'low_stock_threshold' => 10, 'expiration_date' => '2025-12-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Amoxicillin', 'dosage' => '250mg'],
            [
                'brand_name' => 'Generic', 'quantity' => 8, 'category' => 'Antibiotic',
                'low_stock_threshold' => 10, 'expiration_date' => '2025-06-30',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Loratadine', 'dosage' => '10mg'],
            [
                'brand_name' => 'Allerta', 'quantity' => 75, 'category' => 'Allergy',
                'low_stock_threshold' => 10, 'expiration_date' => '2026-01-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Salbutamol Nebule', 'dosage' => '2.5mg/mL'],
            [
                'brand_name' => 'Ventolin', 'quantity' => 20, 'category' => 'Asthma',
                'low_stock_threshold' => 5, 'expiration_date' => '2025-08-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Cough Syrup', 'dosage' => '120ml'],
            [
                'brand_name' => 'Robitussin', 'quantity' => 5, 'category' => 'Cold & Cough',
                'low_stock_threshold' => 5, 'expiration_date' => '2024-10-01', // Expired
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Multivitamins', 'dosage' => '1 strip'],
            [
                'brand_name' => 'Centrum', 'quantity' => 9, 'category' => 'Vitamins & Supplements',
                'low_stock_threshold' => 10, 'expiration_date' => '2025-11-30',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Mefenamic Acid', 'dosage' => '500mg'],
            [
                'brand_name' => 'Dolfenal', 'quantity' => 40, 'category' => 'Pain Relief/Fever',
                'low_stock_threshold' => 10, 'expiration_date' => '2026-05-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Ascorbic Acid (Vitamin C)', 'dosage' => '500mg'],
            [
                'brand_name' => 'Poten-Cee', 'quantity' => 120, 'category' => 'Vitamins & Supplements',
                'low_stock_threshold' => 20, 'expiration_date' => '2026-10-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Oral Rehydration Salts', 'dosage' => '1 Sachet'],
            [
                'brand_name' => 'Hydrite', 'quantity' => 30, 'category' => 'Digestive Health',
                'low_stock_threshold' => 10, 'expiration_date' => '2027-01-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Povidone-Iodine', 'dosage' => '120ml Bottle'],
            [
                'brand_name' => 'Betadine', 'quantity' => 7, 'category' => 'First Aid',
                'low_stock_threshold' => 5, 'expiration_date' => '2025-09-30',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Sterile Gauze Pads', 'dosage' => '10-pack'],
            [
                'brand_name' => 'Generic', 'quantity' => 15, 'category' => 'First Aid',
                'low_stock_threshold' => 5, 'expiration_date' => '2028-01-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Omeprazole', 'dosage' => '20mg'],
            [
                'brand_name' => 'Generic', 'quantity' => 25, 'category' => 'Digestive Health',
                'low_stock_threshold' => 10, 'expiration_date' => '2025-07-31',
            ]
        );
        Medicine::updateOrCreate(
            ['item_name' => 'Ibuprofen', 'dosage' => '200mg'],
            [
                'brand_name' => 'Advil', 'quantity' => 0, 'category' => 'Pain Relief/Fever',
                'low_stock_threshold' => 10, 'expiration_date' => '2025-04-30',
            ]
        );


        // ==========================================
        // DOCUMENT SERVICES DATA
        // ==========================================

        // --- 1. Seed Document Types ---
        $docType1 = DocumentType::updateOrCreate(
            ['name' => 'Barangay Clearance'],
            ['price' => 50.00, 'requires_payment' => true, 'is_active' => true]
        );
        $docType2 = DocumentType::updateOrCreate(['name' => 'Certificate of Residency']);
        $docType3 = DocumentType::updateOrCreate(['name' => 'Certificate of Indigency']);
        $docType4 = DocumentType::updateOrCreate(['name' => 'Business Permit']);
        $docType5 = DocumentType::updateOrCreate(['name' => 'Construction Permit']);

        // --- 2. Seed Templates ---
        Template::updateOrCreate(
            ['document_type_id' => $docType1->id],
            [
                'name' => 'Standard Barangay Clearance Template',
                'content' => 'This is to certify that [Resident Name] is a resident of...',
                'is_active' => true,
            ]
        );
        Template::updateOrCreate(
            ['document_type_id' => $docType2->id],
            [
                'name' => 'Standard Certificate of Residency',
                'content' => 'This certifies that [Resident Name] is a bonafide resident of...',
                'is_active' => true,
            ]
        );

        // --- 3. Seed Document Requests ---
        DocumentRequest::updateOrCreate(
            ['tracking_number' => 'BC-2024-001'],
            [
                'resident_id' => $res1->id, // Mark Cruz
                'document_type' => $docType1->id, // FIX: 'document_type'
                'purpose' => 'Employment Requirements',
                'price' => 50, 'priority' => 'Normal', 'payment_status' => 'Unpaid', 'status' => 'Pending',
                'created_at' => '2024-12-10 09:00:00',
            ]
        );
        DocumentRequest::updateOrCreate(
            ['tracking_number' => 'CR-2024-002'],
            [
                'resident_id' => $res2->id, // Maria Cruz
                'document_type' => $docType2->id, // FIX: 'document_type'
                'purpose' => 'Bank Account Opening',
                'price' => 50, 'priority' => 'Normal', 'payment_status' => 'Paid', 'status' => 'Ready for Pickup',
                'created_at' => '2024-12-09 11:30:00',
            ]
        );
        DocumentRequest::updateOrCreate(
            ['tracking_number' => 'CI-2024-003'],
            [
                'resident_id' => $res3->id, // Pedro Garcia
                'document_type' => $docType3->id, // FIX: 'document_type'
                'purpose' => 'Medical Assistance Application',
                'price' => 0, 'priority' => 'Urgent', 'payment_status' => 'Waived', 'status' => 'Processing',
                'created_at' => '2024-12-08 14:15:00',
            ]
        );
        DocumentRequest::updateOrCreate(
            ['tracking_number' => 'BP-2024-004'],
            [
                'resident_id' => $res4->id, // Ana Reyes
                'document_type' => $docType4->id, // FIX: 'document_type'
                'purpose' => 'Sari-sari Store Operation',
                'price' => 200, 'priority' => 'Normal', 'payment_status' => 'Paid', 'status' => 'Under Review',
                'created_at' => '2024-12-07 10:00:00',
            ]
        );
        DocumentRequest::updateOrCreate(
            ['tracking_number' => 'CP-2024-005'],
            [
                'resident_id' => $res5->id, // Carlos Cruz
                'document_type' => $docType5->id, // FIX: 'document_type'
                'purpose' => 'House Extension',
                'price' => 500, 'priority' => 'Normal', 'payment_status' => 'Paid', 'status' => 'Completed',
                'created_at' => '2024-12-01 16:45:00',
            ]
        );
    }
}