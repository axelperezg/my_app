<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.usuarios'))->assertRedirect(route('login'));
});

test('solicitantes cannot access the admin panel', function () {
    $solicitante = User::factory()->solicitante()->create();

    $this->actingAs($solicitante)
        ->get(route('admin.usuarios'))
        ->assertForbidden();
});

test('responsables cannot access the admin panel', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.usuarios'))
        ->assertForbidden();
});

test('admins can access the admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.usuarios'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.instituciones'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.ejercicios-fiscales'))
        ->assertOk();
});
