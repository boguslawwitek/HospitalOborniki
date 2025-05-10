@php
    use Illuminate\Support\Facades\Storage;
    $files = Storage::disk('public')->files('header-logo');
    $lastFile = !empty($files) ? last($files) : 'header-logo/logo.png';
@endphp
<div class="flex items-center gap-2">
    <img src="{{ asset('storage/' . $lastFile) }}" alt="Logo Szpitala" class="h-10">
    <h1 class="text-2xl font-semibold">Szpital Oborniki</h1>
</div>