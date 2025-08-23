<?php

namespace App\Actions\Fortify;

use App\Models\Role;
use App\Models\Radio;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        // Get valid role and radio IDs directly from database
        $validRoles = Role::whereNotIn('name', ['admin', 'directeur'])->get(['id']);
        $validRadios = Radio::where('status', 'active')->get(['id']);

        // Prepare arrays of valid IDs
        $validRoleIds = $validRoles->map(fn($role) => $role->id)->toArray();
        $validRadioIds = $validRadios->map(fn($radio) => $radio->id)->toArray();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'role_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($validRoleIds) {
                    if (!in_array($value, $validRoleIds)) {
                        $fail('The selected role is invalid.');
                    }
                }
            ],
            'radio_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($validRadioIds) {
                    if ($value && !in_array($value, $validRadioIds)) {
                        $fail('The selected radio station is invalid.');
                    }
                }
            ],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone_number' => $input['phone_number'] ?? null,
            'address' => $input['address'] ?? null,
            'bio' => $input['bio'] ?? null,
            'role_id' => $input['role_id'],
            'radio_id' => $input['radio_id'] ?? null,
        ]);
    }
}