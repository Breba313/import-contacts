<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\File;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(File::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'filename' => 'contacts.csv',
        'location' => '1/contacts.csv',
        'field_name' => 'name',
        'field_birthday' => 'birthday',
        'field_phone' => 'phone',
        'field_address' => 'address',
        'field_credit_card_number' => 'creditcard',
        'field_email' => 'email'
    ];
});
