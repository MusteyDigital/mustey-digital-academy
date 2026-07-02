<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('courses.show', $course->id) }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Course
            </a>

            <div>
                <h1 class="text-2xl font-bold text-slate-800">Set Live Session</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $course->title }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="POST" action="{{ route('courses.session.update', $course->id) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Meeting URL (Zoom/Google Meet/etc)</label>
                        <input
                            type="text"
                            name="meeting_url"
                            value="{{ old('meeting_url', $course->meeting_url) }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://..."
                        >
                        <x-input-error :messages="$errors->get('meeting_url')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Starts At (optional)</label>
                        <input
                            type="datetime-local"
                            name="starts_at"
                            value="{{ old('starts_at', $course->starts_at ? $course->starts_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                        <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                            Save Session
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>