@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-6rem)] px-6 py-8">
  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
      <p class="text-gray-600">Welcome to your administrative dashboard</p>
    </div>
  </div>
  
  <!-- underline divider (matches other pages style) -->
  <div class="border-b border-gray-200 mb-6"></div>
  
  <!-- Dashboard content will go here -->
  <div class="bg-white rounded-xl shadow-lg p-6">
    <p class="text-gray-500">Dashboard content coming soon...</p>
  </div>
</div>
@endsection
