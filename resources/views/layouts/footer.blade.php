<footer class="text-white py-12" style="background-color: #0a2164">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-xl font-semibold mb-4">About RadioFlow</h3>
                <p class="text-gray-400">
                    Manage your entire radio station from one powerful platform. Stream, schedule, and grow your audience with ease.
                </p>
            </div>

            <!-- Newsletter -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Subscribe to our Newsletter</h3>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-4">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email"
                           class="w-full px-4 py-2 rounded-lg text-gray-900" required>
                    <button type="submit" class="btn-primary px-6 py-2 rounded-lg">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-8 border-t border-gray-700 pt-6 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} RadioFlow. All rights reserved.
        </div>
    </div>
</footer>
