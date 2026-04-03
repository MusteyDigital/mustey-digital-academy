@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $liveSession->title ?: 'Live Session' }}</h1>
                <p class="text-sm text-gray-600 mt-1">Course: {{ $liveSession->course->title }}</p>
                <p class="text-sm text-gray-600">Host: {{ $liveSession->instructor->name }}</p>
                <p class="text-sm text-gray-600">
                    Status:
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        {{ ucfirst($liveSession->status) }}
                    </span>
                </p>
            </div>

            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'instructor']) && auth()->id() === $liveSession->instructor_id)
                <form method="POST" action="{{ route('live-sessions.end', $liveSession->id) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        End Live Session
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-4">
            <div id="jitsi-container" class="w-full rounded-lg overflow-hidden" style="height: 760px;"></div>
        </div>
    </div>
</div>

<script src="https://live.musteydigitalacademy.online/external_api.js"></script>
<script>
    const domain = 'live.musteydigitalacademy.online';
    const isStudent = @json(auth()->check() && auth()->user()->role === 'student');
    const attendanceUrl = @json(route('attendance.live.store', $liveSession->course_id));
    const csrfToken = @json(csrf_token());

    const options = {
        roomName: @json($liveSession->room_name),
        width: '100%',
        height: 760,
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: @json(auth()->user()->name),
            email: @json(auth()->user()->email),
        },
        configOverwrite: {
            prejoinPageEnabled: true,
            startWithAudioMuted: true
        },
        interfaceConfigOverwrite: {
            MOBILE_APP_PROMO: false
        }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    let liveAttendanceMarked = false;

    async function markLiveAttendance() {
        if (!isStudent || liveAttendanceMarked) {
            return;
        }

        liveAttendanceMarked = true;

        try {
            await fetch(attendanceUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
        } catch (error) {
            console.error('Live attendance mark failed:', error);
        }
    }

    api.addListener('videoConferenceJoined', function () {
        markLiveAttendance();
    });
</script>
@endsection
