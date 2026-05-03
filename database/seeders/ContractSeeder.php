<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $users = User::factory(3)->create();
        }

        foreach ($users as $user) {
            Contract::factory(5)->create(['user_id' => $user->id]);
        }
    }
}
