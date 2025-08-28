<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">     
    <title>{{"Recover Password"}} | Minia - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Tailwind Admin & Dashboard Template" name="description">
    <meta content="Themesbrand" name="author">
    <link rel="shortcut icon" href="{{ asset('images/logos/Infinecs-Logo-Square.ico') }}">
    <link rel="stylesheet" href="{{ asset('libs/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css')}}">
    <link rel="stylesheet" href="{{ asset('css/tailwind2.css') }}">
</head>

<body data-mode="light" data-sidebar-size="lg" class="group">

    <div class="container-fluid">
        <div class="h-screen md:overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12">
                
                <!-- Swiper/Slider Section - 7/12 -->
                <div class="col-span-12 md:col-span-7 lg:col-span-7 xl:col-span-7">
                    <div class="h-screen bg-cover relative p-5 bg-[url('../images/auth-bg.jpg')]">
                        <div class="absolute inset-0 bg-violet-500/90"></div>
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

                        <div class="flex items-center justify-center h-screen">
                            <div class="w-full md:max-w-4xl lg:px-9">
                                <div class="swiper login-slider">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="/images/users/avatar-1.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-3">
                                                    <h5 class="text-white font-size-18">Ilse R. Eaton</h5>
                                                    <p class="mb-0 text-white/50">Manager</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="/images/users/avatar-2.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-3">
                                                    <h5 class="text-white font-size-18">Mariya Willam</h5>
                                                    <p class="mb-0 text-white/50">Designer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                            <div class="flex pt-4 mt-6 mb-10">
                                                <img src="/images/users/avatar-3.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-3">
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

                <!-- Form Section - 5/12 -->
                <div class="relative z-50 col-span-12 md:col-span-5 lg:col-span-5 xl:col-span-5">
                    <div class="w-full p-10 bg-white xl:p-12 dark:bg-zinc-800">
                        <div class="flex h-[90vh] flex-col">
                            <div class="mx-auto mb-12">
                                <a href="index.html">
                                    <img src="/images/logo-sm.svg" alt="" class="inline h-7">
                                    <span class="text-xl font-medium align-middle ltr:ml-1.5 rtl:mr-1.5 dark:text-white">Minia</span>
                                </a>
                            </div>

                            <div class="my-auto">
                                <div class="text-center">
                                    <h5 class="font-medium text-gray-700 dark:text-gray-100">Reset Password</h5>
                                    <p class="mt-2 mb-4 text-gray-500 dark:text-zinc-100/60">Reset Password with Minia.</p>
                                </div>

                                <div class="px-5 py-3 my-6 border-2 border-transparent rounded bg-green-500/40">
                                    <p class="text-green-900">Enter your Email and instructions will be sent to you!</p>
                                </div>

                                <form action="index.html">
                                    <div class="mb-4">
                                        <label class="block mb-2 font-medium text-gray-700 dark:text-gray-100">Email</label>
                                        <input type="text" class="w-full py-1.5 border-gray-100 rounded placeholder:text-13 text-13 bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100" id="email" placeholder="Enter email">
                                    </div>

                                    <div class="mt-6 mb-5">
                                        <button class="w-full py-2 text-white border-transparent shadow-md btn bg-violet-500 w-100 waves-effect waves-light shadow-violet-200 dark:shadow-zinc-600" type="submit">Reset</button>
                                    </div>
                                </form>

                                <div class="mt-12 text-center">
                                    <p class="text-gray-500 dark:text-zinc-100">Remember It? 
                                        <a href="{{ url('/') }}" class="font-semibold text-violet-500"> Sign In </a> 
                                    </p>
                                </div>
                            </div>

                            <div class="text-center">
                                <p class="relative text-gray-700 dark:text-gray-100">©
                                    <script>document.write(new Date().getFullYear())</script> Minia . Crafted with <i class="text-red-400 mdi mdi-heart"></i> by Themesbrand
                                </p>
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

</body>
</html>
