@extends('layouts.app')

@section('title', "About - Sanctum")

@section('content')
    {{-- TEAM NOTE: Halaman ini masih DUMMY untuk validasi layout storefront. --}}
    {{-- PAGE PURPOSE: Menjelaskan brand story, heritage, value proposition, dan trust signals untuk calon customer. --}}
    <section class="pt-40 pb-32 text-center min-h-[70vh] px-6 md:px-12">
        <div class="max-w-3xl mx-auto border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 mb-8">
            <strong>DUMMY PAGE:</strong> Ini placeholder halaman About. Tim content silakan isi story brand dan visual pendukung.
        </div>

        <h1 class="text-4xl font-serif text-[var(--color-primary)]">Our Heritage</h1>
        <p class="mt-8 text-[var(--color-accent)]/60 max-w-2xl mx-auto">
            Crafting olfactory memories since 1924 with signature compositions inspired by art, travel, and timeless elegance.
        </p>
    </section>
@endsection
