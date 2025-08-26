<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ 'Participant Registration' }} | Minia - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Tailwind Admin & Dashboard Template" name="description">
    <meta content="Themesbrand" name="author">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('libs/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tailwind2.css') }}">
</head>

<body data-mode="light" data-sidebar-size="lg" class="group">

    <div class="container-fluid">
        <div class="h-screen md:overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12">

                <!-- ===================================== -->
                <!-- LEFT PANEL WITH BUBBLES AND SLIDER -->
                <!-- ===================================== -->
                <div class="col-span-12 md:col-span-7">
                    <div class="h-screen bg-cover relative p-5 bg-[url('../images/auth-bg.jpg')]">

                        <!-- Purple overlay -->
                        <div class="absolute inset-0 bg-violet-500/90"></div>

                        <!-- Animated bubbles -->
                        <ul class="absolute top-0 left-0 w-full h-full overflow-hidden bg-bubbles animate-square">
                            <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[10%]"></li>
                            <li class="h-28 w-28 rounded-3xl bg-white/10 absolute left-[20%]"></li>
                            <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[25%]"></li>
                            <li class="h-20 w-20 rounded-3xl bg-white/10 absolute left-[40%]"></li>
                            <li class="h-24 w-24 rounded-3xl bg-white/10 absolute left-[70%]"></li>
                            <li class="h-32 w-32 rounded-3xl bg-white/10 absolute left-[70%]"></li>
                            <li class="h-36 w-36 rounded-3xl bg-white/10 absolute left-[32%]"></li>
                            <li class="h-20 w-20 rounded-3xl bg-white/10 absolute left-[55%]"></li>
                            <li class="h-12 w-12 rounded-3xl bg-white/10 absolute left-[25%]"></li>
                            <li class="h-36 w-36 rounded-3xl bg-white/10 absolute left-[90%]"></li>
                        </ul>

                        <!-- Centered swiper slider with faces/testimonials -->
                        <div class="flex items-center justify-center h-screen">
                            <div class="w-full md:max-w-4xl lg:px-9">
                                <div class="swiper login-slider">
                                    <div class="swiper-wrapper">
                                        <!-- Slide 1 -->
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">
                                                “Great platform for learning and taking quick assessments.”
                                            </h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="{{ asset('images/users/avatar-1.jpg') }}"
                                                    class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                    <h5 class="text-white font-size-18">Ilse R. Eaton</h5>
                                                    <p class="mb-0 text-white/50">Manager</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Slide 2 -->
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">
                                                “Simple, clean and fast. Loved how easy it was to register and start.”
                                            </h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="{{ asset('images/users/avatar-2.jpg') }}"
                                                    class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                    <h5 class="text-white font-size-18">Mariya Willam</h5>
                                                    <p class="mb-0 text-white/50">Designer</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Slide 3 -->
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">
                                                “The quiz experience was smooth and interactive. Highly recommended.”
                                            </h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="{{ asset('images/users/avatar-3.jpg') }}"
                                                    class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                    <h5 class="text-white font-size-18">Jiya Jons</h5>
                                                    <p class="mb-0 text-white/50">Developer</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===================== -->
                <!-- RIGHT FORM SECTION -->
                <!-- ===================== -->
                <div class="relative z-50 col-span-12 md:col-span-5">
                    <div class="w-full p-10 bg-white xl:p-12 dark:bg-zinc-800">
                        <div class="flex h-[90vh] flex-col">
                            <div class="mx-auto mb-12">
                                <a href="#">
                                    
                                    <span class="text-4xl font-black align-middle ltr:ml-1.5 rtl:mr-1.5 text-gray-900 dark:text-white">
                                        E-Assessment
                                    </span>
                                </a>
                            </div>

                            <!-- Form -->
                            <div class="my-auto">
                                <div class="text-center">
                                    <h5 class="font-medium text-gray-700 dark:text-gray-100">Register to Take Quiz</h5>
                                    <p class="mt-2 mb-4 text-gray-500 dark:text-gray-100/60">
                                        Fill in your details to begin.
                                    </p>
                                </div>

                                <form method="POST" action="{{ url('participantRegister/' . $eventCode) }}">

                                    @csrf

                                    {{-- Name --}}
                                    <div class="mb-4">
                                        <label class="block mb-2 font-medium text-gray-700 dark:text-gray-100">Full
                                            Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                            class="w-full py-2 border-gray-50 rounded bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 focus:ring focus:ring-violet-500/20 focus:border-violet-100"
                                            placeholder="Enter your name">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Phone --}}
                                    <div class="mb-4">
                                        <label class="block mb-2 font-medium text-gray-700 dark:text-gray-100">Phone
                                            Number</label>
                                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                            pattern="[0-9]+" title="Numbers only" required
                                            class="w-full py-2 border-gray-50 rounded bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 focus:ring focus:ring-violet-500/20 focus:border-violet-100"
                                            placeholder="Enter phone number">
                                        @error('phone_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="mb-6">
                                        <label
                                            class="block mb-2 font-medium text-gray-700 dark:text-gray-100">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            class="w-full py-2 border-gray-50 rounded bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 focus:ring focus:ring-violet-500/20 focus:border-violet-100"
                                            placeholder="Enter email"
                                            style="text-transform:lowercase;"
                                            oninput="this.value = this.value.toLowerCase()">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Password --}}
                                    <div class="mb-6">
                                        <label class="block mb-2 font-medium text-gray-700 dark:text-gray-100">Password</label>
                                        <div class="flex">
                                            <input type="password" name="password" id="register-password"
                                                class="w-full py-2 border-gray-50 rounded-l bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 focus:ring focus:ring-violet-500/20 focus:border-violet-100"
                                                placeholder="Enter password" required aria-label="Password" aria-describedby="register-password-addon">
                                            <button class="px-4 border rounded-r border-gray-50 bg-gray-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100"
                                                type="button" id="register-password-addon"><i class="mdi mdi-eye-off-outline"></i></button>
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Submit --}}
                                    <div class="mb-3">
                                        <button
                                            class="w-full py-2 text-white border-transparent shadow-md btn bg-violet-500 hover:bg-violet-600"
                                            type="submit">
                                            Register & Start Quiz
                                        </button>
                                    </div>
                                </form>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        var passwordInput = document.getElementById('register-password');
                                        var eyeButton = document.getElementById('register-password-addon');
                                        if (passwordInput && eyeButton) {
                                            var icon = eyeButton.querySelector('i');
                                            eyeButton.addEventListener('click', function () {
                                                if (passwordInput.type === 'password') {
                                                    passwordInput.type = 'text';
                                                    icon.classList.remove('mdi-eye-off-outline');
                                                    icon.classList.add('mdi-eye-outline');
                                                } else {
                                                    passwordInput.type = 'password';
                                                    icon.classList.remove('mdi-eye-outline');
                                                    icon.classList.add('mdi-eye-off-outline');
                                                }
                                            });
                                        }
                                    });
                                </script>
                            </div>

                            <div class="text-center">
                                <p class="relative text-gray-500 dark:text-gray-100">©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> Infinecs Systems | Design & Developed by <a href="https://infinecs.com/" class="text-blue-600 underline hover:text-blue-800 transition-colors">Infinecs</a>
           
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('libs/metismenujs/metismenujs.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/pages/login.init.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
