<?php

use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');


		User::truncate();
		Category::truncate();
		Product::truncate();
		Transaction::truncate();
		DB::table('category_product')->truncate();

		$userQuantity = 1000;
		$categoryQuantity = 30;
		$productQuantity = 1000;
		$transactionQuantity = 1000;

		factory(User::class, $userQuantity)->create();
		factory(Category::class, $categoryQuantity)->create();
		factory(Product::class, $productQuantity)->create()->each(
			function ($product) {

				$category_ids = Category::all()->random(mt_rand(1, 5))->pluck('id');
				$product->categories()->attach($category_ids);
			}
		);
		factory(Transaction::class, $transactionQuantity)->create();


	}
}
