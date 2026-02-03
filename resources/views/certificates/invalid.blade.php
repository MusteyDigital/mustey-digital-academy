<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Certificate Verification
            </h2>

            <a href="{{ route('courses.index') }}" class="underline text-gray-600">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                {{-- Header --}}
                <div class="p-6 sm:p-8 border-b bg-gray-50">
                    <div class="flex items-center justify-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-red-100 text-red-800 px-4 py-2 text-sm font-semibold">
                            ❌ Invalid Certificate
                        </span>
                    </div>

                    <p class="text-center text-gray-600 mt-3">
                        This serial code does not match any certificate issued by
                        <span class="font-semibold">Nexdus Academy × Mustey Digital Academy</span>.
                    </p>
                </div>

                {{-- Body --}}
                <div class="p-6 sm:p-8 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="rounded-lg border p-5">
                            <p class="text-xs uppercase tracking-widest text-gray-500">Serial Code Entered</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1 break-all">
                                {{ $serial ?? '—' }}
                            </p>

                            <p class="text-xs uppercase tracking-widest text-gray-500 mt-4">Status</p>
                            <span class="inline-flex items-center rounded-full bg-red-100 text-red-800 px-3 py-1 text-xs font-semibold mt-2">
                                ❌ NOT FOUND
                            </span>
                        </div>

                        <div class="rounded-lg border p-5">
                            <p class="text-xs uppercase tracking-widest text-gray-500">Possible Reasons</p>
                            <ul class="mt-3 text-sm text-gray-700 space-y-2 list-disc pl-5">
                                <li>The serial code was typed incorrectly.</li>
                                <li>The certificate link was edited or incomplete.</li>
                                <li>The certificate has not been issued yet.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="rounded-lg border border-dashed p-5 bg-gray-50">
                        <p class="font-semibold text-gray-800">What you can do</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Double-check the serial code on the certificate and try again. If you received this certificate from someone,
                            request a fresh copy directly from the student or the issuing academy.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-white"
                               href="{{ route('courses.index') }}">
                                Browse Courses
                            </a>

                            <a class="inline-flex items-center rounded-md bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black"
                               href="{{ url()->previous() }}">
                                Go Back
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="p-6 sm:p-8 bg-gray-50 border-t">
                    <p class="text-xs text-gray-500 text-center">
                        Powered by Nexdus Academy × Mustey Digital Academy
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
