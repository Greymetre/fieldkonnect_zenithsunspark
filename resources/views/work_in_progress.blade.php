<x-app-layout>

    <div class="coming-soon-container">
        <h1 style="text-shadow: 0px 4px 5px #000;">Coming Soon</h1>
        <p>This module is under construction. We'll be here soon with our new awesome module.</p>
        <a href="{{ url()->previous() }}" class="btn btn-back">
            << Go Back</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</x-app-layout>