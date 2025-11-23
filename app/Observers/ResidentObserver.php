<?php

namespace App\Observers;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon; // <-- IMPORT CARBON

class ResidentObserver
{
    /**
     * Handle the Resident "created" event.
     *
     * @param  \App\Models\Resident  $resident
     * @return void
     */
    public function created(Resident $resident)
    {
        // Only create a user if an email is provided
        if (empty($resident->email)) {
            return; // Cannot create a user without an email
        }
        
        // Check if a user with this email already exists
        $existingUser = User::where('email', $resident->email)->first();

        if ($existingUser) {
            // A user with this email already exists. Link them.
            $resident->user_id = $existingUser->id;
            $resident->saveQuietly(); // Use saveQuietly to not trigger observers again
        } else {
            // No user found, so let's create a new one.

            // 1. Create username
            $username = Str::lower(substr($resident->first_name, 0, 1) . '.' . Str::slug($resident->last_name, ''));
            $count = User::where('username', 'like', $username . '%')->count();
            if ($count > 0) {
                $username = $username . ($count + 1); 
            }

            // --- NEW PASSWORD LOGIC ---
            // 1. Get the last name, lowercase, no spaces (e.g., "dela cruz" -> "delacruz")
            $lastName = Str::slug($resident->last_name, '');

            // 2. Get the birthdate as YYYYMMDD (e.g., "1990-01-15" -> "19900115")
            // We use Carbon::parse() to safely handle the date string.
            $birthdate = Carbon::parse($resident->date_of_birth)->format('Ymd');

            // 3. Combine them to create the default password
            $defaultPassword = $lastName . $birthdate;
            // --- END NEW PASSWORD LOGIC ---

            // 4. Create the new user
            $user = User::create([
                'first_name' => $resident->first_name,
                'last_name' => $resident->last_name,
                'email' => $resident->email,
                'contact_number' => $resident->contact_number,
                'username' => $username,
                'password' => Hash::make($defaultPassword), // <-- USE THE NEW PASSWORD
                'role' => 'resident',
                'is_active' => $resident->is_active,
            ]);

            // 5. Link the new User to the Resident
            $resident->user_id = $user->id;
            $resident->saveQuietly();
        }
    }

    /**
     * Handle the Resident "updated" event.
     *
     * @param  \App\Models\Resident  $resident
     * @return void
     */
    public function updated(Resident $resident)
    {
        if ($resident->user) {
            // Check if any key details were changed
            if ($resident->isDirty('first_name', 'last_name', 'email', 'contact_number', 'is_active')) {
                $resident->user->update([
                    'first_name' => $resident->first_name,
                    'last_name' => $resident->last_name,
                    'email' => $resident->email,
                    'contact_number' => $resident->contact_number,
                    'is_active' => $resident->is_active,
                ]);
            }
        } elseif (!empty($resident->email) && is_null($resident->user)) {
            // This handles the case where a resident was created without an email,
            // and then an email was added later.
            $this->created($resident);
        }
    }

    /**
     * Handle the Resident "deleting" event (for soft deletes).
     *
     * @param  \App\Models\Resident  $resident
     * @return void
     */
    public function deleting(Resident $resident)
    {
        // When a resident is soft-deleted, deactivate their user account.
        if ($resident->user) {
            $resident->user->update(['is_active' => false]);
        }
    }
}