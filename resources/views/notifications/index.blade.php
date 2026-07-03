<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Notifications
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                @if($notifications->count() === 0)
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No notifications yet.
                    </div>
                @endif

                @foreach($notifications as $n)
                    <div class="border-b border-slate-100 py-4 flex justify-between items-start gap-3 last:border-b-0">

                        <div>
                            <p class="font-medium text-slate-900">
                                {{ $n->data['message'] ?? 'Notification' }}
                            </p>

                            <p class="text-xs text-slate-500 mt-1">
                                {{ $n->created_at->diffForHumans() }}
                            </p>
                        </div>

                        @if(is_null($n->read_at))
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                @csrf
                                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline transition shrink-0">
                                    Mark as read
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 px-3 py-1 text-xs font-semibold shrink-0">
                                Read
                            </span>
                        @endif

                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>

                <form method="POST" action="{{ route('notifications.readAll') }}" class="mt-4">
                    @csrf
                    <button class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium hover:bg-slate-50 transition">
                        Mark All as Read
                    </button>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>