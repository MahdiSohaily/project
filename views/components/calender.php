<style>
    body {
        background-color: #F3F4F6 !important;
    }

    .bg-gradient::after {
        background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), rgba(0, 0, 0, 1), transparent 20%);
    }
</style>

<div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg">
    <div class="border border-dashed border-gray-900 flex flex-col items-center justify-center p-5 rounded-lg">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-2"><?= jdate('l J F') . ' - ' . jdate('Y/m/d'); ?></h1>
        <p class="flex items-end mt-2 text-base text-center text-gray-600 gap-x-2">
            <span class="ml-3 text-sm font-semibold"> دور گردون گر دو روزی بر مراد ما نرفت </span>
            <span class="mr-3 text-sm font-semibold"> دائما یکسان نباشد حال دوران غم مخور</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-red-500 shrink-0">
                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
            </svg>
        </p>

        <div class="grid w-full max-w-xl grid-cols-7 gap-4 mx-auto mt-6">
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">شنبه</p>
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">یکشنبه</p>
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">دوشنبه</p>
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">سه شنبه</p>
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">چهار شنبه</p>
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">پنجشنبه</p>
            <p class="flex items-center justify-center h-12 text-blue-400 text-sm font-semibold">جمعه</p>
        </div>

        <div class="grid w-full max-w-xl grid-cols-7 gap-6 mx-auto">
            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">1</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">2</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">3</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">4</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">5</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">6</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">7</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">8</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">9</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">10</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">11</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">12</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">13</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">14</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">15</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">16</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">17</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">18</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">19</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">20</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">21</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">22</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">23</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">24</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">25</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">26</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">27</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">28</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">29</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">30</div>
            </div>

            <div class="relative w-full h-10 cursor-pointer hover:scale-110 box bg-gradient after:absolute after:inset-0 after:z-10 after:h-full after:w-full after:transition-opacity after:duration-500 hover:bg-gray-800">
                <div class="absolute inset-[3px] z-20 flex items-center justify-center bg-white text-gray-800 font-semibold">31</div>
            </div>
        </div>

    </div>
</div>