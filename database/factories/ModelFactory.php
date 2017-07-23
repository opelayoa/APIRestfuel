<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Category;
use App\Product;
use App\Seller;
use App\Transaction;
use App\User;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
	static $password;

	return [
		'name'               => $faker->name,
		'email'              => $faker->unique()->safeEmail,
		'password'           => $password ?: $password = bcrypt('secret'),
		'remember_token'     => str_random(10),
		'verified'           => $verified = $faker->randomElement([User::USER_VERIFIED, User::USER_NOT_VERIFIED]),
		'verification_token' => $verified == User::USER_VERIFIED ? null : User::generateVerificationToken(),
		'admin'              => $faker->randomElement([User::ADMIN_USER, User::REGULAR_USER]),
	];

});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Category::class, function (Faker\Generator $faker) {

	return [
		'name'        => $faker->word,
		'description' => $faker->paragraph(1)
	];

});


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Product::class, function (Faker\Generator $faker) {

	return [
		'name'        => $faker->word,
		'description' => $faker->paragraph(2),
		'quantity'    => $faker->numberBetween(1, 10),
		'status'      => $faker->randomElement([Product::PRODUCT_NOT_AVAILABLE, Product::PRODUCT_AVAILABLE]),
		'image'       => $faker->randomElement(['1.png', '2.png', '3.png']),
		'seller_id'   => User::inRandomOrder()->first()->id,
	];

});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Transaction::class, function (Faker\Generator $faker) {

	$seller = Seller::has('products')->get()->random();
	$buyer = User::all()->except($seller->id)->random();

	return [
		'quantity'    => $faker->numberBetween(1, 3),
		'buyer_id'	=> $buyer->id,
		'product_id' => $seller->products->random()->id
	];

});