<?php

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
use Illuminate\Support\Facades\DB;

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

        // ==========================================
        // 7 KAGAWAD MEMBERS
        // ==========================================
        
        $kagawads = [
            ['id' => 1, 'first' => 'Maria', 'last' => 'Santos'],
            ['id' => 2, 'first' => 'Pedro', 'last' => 'Reyes'],
            ['id' => 3, 'first' => 'Lita', 'last' => 'Bautista'],
            ['id' => 4, 'first' => 'Ramon', 'last' => 'Garcia'],
            ['id' => 5, 'first' => 'Teresa', 'last' => 'Ocampo'],
            ['id' => 6, 'first' => 'Jose', 'last' => 'Manalo'],
            ['id' => 7, 'first' => 'Anita', 'last' => 'Mercado'],
        ];

        foreach ($kagawads as $kagawad) {
            User::updateOrCreate(
                ['username' => 'kagawad' . $kagawad['id']], // Checks for username: kagawad1, kagawad2...
                [
                    'password' => Hash::make('password123'),
                    'first_name' => $kagawad['first'],
                    'last_name' => $kagawad['last'],
                    'role' => 'kagawad',
                    'email' => 'kagawad' . $kagawad['id'] . '@calbueg.gov.ph',
                    'contact_number' => '0918123456' . $kagawad['id'], // Generates unique number
                    'is_active' => true,
                ]
            );
        }

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
                'status' => 'incomplete' 
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
                'is_registered_voter' => false, 'precinct_number' => null,
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

        // Jaime Yee
        $res_jaime = Resident::updateOrCreate(
            ['email' => 'resident1@calbueg.gov.ph'],
            [
                'first_name' => 'Jaime', 'middle_name' => '', 'last_name' => 'Yee', 'suffix' => '',
                'date_of_birth' => '1990-01-01', 'age' => 35, 'gender' => 'Male', 'civil_status' => 'Single',
                'household_id' => $household1->id, 
                'household_status' => 'Member',
                'address' => '123 Rizal St.', 'contact_number' => '09155371154',
                'occupation' => 'Technician', 'monthly_income' => 20000,
                'is_registered_voter' => true, 'precinct_number' => '0021B',
                'is_pwd' => false, 'pwd_id_number' => null, 'disability_type' => null,
                'is_indigenous' => false, 'is_senior_citizen' => false, 'is_4ps' => false, 'is_active' => true,
            ]
        );


        // --- Recalculate Household Totals & Status ---
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

        // 1. Barangay Business Clearance (Business Permit)
        // Focuses on business details, not personal resident details.
        $businessPermit = DocumentType::updateOrCreate(
            ['name' => 'Barangay Business Clearance'],
            [
                'description' => 'Clearance required for new or renewing business operations.',
                'price' => 500.00,
                'requires_payment' => true,
                'is_active' => true,
                'custom_fields' => [
                    [
                        'name' => 'transaction_type',
                        'label' => 'Transaction Type',
                        'type' => 'select',
                        'options' => ['New Application', 'Renewal', 'Closure'],
                        'required' => true
                    ],
                    [
                        'name' => 'business_name',
                        'label' => 'Registered Business Name',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'business_nature',
                        'label' => 'Line of Business (e.g., Sari-sari Store, Computer Shop)',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'dti_sec_no',
                        'label' => 'DTI / SEC Registration Number',
                        'type' => 'text',
                        'required' => false // Optional for some small businesses
                    ],
                    [
                        'name' => 'business_address',
                        'label' => 'Business Location/Address',
                        'type' => 'textarea', // Might differ from resident's home address
                        'required' => true
                    ],
                    [
                        'name' => 'gross_sales',
                        'label' => 'Gross Sales (Last Year) - For Assessment',
                        'type' => 'number',
                        'required' => false
                    ]
                ]
            ]
        );

        // 2. Barangay Clearance (General Purpose)
        // Usually requires a Cedula (Community Tax Certificate).
        $brgyClearance = DocumentType::updateOrCreate(
            ['name' => 'Barangay Clearance (General)'],
            [
                'description' => 'General purpose clearance for ID, employment, or postal ID.',
                'price' => 100.00,
                'requires_payment' => true,
                'is_active' => true,
                'custom_fields' => [
                    [
                        'name' => 'cedula_no',
                        'label' => 'Community Tax Cert. (Cedula) No.',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'cedula_date',
                        'label' => 'Date Issued (Cedula)',
                        'type' => 'date',
                        'required' => true
                    ],
                    [
                        'name' => 'cedula_place',
                        'label' => 'Place Issued (Cedula)',
                        'type' => 'text',
                        'required' => true
                    ]
                ]
            ]
        );

        // 3. Certificate of Indigency
        // Focuses on the *need* and the *agency* requiring it.
        $indigency = DocumentType::updateOrCreate(
            ['name' => 'Certificate of Indigency'],
            [
                'description' => 'Proof of low-income status for financial, medical, or legal assistance.',
                'price' => 0.00, // Usually free
                'requires_payment' => false,
                'is_active' => true,
                'custom_fields' => [
                    [
                        'name' => 'requesting_agency',
                        'label' => 'Requesting Agency (e.g., DSWD, PAO, PCSO, School)',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'assistance_type',
                        'label' => 'Type of Assistance Needed',
                        'type' => 'select',
                        'options' => ['Financial Assistance', 'Medical Assistance', 'Educational Assistance', 'Burial Assistance', 'Legal Assistance'],
                        'required' => true
                    ],
                    [
                        'name' => 'beneficiary_name',
                        'label' => 'Name of Patient/Beneficiary (If different from Requestor)',
                        'type' => 'text',
                        'required' => false
                    ],
                    [
                        'name' => 'relation_to_beneficiary',
                        'label' => 'Relation to Beneficiary',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ]
        );

        // 4. Certificate of Residency
        // Confirms how long they have lived there. Profile address is already known.
        $residency = DocumentType::updateOrCreate(
            ['name' => 'Certificate of Residency'],
            [
                'description' => 'Official proof of residence within the barangay.',
                'price' => 50.00,
                'requires_payment' => true,
                'is_active' => true,
                'custom_fields' => [
                    [
                        'name' => 'years_of_residency',
                        'label' => 'Number of Years Residing in Barangay',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'previous_address',
                        'label' => 'Previous Address (If residing less than 1 year)',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ]
        );

        // 5. Barangay Construction Permit
        // Needs technical details about the structure.
        $construction = DocumentType::updateOrCreate(
            ['name' => 'Barangay Construction Clearance'],
            [
                'description' => 'Clearance for building, fencing, or renovation activities.',
                'price' => 1000.00, // Often higher
                'requires_payment' => true,
                'is_active' => true,
                'custom_fields' => [
                    [
                        'name' => 'scope_of_work',
                        'label' => 'Scope of Work',
                        'type' => 'select',
                        'options' => ['New Construction', 'Renovation/Extension', 'Fencing', 'Demolition', 'Electrical Installation'],
                        'required' => true
                    ],
                    [
                        'name' => 'project_location',
                        'label' => 'Exact Project Location/Address',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'floor_area',
                        'label' => 'Total Floor Area (sqm)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'estimated_cost',
                        'label' => 'Estimated Project Cost (PHP)',
                        'type' => 'number',
                        'required' => true
                    ],
                    [
                        'name' => 'lot_owner',
                        'label' => 'Name of Lot Owner (If different from Applicant)',
                        'type' => 'text',
                        'required' => false
                    ]
                ]
            ]
        );

        // --- 3. Seed Templates ---
        // Only if the document type exists
        if ($brgyClearance) {
            Template::updateOrCreate(
                ['document_type_id' => $brgyClearance->id],
                [
                    'name' => 'Standard Barangay Clearance Template',
                    'content' => 'This is to certify that [Resident Name] is a resident of...',
                    'is_active' => true,
                ]
            );
        }
        
        if ($residency) {
            Template::updateOrCreate(
                ['document_type_id' => $residency->id],
                [
                    'name' => 'Standard Certificate of Residency',
                    'content' => 'This certifies that [Resident Name] is a bonafide resident of...',
                    'is_active' => true,
                ]
            );
        }

        // --- 4. Seed Document Requests ---
        // Only if the document type exists
        if ($brgyClearance) {
            DocumentRequest::updateOrCreate(
                ['tracking_number' => 'BC-2024-001'],
                [
                    'resident_id' => $res1->id, // Mark Cruz
                    'document_type' => $brgyClearance->id,
                    'purpose' => 'Employment Requirements',
                    'price' => $brgyClearance->price,
                    'priority' => 'Normal',
                    'payment_status' => 'Unpaid',
                    'status' => 'Pending',
                    'created_at' => '2024-12-10 09:00:00',
                    'custom_data' => [
                        'cedula_no' => '12345678',
                        'cedula_date' => '2024-01-10',
                        'cedula_place' => 'Malasiqui',
                        'purpose_of_request' => 'Employment'
                    ]
                ]
            );
        }

        if ($residency) {
            DocumentRequest::updateOrCreate(
                ['tracking_number' => 'CR-2024-002'],
                [
                    'resident_id' => $res2->id, // Maria Cruz
                    'document_type' => $residency->id,
                    'purpose' => 'Bank Account Opening',
                    'price' => $residency->price,
                    'priority' => 'Normal',
                    'payment_status' => 'Paid',
                    'status' => 'Ready for Pickup',
                    'created_at' => '2024-12-09 11:30:00',
                    'custom_data' => [
                        'years_of_residency' => 5
                    ]
                ]
            );
        }

        if ($indigency) {
            DocumentRequest::updateOrCreate(
                ['tracking_number' => 'CI-2024-003'],
                [
                    'resident_id' => $res3->id, // Pedro Garcia
                    'document_type' => $indigency->id,
                    'purpose' => 'Medical Assistance Application',
                    'price' => 0, 
                    'priority' => 'Urgent', 
                    'payment_status' => 'Waived', 
                    'status' => 'Processing',
                    'created_at' => '2024-12-08 14:15:00',
                    'custom_data' => [
                        'requesting_agency' => 'DSWD',
                        'assistance_type' => 'Medical Assistance'
                    ]
                ]
            );
        }

        if ($businessPermit) {
            DocumentRequest::updateOrCreate(
                ['tracking_number' => 'BP-2024-004'],
                [
                    'resident_id' => $res4->id, // Ana Reyes
                    'document_type' => $businessPermit->id,
                    'purpose' => 'Sari-sari Store Operation',
                    'price' => $businessPermit->price,
                    'priority' => 'Normal',
                    'payment_status' => 'Paid',
                    'status' => 'Under Review',
                    'created_at' => '2024-12-07 10:00:00',
                    'custom_data' => [
                        'business_name' => 'Ana Sari-Sari Store',
                        'business_nature' => 'Retail',
                        'transaction_type' => 'New Application',
                        'business_address' => '789 Bonifacio St.'
                    ]
                ]
            );
        }

        if ($construction) {
            DocumentRequest::updateOrCreate(
                ['tracking_number' => 'CP-2024-005'],
                [
                    'resident_id' => $res5->id, // Carlos Cruz
                    'document_type' => $construction->id,
                    'purpose' => 'House Extension',
                    'price' => $construction->price,
                    'priority' => 'Normal',
                    'payment_status' => 'Paid',
                    'status' => 'Completed',
                    'created_at' => '2024-12-01 16:45:00',
                    'custom_data' => [
                        'scope_of_work' => 'Renovation/Extension',
                        'project_location' => '123 Rizal St.',
                        'floor_area' => 50,
                        'estimated_cost' => 150000
                    ]
                ]
            );
            }
       DB::table('settings')->insertOrIgnore([
            ['key' => 'monthly_budget', 'value' => '150000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'annual_budget', 'value' => '2000000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'budget_infrastructure', 'value' => '400000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'budget_health_programs', 'value' => '200000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'budget_education', 'value' => '150000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'budget_environmental', 'value' => '100000', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}