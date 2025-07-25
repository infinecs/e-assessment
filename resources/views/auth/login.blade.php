<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ "Login"}} | Minia - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Tailwind Admin & Dashboard Template" name="description">
    <meta content="Themesbrand" name="author">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('libs/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css')}}">
    <link rel="stylesheet" href="{{ asset('css/tailwind2.css') }}">
</head>

<body data-mode="light" data-sidebar-size="lg" class="group">

    <div class="container-fluid">
        <div class="h-screen md:overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12">

                <!-- Testimonial Section: 7/12 -->
                <div class="col-span-12 md:col-span-7 lg:col-span-7 xl:col-span-7">
                    <div class="h-screen bg-cover relative p-5 bg-[url('../images/auth-bg.jpg')]">
                        <div class="absolute inset-0 bg-violet-500/90"></div>
                        <ul class="absolute top-0 left-0 w-full h-full overflow-hidden bg-bubbles animate-square">
                            <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[10%] "></li>
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

                        <div class="flex items-center justify-center h-screen">
                            <div class="w-full md:max-w-4xl lg:px-9">
                                <div class="swiper login-slider">
                                    <div class="swiper-wrapper">
                                        <!-- Slide 1 -->
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="images/users/avatar-1.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                    <h5 class="text-white font-size-18">Ilse R. Eaton</h5>
                                                    <p class="mb-0 text-white/50">Manager</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Slide 2 -->
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="images/users/avatar-2.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                    <h5 class="text-white font-size-18">Mariya Willam</h5>
                                                    <p class="mb-0 text-white/50">Designer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Slide 3 -->
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="images/users/avatar-3.jpg" class="w-12 h-12 rounded-full" alt="...">
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

                <!-- Login Form Section: 5/12 -->
                <div class="relative z-50 col-span-12 md:col-span-5 lg:col-span-5 xl:col-span-5">
                    <div class="w-full p-10 bg-white xl:p-12 dark:bg-zinc-800">
                        <div class="flex h-[90vh] flex-col">
                            <div class="mx-auto mb-12">
                                <a href="index.html">
                                    <img src="images/logo-sm.svg" alt="" class="inline h-7">
                                    <span class="text-xl font-medium align-middle ltr:ml-1.5 rtl:mr-1.5 dark:text-white">Minia</span>
                                </a>
                            </div>

                            <div class="my-auto">
                                <div class="text-center">
                                    <h5 class="font-medium text-gray-700 dark:text-gray-100">Welcome Back !</h5>
                                    <p class="mt-2 mb-4 text-gray-500 dark:text-gray-100/60">Sign in to continue to Minia.</p>
                                </div>

                                <form class="pt-2" action="{{ url('assessment') }}">
                                    <div class="mb-4">
                                        <label class="block mb-2 font-medium text-gray-700 dark:text-gray-100">Username</label>
                                        <input type="text" class="w-full py-1.5 border-gray-50 rounded placeholder:text-13 bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100 text-13" id="username" placeholder="Enter username">
                                    </div>
                                    <div class="mb-3">
                                        <div class="flex justify-between">
                                            <label class="block mb-2 font-medium text-gray-600 dark:text-gray-100">Password</label>
                                            <a href="{{ url('password.request') }}" class="text-gray-500 dark:text-gray-100 text-sm hover:text-violet-500 hover:underline transition-colors">Forgot password?</a>
                                        </div>
                                        <div class="flex">
                                            <input type="password" class="w-full py-1.5 border-gray-50 rounded-l bg-gray-50/30 placeholder:text-13 text-13 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                            <button class="px-4 border rounded-r border-gray-50 bg-gray-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100" type="button" id="password-addon"><i class="mdi mdi-eye-off-outline"></i></button>
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-violet-500 checked:bg-blue-600 checked:border-blue-600 dark:checked:bg-blue-500 dark:checked:border-blue-500 focus:ring-offset-0">
                                            <span class="ml-2 text-gray-600 dark:text-gray-100">Remember me</span>
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <button class="w-full py-2 text-white border-transparent shadow-md btn bg-violet-500 waves-effect waves-light shadow-violet-200 dark:shadow-zinc-600" type="submit">Log In</button>
                                    </div>
                                </form>

                                <div class="pt-2 mt-5 text-center">
                                    <h6 class="mb-3 font-medium text-gray-500 text-14 dark:text-gray-100">- Sign in with -</h6>
                                    <div class="flex justify-center gap-3">
                                        <a href="#" class="w-8 h-8 leading-8 rounded-full bg-violet-500 text-center"><i class="text-sm text-white mdi mdi-facebook"></i></a>
                                        <a href="#" class="w-8 h-8 leading-8 rounded-full bg-sky-500 text-center"><i class="text-sm text-white mdi mdi-twitter"></i></a>
                                        <a href="#" class="w-8 h-8 leading-8 rounded-full bg-red-400 text-center"><i class="text-sm text-white mdi mdi-google"></i></a>
                                    </div>
                                </div>


                            </div>

                            <div class="text-center">
                                <p class="relative text-gray-500 dark:text-gray-100">© <script>document.write(new Date().getFullYear())</script> Minia. Crafted with <i class="text-red-400 mdi mdi-heart"></i> by Themesbrand</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="libs/@popperjs/core/umd/popper.min.js"></script>
    <script src="libs/feather-icons/feather.min.js"></script>
    <script src="libs/metismenujs/metismenujs.min.js"></script>
    <script src="libs/simplebar/simplebar.min.js"></script>
    <script src="libs/swiper/swiper-bundle.min.js"></script>
    <script src="js/pages/login.init.js"></script>
    <script src="js/app.js"></script>

    <!-- Password Visibility Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var passwordInput = document.querySelector('input[type="password"]');
            var eyeButton = document.getElementById('password-addon');
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
</body>

</html>
