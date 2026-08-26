@extends('layouts.site')

@section('title', 'Mi perfil | '.config('shop.business.name'))

@section('content')
  <div class="max-w-4xl mx-auto space-y-6">
    <div><h1 class="text-3xl font-extrabold">Mi perfil</h1><p class="mt-2 text-white/70">Gestiona los datos y la seguridad de tu cuenta.</p></div>
    <div class="p-6 sm:p-8 bg-white shadow rounded-2xl"><div class="max-w-xl">@include('profile.partials.update-profile-information-form')</div></div>
    <div class="p-6 sm:p-8 bg-white shadow rounded-2xl"><div class="max-w-xl">@include('profile.partials.update-password-form')</div></div>
    <div class="p-6 sm:p-8 bg-white shadow rounded-2xl"><div class="max-w-xl">@include('profile.partials.delete-user-form')</div></div>
  </div>
@endsection
