<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ConsommationJournaliere;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // On récupère tous les utilisateurs
        $users = User::all();

        // On retourne les informations des utilisateurs en JSON
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // La validation de données
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        // On crée un nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // On retourne les informations du nouvel utilisateur en JSON
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // On retourne les informations de l'utilisateur en JSON
        $user = Auth::user();
        if ($user instanceof User) {
            $user->load("consommationsJournalieres");
        }
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // La validation de données
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // On modifie les informations de l'utilisateur
        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        // On retourne la réponse JSON
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // On supprime l'utilisateur
        $user->delete();

        // On retourne la réponse JSON
        return response()->json();
    }

    public function register(Request $request)
    {
        Log::info('Register Request Data: ', $request->all());

        try {
            // Validation des données de la requête
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenoms' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'tel' => 'required|string|max:255|unique:users',
                'id_compteur' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            Log::info('Validated Request Data: ', $validated);
            // Création de l'utilisateur
            $user = User::create([
                'nom' => $request->nom,
                'prenoms' => $request->prenoms,
                'email' => $request->email,
                'tel' => $request->tel,
                'id_compteur' => $request->id_compteur,
                'password' => Hash::make($request->password),
            ]);

            // Retourne la réponse avec l'utilisateur et le token
            return response()->json([
                "user" => $user,
                "token" => $user->createToken("token")->plainTextToken
            ], 201);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(["error" => $th->getMessage()], 500);
        }
    }
    public function addNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'message' => 'required|string',

        ]);

        $user = $request->user();

        // Créer une nouvelle consommation
        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->type = $request->type;
        $notification->message = $request->message;

        $notification->save();


        return response()->json(['message' => 'Consommation ajoutée ou mise à jour avec succès'], 200);
    }
    public function addConsommation(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'consommation' => 'required|integer',
        ]);

        $user = $request->user();

        // Vérifier si une consommation existe déjà pour l'utilisateur et la date donnée
        $consommation = ConsommationJournaliere::where('user_id', $user->id)
            ->where('date', $request->date)
            ->first();

        if ($consommation) {
            // Mettre à jour la consommation existante
            $consommation->consommation = $request->consommation;
            $consommation->save();
        } else {
            // Créer une nouvelle consommation
            $consommation = new ConsommationJournaliere();
            $consommation->user_id = $user->id;
            $consommation->date = $request->date;
            $consommation->consommation = $request->consommation;
            $consommation->save();
        }

        return response()->json(['message' => 'Consommation ajoutée ou mise à jour avec succès', 'consommation' => $consommation], 200);
    }
    public function addFakeConsommation()
    {

        for ($i = 24; $i <= 31; $i++) {
            # code...

            // Vérifier si une consommation existe déjà pour l'utilisateur et la date donnée
            // Créer une nouvelle consommation
            $consommation = new ConsommationJournaliere();
            $consommation->user_id = 1;
            $consommation->date = "2024-05-$i";
            $consommation->consommation = random_int(10, 50);
            $consommation->save();
        }


        return response()->json(['message' => 'Consommation ajoutée ou mise à jour avec succès'], 200);
    }
    public function addAbonnement(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'total' => 'required|Numeric',
            'consommation' => 'required|Numeric',
        ]);

        $user = $request->user();

        // Vérifier si une consommation existe déjà pour l'utilisateur et la date donnée
        $abonnement = Abonnement::where('user_id', $user->id)
            ->whereRaw('consommation < total')
            ->first();

        if ($abonnement) {
            // Mettre à jour la consommation existante
            $abonnement->consommation = $request->consommation;
            $abonnement->total = $request->total;
            $abonnement->titre = $request->titre;
            $abonnement->save();
        } else {
            // Créer une nouvelle consommation
            $abonnement = new Abonnement();
            $abonnement->user_id = $user->id;
            $abonnement->total = $request->total;
            $abonnement->titre = $request->titre;
            $abonnement->consommation = $request->consommation;
            $abonnement->save();
        }

        return response()->json(['message' => 'Consommation ajoutée ou mise à jour avec succès', 'abonnement' => $abonnement], 200);
    }

    public function getAbonnement(Request $request)
    {
        $user = $request->user();
        Log::info($user);
        // Récupérer les 7 dernières consommations de l'utilisateur
        $abonnement = Abonnement::where('user_id', $user->id)
            ->whereRaw('consommation < total')
            ->first();


        return response()->json(["abonnement" => $abonnement], 200);
    }
    public function getLastSevenConsumptions(Request $request)
    {
        $user = $request->user();
        Log::info($user);

        $consommations = ConsommationJournaliere::where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->take(7)
            ->get()
            ->map(function ($consommation) {
                return [
                    'date' => $consommation->formatted_date,
                    'consommation' => $consommation->consommation,
                ];
            });

        return response()->json(["consommations" => $consommations], 200);
    }
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        Log::info($user);
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->take(7)
            ->get()
            ->map(function ($notification) {
                return [
                    'date' => $notification->formatted_date,
                    'message' => $notification->message,
                    'type' => $notification->type,
                ];
            });

        return response()->json(["notifications" => $notifications], 200);
    }
    public function login(Request $request)
    {
        $validated =  $request->validate([
            'id_compteur' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentative de connexion avec l'ID du compteur et le mot de passe
        if (!Auth::attempt(['id_compteur' => $request->id_compteur, 'password' => $request->password])) {
            // Si la tentative échoue, retourne une réponse d'erreur
            Log::info($validated);
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        // Récupère l'utilisateur authentifié
        $user = Auth::user();
        if ($user instanceof User) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token]);
        }

        return response()->json(['message' => 'Unable to generate token'], 500);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
