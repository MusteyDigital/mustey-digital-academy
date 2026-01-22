<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Set Live Session — {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                <form method="POST" action="{{ route('courses.session.update', $course->id) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label value="Meeting URL (Zoom/Google Meet/etc)" />
                        <x-text-input class="block mt-1 w-full" type="text" name="meeting_url"
                                      value="{{ old('meeting_url', $course->meeting_url) }}" />
                        <x-input-error :messages="$errors->get('meeting_url')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Starts At (optional)" />
                        <x-text-input class="block mt-1 w-full" type="datetime-local" name="starts_at"
                            value="{{ old('starts_at', $course->starts_at ? $course->starts_at->format('Y-m-d\TH:i') : '') }}" />
                        <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>Save Session</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
