<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Entities\User;
use App\Services\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenService $tokenService
    ) {}

    public function register(Request $request)
    {
         logger('=== REGISTER START ===');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        logger('Validation passed');
        $user = new User();
        $user->setName($request->name);
        $user->setEmail($request->email);
        $user->setPassword(Hash::make($request->password));
            logger('User created, saving to database...');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = $user->createToken('auth_token')->plainTextToken;
        logger('Token created successfully', ['token' => $token]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        logger('=== LOGIN START ===');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        logger('Validation passed');

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $request->email]);

        logger('User found: ' . ($user ? 'yes' : 'no'));

        if (!$user || !Hash::check($request->password, $user->getPassword())) {
            logger('Invalid credentials');
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        logger('Password check passed, creating token...');

        try {
            // Használd a TokenService-t, NE a $user->createToken()-t!
            $token = $this->tokenService->createToken($user, 'auth_token');
            logger('Token created successfully');
        } catch (\Exception $e) {
            logger('ERROR creating token: ' . $e->getMessage());
            logger('Trace: ' . $e->getTraceAsString());
            throw $e;
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
}
