<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ProjetController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UtilisateurController;

// ─── Page d'accueil ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ─── Authentification ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Application (authentifié) ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    // Projets
    Route::get('/projets', [ProjetController::class, 'index'])->name('projets.index');
    Route::get('/projets/{projet}', [ProjetController::class, 'show'])->name('projets.show');

    Route::middleware('role:admin,collaborateur')->group(function () {
        Route::get('/projets/create', [ProjetController::class, 'create'])->name('projets.create');
        Route::post('/projets', [ProjetController::class, 'store'])->name('projets.store');
        Route::get('/projets/{projet}/edit', [ProjetController::class, 'edit'])->name('projets.edit');
        Route::put('/projets/{projet}', [ProjetController::class, 'update'])->name('projets.update');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/projets/{projet}', [ProjetController::class, 'destroy'])->name('projets.destroy');
    });

    // Contrats
    Route::get('/contrats', [ContratController::class, 'index'])->name('contrats.index');
    Route::get('/contrats/{contrat}', [ContratController::class, 'show'])->name('contrats.show');

    Route::middleware('role:admin')->group(function () {
        Route::get('/contrats/create', [ContratController::class, 'create'])->name('contrats.create');
        Route::post('/contrats', [ContratController::class, 'store'])->name('contrats.store');
        Route::get('/contrats/{contrat}/edit', [ContratController::class, 'edit'])->name('contrats.edit');
        Route::put('/contrats/{contrat}', [ContratController::class, 'update'])->name('contrats.update');
        Route::delete('/contrats/{contrat}', [ContratController::class, 'destroy'])->name('contrats.destroy');
    });

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{ticket}/approuver', [TicketController::class, 'approuver'])->name('tickets.approuver');
    Route::post('/tickets/{ticket}/refuser', [TicketController::class, 'refuser'])->name('tickets.refuser');

    Route::middleware('role:admin')->group(function () {
        Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    });

    // Commentaires de ticket
    Route::post('/tickets/{ticket}/commentaires', [TicketController::class, 'storeCommentaire'])->name('tickets.commentaires.store');

    // Temps de ticket
    Route::post('/tickets/{ticket}/temps', [TicketController::class, 'storeTemps'])->name('tickets.temps.store');

    // Utilisateurs (admin seulement)
    Route::middleware('role:admin')->group(function () {
        Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
        Route::get('/utilisateurs/create', [UtilisateurController::class, 'create'])->name('utilisateurs.create');
        Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
        Route::get('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'show'])->name('utilisateurs.show');
        Route::get('/utilisateurs/{utilisateur}/edit', [UtilisateurController::class, 'edit'])->name('utilisateurs.edit');
        Route::put('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');
    });
});
