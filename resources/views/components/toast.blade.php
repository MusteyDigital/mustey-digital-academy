@props([
    'success' => session('success'),
    'error' => session('error'),
])

<div
    x-data="{
        showSuccess: {{ $success ? 'true' : 'false' }},
        showError: {{ $error ? 'true' : 'false' }},
        init() {
            if (this.showSuccess) setTimeout(() => this.showSuccess = false, 3500);
            if (this.showError) setTimeout(() => this.showError = false, 4500);
        }
    }"
    class="fixed top-5 right-5 z-50 space-y-3 w-[92%] sm:w-[420px]"
>
    {{-- Success --}}
    @if($success)
        <div
            x-show="showSuccess"
            x-transition.opacity.duration.200ms
            class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900 shadow"
        >
            <div class="flex items-start gap-3">
                <div class="mt-0.5">✅</div>
                <div class="flex-1">
                    <p class="font-semibold">Success</p>
                    <p class="text-sm text-green-800 mt-1">{{ $success }}</p>
                </div>
                <button @click="showSuccess=false" class="text-green-700 hover:opacity-80">✕</button>
            </div>
        </div>
    @endif

    {{-- Error --}}
    @if($error)
        <div
            x-show="showError"
            x-transition.opacity.duration.200ms
            class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900 shadow"
        >
            <div class="flex items-start gap-3">
                <div class="mt-0.5">❌</div>
                <div class="flex-1">
                    <p class="font-semibold">Error</p>
                    <p class="text-sm text-red-800 mt-1">{{ $error }}</p>
                </div>
                <button @click="showError=false" class="text-red-700 hover:opacity-80">✕</button>
            </div>
        </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900 shadow">
            <div class="flex items-start gap-3">
                <div class="mt-0.5">⚠️</div>
                <div class="flex-1">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="list-disc pl-5 mt-2 text-sm text-red-800 space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
