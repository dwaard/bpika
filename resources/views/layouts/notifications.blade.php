@if (session('status')) {{-- status messages are for auth and password messages --}}
    <div class="notification is-success">
        <button class="delete"></button>
        {{ session('status') }}
    </div>
@endif
@if(session('info'))
    <div class="notification is-info">
        <button class="delete"></button>
        {{ session('info') }}
    </div>
@endif
@if(session('success'))
    <div class="notification is-success">
        <button class="delete"></button>
        {{ session('success') }}
    </div>
@endif
@if(session('warning'))
    <div class="notification is-warning">
        <button class="delete"></button>
        {{ session('warning') }}
    </div>
@endif
@if(session('danger'))
    <div class="notification is-danger">
        <button class="delete"></button>
        {{ session('danger') }}
    </div>
@endif


@push('scripts')
    <script type="application/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
                $notification = $delete.parentNode;

                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });
    </script>
@endpush
