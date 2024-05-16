<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $service = $request->route()->parameter('service');

        try {
            $socialUser = Socialite::driver('google')->userFromToken($request['token']);
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 401);
        } catch (Throwable $x) {
            logger()->error('Unable to process social callback because of a Socialite failre', [
                'service' => $service ?? null,
                'error' => $x->getMessage(),
            ]);

            return response()->json(['error' => 'Unable to process request'], 500);
        }

//        dd($socialUser);

        $email = $socialUser->email;
        $user = $socialUser->user;

//        Auth::login($user);
//        $user = Auth::user();

        $response = [];
//        $response['user'] = UserResource::make($user);
//        $response['token'] = Token::create($user);

        $response['user'] = $user['name']; // here is user from UserResource::make($user)
        $response['token'] = $request['token']; // here is token from Token::create($user)

        return response()->json($response);
    }
}
