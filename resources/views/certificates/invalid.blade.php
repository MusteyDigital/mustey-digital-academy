<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Certificate Verification
            </h2>

            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="p-6 sm:p-8 border-b border-slate-200 bg-slate-50">
                    <div class="flex items-center justify-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-red-50 text-red-700 px-4 py-2 text-sm font-semibold">
                            Invalid Certificate
                        </span>
                    </div>

                    <p class="text-center text-slate-600 mt-3">
                        This serial code does not match any certificate issued by
                        <span class="font-semibold">Mustey Digital Academy</span>.
                    </p>
                </div>

                {{-- Body --}}
                <div class="p-6 sm:p-8 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="rounded-xl border border-slate-200 p-5">
                            <p class="text-xs uppercase tracking-widest text-slate-500">Serial Code Entered</p>
                            <p class="text-lg font-semibold text-slate-900 mt-1 break-all">
                                {{ $serial ?? '—' }}
                            </p>

                            <p class="text-xs uppercase tracking-widest text-slate-500 mt-4">Status</p>
                            <span class="inline-flex items-center rounded-full bg-red-50 text-red-700 px-3 py-1 text-xs font-semibold mt-2">
                                NOT FOUND
                            </span>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-5">
                            <p class="text-xs uppercase tracking-widest text-slate-500">Possible Reasons</p>
                            <ul class="mt-3 text-sm text-slate-700 space-y-2 list-disc pl-5">
                                <li>The serial code was typed incorrectly.</li>
                                <li>The certificate link was edited or incomplete.</li>
                                <li>The certificate has not been issued yet.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="rounded-xl border border-dashed border-slate-200 p-5 bg-slate-50">
                        <p class="font-semibold text-slate-800">What you can do</p>
                        <p class="text-sm text-slate-600 mt-1">
                            Double-check the serial code on the certificate and try again. If you received this certificate from someone,
                            request a fresh copy directly from the student or the issuing academy.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-white transition"
                               href="{{ route('courses.index') }}">
                                Browse Courses
                            </a>

                            <a class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition"
                               href="{{ url()->previous() }}">
                                Go Back
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="p-6 sm:p-8 bg-slate-50 border-t border-slate-200">
                    <p class="text-xs text-slate-500 text-center">
                        Powered by Mustey Digital Academy
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>