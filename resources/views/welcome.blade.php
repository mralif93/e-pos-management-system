@extends('layouts.guest')

@section('title', 'e-POS - Modern Point of Sale')

@section('content')
    <div class="bg-secondary-50 dark:bg-secondary-900">
        <!-- Hero Section -->
        <main class="flex-grow">
            <section class="relative py-20 sm:py-28 lg:py-32">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div
                        class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-primary-100/50 to-transparent dark:from-primary-900/20">
                    </div>
                </div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="text-center lg:text-left">
                            <h1
                                class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-secondary-800 dark:text-white mb-6">
                                The Future of Point of Sale is Here
                            </h1>
                            <p
                                class="mt-6 text-lg sm:text-xl text-secondary-600 dark:text-secondary-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                                e-POS is a powerful, intuitive, and developer-friendly point of sale system designed to
                                scale with your business.
                            </p>
                            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <a href="#"
                                    class="group inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-white bg-primary-600 hover:bg-primary-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                                    Request a Demo
                                    <i class='hgi-stroke hgi-arrow-right-01 ml-2 text-xl'></i>
                                </a>
                                <a href="#features"
                                    class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-primary-600 dark:text-primary-300 bg-primary-100 dark:bg-primary-900/30 hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-all duration-200">
                                    Learn More
                                </a>
                            </div>
                        </div>
                        <div class="relative hidden lg:block">
                            <div
                                class="bg-white dark:bg-secondary-800 rounded-2xl p-4 shadow-2xl border border-secondary-200 dark:border-secondary-800">
                                <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt="e-POS Dashboard" class="rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="py-20 bg-white dark:bg-secondary-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-base font-bold tracking-wide uppercase text-primary-600 dark:text-primary-400 mb-3">
                            Everything you need</h2>
                        <p class="text-4xl sm:text-5xl font-extrabold tracking-tight text-secondary-800 dark:text-white">
                            Developer-First POS
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                        <div class="flex items-start space-x-6">
                            <div
                                class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                <i class='hgi-stroke hgi-puzzle-01 text-3xl text-white'></i>
                            </div>
                            <div>
                                <h3 class="text-md font-bold text-secondary-800 dark:text-white mb-2">Extensible API</h3>
                                <p class="text-secondary-600 dark:text-secondary-400">Integrate with your favorite tools and
                                    services with our RESTful API.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-6">
                            <div
                                class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                <i class="hgi-stroke text-[20px] hgi-computer text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-md font-bold text-secondary-800 dark:text-white mb-2">Customizable UI</h3>
                                <p class="text-secondary-600 dark:text-secondary-400">Build your own UI components or use
                                    our pre-built library.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-6">
                            <div
                                class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                <i class='hgi-stroke hgi-database-01 text-3xl text-white'></i>
                            </div>
                            <div>
                                <h3 class="text-md font-bold text-secondary-800 dark:text-white mb-2">Real-time Data</h3>
                                <p class="text-secondary-600 dark:text-secondary-400">Get real-time insights into your sales
                                    and inventory data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonials Section -->
            <section id="testimonials" class="py-20 bg-secondary-50 dark:bg-secondary-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-base font-bold tracking-wide uppercase text-primary-600 dark:text-primary-400 mb-3">
                            Trusted by developers</h2>
                        <p class="text-4xl sm:text-5xl font-extrabold tracking-tight text-secondary-800 dark:text-white">
                            Build with Confidence
                        </p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="bg-white dark:bg-secondary-800 rounded-2xl p-8 shadow-lg">
                            <p class="text-lg text-secondary-600 dark:text-secondary-400 mb-6">"e-POS has been a
                                game-changer for our business. The extensible API allowed us to build custom integrations
                                with our existing systems."</p>
                            <div class="flex items-center">
                                <img class="h-12 w-12 rounded-full" src="https://randomuser.me/api/portraits/men/32.jpg"
                                    alt="User Avatar">
                                <div class="ml-4">
                                    <p class="font-semibold text-secondary-800 dark:text-white">John Doe</p>
                                    <p class="text-secondary-500 dark:text-secondary-400">CTO, Acme Inc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-secondary-800 rounded-2xl p-8 shadow-lg">
                            <p class="text-lg text-secondary-600 dark:text-secondary-400 mb-6">"The customizable UI is
                                fantastic. We were able to create a unique and branded experience for our customers."</p>
                            <div class="flex items-center">
                                <img class="h-12 w-12 rounded-full" src="https://randomuser.me/api/portraits/women/44.jpg"
                                    alt="User Avatar">
                                <div class="ml-4">
                                    <p class="font-semibold text-secondary-800 dark:text-white">Jane Smith</p>
                                    <p class="text-secondary-500 dark:text-secondary-400">Lead Developer, Stark Industries
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-secondary-800 rounded-2xl p-8 shadow-lg">
                            <p class="text-lg text-secondary-600 dark:text-secondary-400 mb-6">"The real-time data and
                                analytics have helped us make smarter business decisions."</p>
                            <div class="flex items-center">
                                <img class="h-12 w-12 rounded-full" src="https://randomuser.me/api/portraits/men/86.jpg"
                                    alt="User Avatar">
                                <div class="ml-4">
                                    <p class="font-semibold text-secondary-800 dark:text-white">Mike Johnson</p>
                                    <p class="text-secondary-500 dark:text-secondary-400">Founder, Wayne Enterprises</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section id="pricing" class="py-20 bg-white dark:bg-secondary-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-base font-bold tracking-wide uppercase text-primary-600 dark:text-primary-400 mb-3">
                            Simple Pricing</h2>
                        <p class="text-4xl sm:text-5xl font-extrabold tracking-tight text-secondary-800 dark:text-white">
                            Choose Your Plan
                        </p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="border border-secondary-200 dark:border-secondary-700 rounded-2xl p-8 flex flex-col">
                            <h3 class="text-md font-bold text-secondary-800 dark:text-white mb-4">Starter</h3>
                            <p class="text-secondary-600 dark:text-secondary-400 mb-6">For small businesses and startups.
                            </p>
                            <p class="text-5xl font-extrabold text-secondary-800 dark:text-white mb-6">$49<span
                                    class="text-lg font-medium text-secondary-500">/mo</span></p>
                            <ul class="space-y-4 text-secondary-600 dark:text-secondary-400 mb-8">
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Core POS Features
                                </li>
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Basic Reporting</li>
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Community Support
                                </li>
                            </ul>
                            <a href="#"
                                class="mt-auto w-full inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-lg text-primary-600 bg-primary-100 hover:bg-primary-200 dark:text-primary-300 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 transition-all duration-200">Choose
                                Plan</a>
                        </div>
                        <div class="border-2 border-primary-600 rounded-2xl p-8 flex flex-col relative">
                            <div class="absolute top-0 -translate-y-1/2 left-1/2 -translate-x-1/2">
                                <span class="bg-primary-600 text-white px-4 py-1 rounded-full text-sm font-semibold">Most
                                    Popular</span>
                            </div>
                            <h3 class="text-md font-bold text-secondary-800 dark:text-white mb-4">Pro</h3>
                            <p class="text-secondary-600 dark:text-secondary-400 mb-6">For growing businesses and
                                enterprises.</p>
                            <p class="text-5xl font-extrabold text-secondary-800 dark:text-white mb-6">$99<span
                                    class="text-lg font-medium text-secondary-500">/mo</span></p>
                            <ul class="space-y-4 text-secondary-600 dark:text-secondary-400 mb-8">
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Everything in
                                    Starter</li>
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Advanced Reporting
                                </li>
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Priority Support
                                </li>
                            </ul>
                            <a href="#"
                                class="mt-auto w-full inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-all duration-200 shadow-md hover:shadow-lg">Choose
                                Plan</a>
                        </div>
                        <div class="border border-secondary-200 dark:border-secondary-700 rounded-2xl p-8 flex flex-col">
                            <h3 class="text-md font-bold text-secondary-800 dark:text-white mb-4">Enterprise</h3>
                            <p class="text-secondary-600 dark:text-secondary-400 mb-6">For large-scale deployments.</p>
                            <p class="text-5xl font-extrabold text-secondary-800 dark:text-white mb-6">Custom</p>
                            <ul class="space-y-4 text-secondary-600 dark:text-secondary-400 mb-8">
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Everything in Pro
                                </li>
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Dedicated Support
                                </li>
                                <li class="flex items-center"><i
                                        class='hgi-stroke hgi-tick-circle text-accent mr-3 text-xl'></i>Custom Integrations
                                </li>
                            </ul>
                            <a href="#"
                                class="mt-auto w-full inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-lg text-primary-600 bg-primary-100 hover:bg-primary-200 dark:text-primary-300 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 transition-all duration-200">Contact
                                Sales</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-secondary-800 dark:bg-secondary-900 border-t border-secondary-700 dark:border-secondary-800">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-white font-semibold mb-4">Product</h3>
                        <ul class="space-y-3">
                            <li><a href="#features"
                                    class="text-secondary-400 hover:text-primary-400 transition-colors">Features</a></li>
                            <li><a href="#pricing"
                                    class="text-secondary-400 hover:text-primary-400 transition-colors">Pricing</a></li>
                            <li><a href="#"
                                    class="text-secondary-400 hover:text-primary-400 transition-colors">Documentation</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-4">Company</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">About</a>
                            </li>
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Blog</a>
                            </li>
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Careers</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-4">Resources</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">API
                                    Status</a></li>
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Support</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-4">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Privacy
                                    Policy</a></li>
                            <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Terms of
                                    Service</a></li>
                        </ul>
                    </div>
                </div>
                <div
                    class="border-t border-secondary-700 mt-10 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-secondary-400 text-sm text-center md:text-left">
                        &copy; {{ date('Y') }} e-POS System. All rights reserved.
                    </p>
                    <div class="mt-4 md:mt-0 flex items-center space-x-6">
                        <a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors"><i
                                class="hgi-stroke text-[20px] hgi-twitter text-2xl"></i></a>
                        <a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors"><i
                                class="hgi-stroke text-[20px] hgi-github text-2xl"></i></a>
                        <a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors"><i
                                class="hgi-stroke text-[20px] hgi-linkedin-01 text-2xl"></i></a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@endsection