<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// temporary routes 

Route::prefix('v1')->group(function () {

	Route::get('/pokemons/{id}', function (int $id) {

		if ($id < 1 || $id > 9000) {
			return response()->json([
				'status' => 'error',
				'message' => 'Pokemon not found',
			], 404);
		}

		return response()->json([
			'status' => 'success',
			'pokemons' => [
				[
					'id' => $id,
					'name' => 'bulbasaur',
					'type' => 'grass',
					'height' => 7,
					'weight' => 69,
				]
			],
		]);
	})->where('id', '[0-9\-]+');

	Route::get('/pokemons/{name}', function (string $name) {
		
		if ($name == "abc") {
			return response()->json([
				'status' => 'error',
				'message' => 'Pokemon not found',
			], 404);
		}

		return response()->json([
			'status' => 'success',
			'pokemons' => [
				[
					'id' => 1,
					'name' => $name,
					'type' => 'grass',
					'height' => 7,
					'weight' => 69,
				]
			],
		]);
	})->where('name', '[a-zA-Z]+');

	Route::get('/pokemons', function () {
		return response()->json([
			'status' => 'success',
			'pokemons' => [
				[
					'id' => 1,
					'name' => 'bulbasaur',
					'type' => 'grass',
					'height' => 7,
					'weight' => 69,
				],
				[
					'id' => 2,
					'name' => 'ivysaur',
					'type' => 'grass',
					'height' => 10,
					'weight' => 130,
				],
			],
		]);
	});

	Route::get('/', function () {
		return response()->json([
			'status' => 'success',
			'message' => 'Welcome to the API',
		]);
	});

});