<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeederAddition extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $userList = '[
            {
                "name": "Test",
                "phone": "1234567890"
            }
        ]';

          // Decode the JSON data
          $users = json_decode($userList, true);

          // Iterate over the users and create them
          foreach ($users as $userData) {
              User::create([
                  'name' => $userData['name'],
                  'phone' => $userData['phone']
              ]);
          }
    }
}
