<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\City;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_prefix' => ['nullable', 'string', 'max:10'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email_id'],
            'mobile_number' => ['required', 'string', 'size:10'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'clinic_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'terms' => ['required', 'accepted'],
            'sale_consent' => ['nullable', 'boolean'],
            'research_consent' => ['nullable', 'boolean'],
        ]);

        // Generate a random password since user will login via email only
        $randomPassword = bin2hex(random_bytes(8));
        
        $user = User::create([
            'name_prefix' => $request->name_prefix,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email_id' => $request->email,
            'password' => Hash::make($randomPassword),
            'mobile_number' => $request->mobile_number,
            'registration_number' => $request->registration_number,
            'clinic_name' => $request->clinic_name,
            'city' => $request->city,
            'state' => $request->state,
            'sale_consent' => $request->sale_consent ?? false,
            'research_consent' => $request->research_consent ?? false,
        ]);

        event(new Registered($user));

        // Auto-login after registration
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'redirect_url' => route('thank.you')
        ]);
    }

    /**
     * Fetch cities for autocomplete from database
     */
    public function fetchCities(Request $request)
    {
        $search = $request->term;
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        
        $cities = City::where('city_name', 'LIKE', "%{$search}%")
                    ->where('is_active', true)
                    ->orderBy('city_name')
                    ->limit(10)
                    ->get();
        
        $results = [];
        foreach ($cities as $city) {
            $results[] = [
                'label' => $city->city_name . ', ' . $city->state_name,
                'value' => $city->city_name,
                'state' => $city->state_name
            ];
        }
        
        return response()->json($results);
    }
}