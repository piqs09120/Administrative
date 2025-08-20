<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document - Soliera</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/soliera.css'])
    
</head>
<body class="bg-base-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebarr')
        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Header -->
            @include('partials.navbar')

            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @if(session('success'))
                    <div class="alert alert-success mb-6">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error mb-6">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Back button and title -->
                <div class="flex items-center mb-6">
                    <a href="{{ route('document.show', $document->id) }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
                    </a>
                    <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Edit Document</h1>
                </div>

                @if($errors->any())
                    <div class="alert alert-error mb-6">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="max-w-3xl">
                    <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                        <div class="card-body">
                            <form action="{{ route('document.update', $document->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                                            <i data-lucide="file-text" class="w-4 h-4" style="color: var(--color-regal-navy);"></i>
                                            Document Title *
                                        </span>
                                    </label>
                                    <input type="text" name="title" class="input input-bordered"
                                           value="{{ old('title', $document->title) }}" placeholder="Enter document title" required
                                           style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                                </div>

                                <div class="form-control mb-6">
                                    <label class="label">
                                        <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                                            <i data-lucide="align-left" class="w-4 h-4" style="color: var(--color-regal-navy);"></i>
                                            Description
                                        </span>
                                    </label>
                                    <textarea name="description" class="textarea textarea-bordered"
                                              placeholder="Enter document description (optional)"
                                              style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">{{ old('description', $document->description) }}</textarea>
                                </div>

                                <div class="card-actions justify-end">
                                    <a href="{{ route('document.show', $document->id) }}" class="btn btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>UPDATE DOCUMENT
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @include('partials.soliera_js')
</body>
</html>