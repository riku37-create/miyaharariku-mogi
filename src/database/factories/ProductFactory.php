<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $japaneseWords = ['スマートフォン', 'カメラ', 'ヘッドフォン', 'ギター', '自転車', 'テレビ'];
        return [
            'user_id' => User::factory()->has(Profile::factory()), // ランダムなユーザーを作成して関連付け
            'condition_id' => 1, // 適当なデフォルト値
            'name' => $this->faker->randomElement($japaneseWords),
            'brand' => $this->faker->randomElement(['Nike', 'Adidas', 'UNIQLO', 'GUCCI', 'Apple', 'Sony', 'Rolex', 'Casio', 'Dior', 'Patagonia']),
            'price' => $this->faker->numberBetween(1000, 50000),
            'description' => $this->faker->sentence,
            'image' => 'product-img/default.jpg',
        ];
    }
}