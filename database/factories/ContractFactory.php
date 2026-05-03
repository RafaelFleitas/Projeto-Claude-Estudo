<?php

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contrato'         => 'CT-' . fake()->numerify('####') . '/' . fake()->year(),
            'numero_relatorio' => fake()->optional(0.7)->numerify('REL-####'),
            'projeto'          => fake()->optional(0.8)->company(),
            'task_azure'       => fake()->optional(0.6)->numerify('#####'),
            'nota_fiscal'      => fake()->optional(0.5)->numerify('NF-#####'),
            'valor_total'      => fake()->optional(0.9)->randomFloat(2, 1000, 500000),
            'status'           => fake()->randomElement(ContractStatus::cases())->value,
            'user_id'          => User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::Pending->value,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::Active->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::Completed->value,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::Cancelled->value,
        ]);
    }
}
