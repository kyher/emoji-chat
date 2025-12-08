<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'owner_id' => User::factory(),
        ];
    }

    public function ownedBy(User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'owner_id' => $user->id,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Workspace $workspace) {
            $workspace->users()->attach($workspace->owner_id, [
                'role' => \App\Enum\WorkspaceUserRole::Administrator,
            ]);
        });
    }
}
