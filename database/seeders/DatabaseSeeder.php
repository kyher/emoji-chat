<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\ChannelFactory;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::factory()->withoutTwoFactor()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $otherUser = User::factory()->withoutTwoFactor()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => 'password'
        ]);

        $workspace = WorkspaceFactory::new()->ownedBy($testUser)->create([
            'name' => 'Test User Workspace',
        ]);

        $workspace->users()->attach($otherUser->id, [
            'role' => \App\Enum\WorkspaceUserRole::Member,
        ]);

        $channel = ChannelFactory::new()->ownedBy($testUser)->create([
            'workspace_id' => $workspace->id,
            'name' => 'general',
        ]);

        $channel->users()->attach($otherUser->id);
    }
}
