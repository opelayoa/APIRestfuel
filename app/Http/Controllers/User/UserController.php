<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Mockery\Exception;

class UserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return response()->json(["data" => User::all()], 404);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$rules = [
			'name'     => 'required',
			'email'    => 'required|email|unique:users',
			'password' => 'required|min:6|confirmed'
		];
		$this->validate($request, $rules);

		$fields = $request->all();
		$fields['password'] = bcrypt($request->password);
		$fields['verified'] = User::USER_NOT_VERIFIED;
		$fields['verification_token'] = User::generateVerificationToken();
		$fields['admin'] = User::REGULAR_USER;

		return response()->json(['data' => User::create($fields)], 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		return response()->json(['data' => User::findOrFail($id)], 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$user = User::findOrFail($id);

		$rules = [
			'email'    => 'email|unique:users,email,' . $user->id,
			'password' => 'min:6|confirmed',
			'admin'    => 'in:' . User::REGULAR_USER . ',' . User::ADMIN_USER
		];

		$this->validate($request, $rules);

		if ($request->has('name')) {
			$user->name = $request->name;
		}

		if ($request->has('email') && $user->email != $request->email) {
			$user->verified = User::USER_NOT_VERIFIED;
			$user->verification_token = User::generateVerificationToken();
			$user->email = $request->email;
		}

		if ($request->has('password')) {
			$user->password = bcrypt($request->password);
		}

		if ($request->has('admin')) {
			if (!$user->isVerified()) {
				return response()->json(['error' => 'Only verified users can change its user type', 'code' => 409], 409);
			}

			$user->admin = $request->admin;
		}

		if (!$user->isDirty()) {
			return response()->json(['error' => 'You should especify a value at least to be updated', 'code' => 422], 422);
		}

		$user->save();

		return response()->json(['data' => $user], 200);


		$user->name = $request->name;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$user = User::findOrFail($id);

		$user->delete();

		return response()->json(['data' => $user],200);
	}
}
