<x-layout title="Not Authorized">
    <section class="error-page">
        <h1 class="error-page__title">403 - Not Authorized</h1>
        <p class="error-page__message">Sorry, you do not have permission to access this page.</p>
        <a href="{{ url('/') }}" class="error-page__home-link">Return to Home</a>
    </section>
</x-layout>
