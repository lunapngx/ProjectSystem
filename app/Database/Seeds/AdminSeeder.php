<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel; // Ensure this is imported

class AdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Get the UserModel instance
        $users = auth()->getProvider();

        // 2. Create a new User entity
        $adminUser = new User([
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => 'password', // **CHANGE THIS TO A SECURE PASSWORD FOR PRODUCTION!**
            // 'active' => 1, // Shield users are active by default, but can explicitly set if needed
        ]);

        // 3. IMPORTANT: Save the user to the database first!
        // This populates the $adminUser entity with its new 'id' from the database.
        try {
            $users->save($adminUser);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Check if the error is due to duplicate email/username
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "Admin user already exists. Skipping creation.\n";
                return; // Exit the seeder if user already exists
            }
            throw $e; // Re-throw other database exceptions
        }


        // 4. Now that the user has an ID, add them to the 'admin' group.
        // Make sure the 'admin' group exists in app/Config/AuthGroups.php and in your database (from Shield migrations).
        $adminUser->addGroup('admin');

        echo "Admin user created and assigned to 'admin' group successfully!\n";
    }
}