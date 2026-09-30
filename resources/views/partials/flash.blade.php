@if (session('status') || session('success'))
    <div class="flash" role="status">{{ session('status') ?? session('success') }}</div>
@endif
@if (session('error'))
    <div class="flash err" role="alert">{{ session('error') }}</div>
@endif
