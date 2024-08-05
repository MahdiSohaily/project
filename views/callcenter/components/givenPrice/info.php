 <!-- Start the code info section -->
 <div class="w-full bg-white <?= $infoSize ?> shadow-md rounded-md p-2">
     <p class="text-sm text-center bg-gray-700 text-white p-3 font-bold">
         <?= strtoupper($index); ?>
     </p>
     <?php if ($information) : ?>
         <div class="bg-blue-400 p-3 text-sm text-white">
             <p class="my-2 text-rose-600 font-semibold"> قطعه:</p>
             <ul>
                 <li class=""><?= $information['relationInfo']['name'] . '<br>'; ?></li>
             </ul>

             <?php if (array_key_exists("status_name", $information['relationInfo'])) : ?>
                 <span class="my-2 text-rose-600 font-semibold"> وضعیت: </span> <span><?= $information['relationInfo']['status_name'] ?></span>
             <?php endif; ?>

             <p class="my-2 text-rose-600 font-semibold"> خودروها:</p>
             <ul class="mb-5">
                 <?php foreach ($information['cars'] as $item) :
                    ?>
                     <li class="">
                         <?= $item ?>
                     </li>
                 <?php endforeach; ?>
             </ul>
             <?php if ($information['relationInfo']['description'] !== '' && $information['relationInfo']['description'] !== null) { ?>
                 <p class="mt-3 text-rose-600 font-semibold">توضیحات:</p>
                 <p class="bg-red-500 text-white rounded-md p-2 shake">
                     <?= $information['relationInfo']['description'] ?>
                 </p>
             <?php } ?>
         </div>
         <p class="my-2 font-semibold">قطعات مرتبط: </p>
         <table>
             <?php foreach ($goods as $item) :
                ?>
                 <tr class="text-sm bg-gray-200 odd:bg-orange-200">
                     <td class="p-2 w-80"> <?= $item['partName'] ?></td>
                     <td class="p-2 text-left"> <?= $item['partnumber'] ?></td>
                 </tr>
             <?php endforeach; ?>
         </table>
         <?php else :
            if (count($goods) > 0) : ?>
             <p class="my-2 font-semibold">قطعات مرتبط: </p>
             <table>
                 <?php foreach ($goods as $item) :?>
                     <tr class="text-sm bg-gray-200 odd:bg-orange-200">
                         <td class="p-2 w-80"> <?= $item['partName'] ?></td>
                         <td class="p-2 text-left"> <?= $item['partnumber'] ?></td>
                     </tr>
                 <?php endforeach; ?>
             </table>
         <?php endif; ?>
         <p class="text-sm font-semibold p-2 text-center">
             رابطه ای پیدا نشد
         </p>
     <?php endif; ?>
 </div>