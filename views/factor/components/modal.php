 <!-- a modal to alert user from leak of required information before saving or marking bill as complete -->
 <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-white/30">
     <div class="relative p-4 w-full max-w-md max-h-full">
         <div class="relative bg-white rounded-lg shadow">
             <button id="close-modal" type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-md w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="popup-modal">
                 <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                 </svg>
                 <span class="sr-only">Close modal</span>
             </button>
             <div class="p-4 md:p-5 text-center">
                 <img class="mx-auto mb-4 text-gray-400 w-16 h-16 dark:text-gray-200" src="./assets/img/warning.svg" alt="warning sign icon">
                 <h3 id="message" class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">

                 </h3>
             </div>
         </div>
     </div>
 </div>