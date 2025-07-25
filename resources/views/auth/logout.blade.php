{{> partials/main }}

    <head>
        {{> partials/title-meta title="Logout" }}

        <link rel="stylesheet" href="assets/libs/swiper/swiper-bundle.min.css">

        {{> partials/head-css }}
    </head>

    {{> partials/body }}

        <div class="container-fluid">
            <div class="h-screen md:overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-12 ">
                    <div class="relative z-50 col-span-12 md:col-span-5 lg:col-span-4 xl:col-span-3">
                        <div class="w-full p-10 bg-white xl:p-12 dark:bg-zinc-800">
                            <div class="flex h-[90vh] flex-col">
                                <div class="mx-auto">
                                    <a href="index.html" class="">
                                        <img src="assets/images/logo-sm.svg" alt="" class="inline h-8"> <span class="text-xl font-medium align-middle ltr:ml-2 rtl:mr-2 dark:text-white">Minia</span>
                                    </a>
                                </div>

                                <div class="my-auto">
                                    <div>
                                        <div class="w-20 h-20 mx-auto text-center rounded-full bg-violet-500/5 text-primary dark:bg-zinc-700">
                                            <i class="bx bxs-user text-4xl leading-[2.2] text-violet-500"></i>
                                        </div>
                                    </div>

                                    <div class="mt-6 text-center">
                                        <h5 class="text-gray-700 dark:text-gray-100">You are Logged Out</h5>
                                        <p class="mt-2 text-gray-500 text-15 dark:text-zinc-100/60">Thank you for using <span class="font-semibold text-gray-800 dark:text-gray-100">Minia</span></p>
                                    </div>

                                    <div class="mt-5">
                                        <a href="auth-login.html" class="btn border-transparent bg-violet-500 w-full py-2.5 text-white shadow-md shadow-violet-200 dark:shadow-zinc-600">Sign In</a>
                                    </div>

                                    <div class="mt-12 text-center">
                                        <p class="text-gray-500 dark:text-zinc-100/60">Don't have an account ? <a href="login.html" class="font-semibold text-violet-500"> Signup </a> </p>
                                    </div>
                                </div>


                                <div class="text-center ">
                                    <p class="relative mb-5 text-gray-500 dark:text-gray-100">©
                                        <script>document.write(new Date().getFullYear())</script> Minia . Crafted with <i class="text-red-400 mdi mdi-heart"></i> by Themesbrand
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-7 lg:col-span-8 xl:col-span-9">
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
                                            <div class="swiper-slide">
                                                <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                                <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                                <div class="flex pt-4 mt-6 mb-10">
                                                    <img src="assets/images/users/avatar-1.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                    <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-3">
                                                        <h5 class="text-white font-size-18">Ilse R. Eaton</h5>
                                                        <p class="mb-0 text-white/50">Manager
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                                <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                                <div class="flex pt-4 mt-6 mb-10">
                                                    <img src="assets/images/users/avatar-2.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                    <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-3">
                                                        <h5 class="text-white font-size-18">Mariya Willam</h5>
                                                        <p class="mb-0 text-white/50">Designer
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                                <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                                <div class="flex pt-4 mt-6 mb-10">
                                                    <img src="assets/images/users/avatar-3.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                    <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-3">
                                                        <h5 class="text-white font-size-18">Jiya Jons</h5>
                                                        <p class="mb-0 text-white/50">Developer
                                                        </p>
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
                </div>
            </div>
        </div>

        {{> partials/customizer }}

        {{> partials/vendor-scripts }}

        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

        <script  src="assets/js/pages/login.init.js"></script>

        <script  src="assets/js/app.js"></script>

    </body>

</html>