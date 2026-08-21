<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OrdersController;
use App\Models\Users;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function account_info(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->activeTab ?? 'management';

        if ($activeTab === 'management') {
            $tabData['user'] = $user;
        } else if ($activeTab === 'orders') {
            $tabData['orders'] = OrdersController::get_orders($user->user_id);
        }

        return Inertia::render('User/Account', [
            'tabData' => Inertia::defer(fn() => $tabData),
            'activeTab' => $activeTab,
        ]);
    }
}
