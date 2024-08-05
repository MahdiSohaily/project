<?php
$pageTitle = "کد های انتخابی پیام خودکار";
$iconUrl = 'telegram.svg';
require_once './components/header.php';
require_once '../../app/controller/telegram/DashboardController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
?>
<style>
    .modal_container {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 100000000000000000;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
</style>
<section class="p-5 max-w-5xl rounded-md mx-auto my-5 bg-white shadow-md">
    <div class="flex justify-between">
        <h2 class="text-xl font-bold "> کد های انتخابی</h2>
        <div>
            <input style="direction: ltr !important;" onkeyup="searchPartNumbers(this.value)" class="border-2 p-2 w-72 text-sm" placeholder="جستجوی کد فنی" type="search" name="search" id="search">
            <button onclick="toggleModalDisplay()" class="bg-blue-500 border-2 border-transparent text-sm text-white py-2 px-5 rounded-sm">افزودن کد</button>
        </div>
    </div>
    <table class="w-full mt-3">
        <thead>
            <tr class="bg-gray-800">
                <th class="text-right p-3 text-white">ردیف</th>
                <th class="text-right p-3 text-white">کدفنی</th>
                <th class="text-right p-3 text-white">
                    <img src="./assets/img/setting.svg" alt="setting icon" />
                </th>
            </tr>
        </thead>
        <tbody id="partialSelectedGoods">
            <!-- Partial selected Goods will be placed here -->
        </tbody>
    </table>
    <div id="pagination"></div>
</section>
<div id="modal_container" class="modal_container" style=" display: none;">
    <div class="bg-white rounded-lg w-1/2 lg:w-1/2 p-5 border-b">
        <div class="flex justify-between">
            <h2 class="font-bold text-xl">افزودن کد جدید</h2>
            <img onclick="toggleModalDisplay()" class="cursor-pointer" src="./assets/img/close.svg" alt="close icon">
        </div>
        <div class="py-5">
            <div class="relative">
                <input onkeyup="getPartNumbers(this.value)" type="text" name="partNumber" id="partNumber" placeholder="کد فنی محصول را وارد کنید" class="w-full p-2 border border-gray-300 rounded-md">
                <div id="search_container">
                    <!-- Matched  part numbers will be displayed here -->
                </div>
            </div>
            <div class="flex justify-between items-center">
                <button onclick="addPartNumber()" class="bg-blue-500 text-white text-sm py-2 px-5 rounded-md mt-5">افزودن</button>
                <p id="message" class="text-green-600 text-sm font-bold"></p>
            </div>
        </div>
    </div>
</div>
<script>
    const message = document.getElementById('message');
    const goodsApi = "../../app/api/telegram/partNumberApi.php";
    let selectedPartNumber = null;

    function getPartialsSelectedGoods(page = 1) {
        const partialSelectedGoods = document.getElementById('partialSelectedGoods');

        var params = new URLSearchParams();
        params.append('getPartialsSelectedGoods', 'getPartialsSelectedGoods');
        params.append('page', page);

        axios
            .post(goodsApi, params)
            .then(function(response) {
                const goods = response.data;
                if (goods.length > 0) {
                    let template = ``;
                    let counter = null;

                    if (page == 1) {
                        counter = 1;
                    } else {
                        counter = (Number(page) - 1) * 50 + 1;
                    }
                    for (good of goods) {
                        template += `
                        <tr id="parent-${good.id}" class="even:bg-gray-200 odd:bg-white">
                            <td class="p-3 text-md font-semibold">${counter}</td>
                            <td class="p-3 text-md font-semibold">${good.partNumber}</td>
                            <td class="p-3 text-md font-semibold cursor-pointer" 
                                onclick="deleteGood('${good.id}')">
                                <img src="./assets/img/del.svg" alt="plus icon">
                            </td>
                        </tr>`;
                        counter++;
                    }
                    partialSelectedGoods.innerHTML = template;
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    function getSelectedGoodsCount() {
        var params = new URLSearchParams();
        params.append('getSelectedGoodsCount', 'getSelectedGoodsCount');

        axios
            .post(goodsApi, params)
            .then(function(response) {
                const totalItems = response.data;
                const itemsPerPage = 50;
                const totalPages = Math.ceil(totalItems / itemsPerPage);

                createPagination(totalPages);
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    function createPagination(totalPages, currentPage = 1) {
        const paginationElement = document.getElementById('pagination');
        paginationElement.innerHTML = ''; // Clear any existing pagination

        const paginationList = document.createElement('ul');
        paginationList.className = 'flex text-white p-2 rounded-md justify-center items-center';

        // Add first page link
        if (currentPage > 1) {
            const firstPage = createPaginationItem(totalPages, 1, '>');
            paginationList.appendChild(firstPage);
        }

        // Add previous page link
        if (currentPage > 1) {
            const prevPage = createPaginationItem(totalPages, currentPage - 1, '<');
            paginationList.appendChild(prevPage);
        }

        // Add page links
        const maxVisiblePages = 5; // Number of page links to show
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageItem = createPaginationItem(totalPages, i, i, currentPage === i);
            paginationList.appendChild(pageItem);
        }

        // Add next page link
        if (currentPage < totalPages) {
            const nextPage = createPaginationItem(totalPages, currentPage + 1, '>');
            paginationList.appendChild(nextPage);
        }

        // Add last page link
        if (currentPage < totalPages) {
            const lastPage = createPaginationItem(totalPages, totalPages, '<');
            paginationList.appendChild(lastPage);
        }

        paginationElement.appendChild(paginationList);
    }

    function createPaginationItem(totalPages, page, text, isActive = false) {
        const li = document.createElement('li');
        li.className = `bg-gray-600 text-white m-1 rounded w-8 h-8 ${isActive ? 'bg-gray-800' : ''}`;

        const a = document.createElement('a');
        a.className = 'px-2 py-1 block w-full h-full text-center';
        a.href = '#';
        a.innerText = text;
        a.addEventListener('click', function(event) {
            event.preventDefault();
            createPagination(totalPages, page);
            getPartialsSelectedGoods(page);
        });

        li.appendChild(a);
        return li;
    }

    function deleteGood(id) {
        const parent = document.getElementById(`parent-${id}`);
        var params = new URLSearchParams();
        params.append('deleteGood', 'deleteGood');
        params.append('id', id);

        axios.post(goodsApi, params)
            .then(function(response) {
                const data = response.data;
                if (data === 'true') {
                    if (parent) {
                        parent.remove();
                    }
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    function getPartNumbers(pattern) {
        if (pattern.length > 6) {
            var params = new URLSearchParams();
            params.append('search', 'search');
            params.append('pattern', pattern);

            axios.post(goodsApi, params)
                .then(function(response) {
                    const data = response.data;
                    let template = ``;
                    search_container.innerHTML = template;
                    for (item of data) {
                        template += `
                            <div onclick="selectGood(${item.id}, '${item.partnumber}')" class="p-2 bg-gray-900 text-white mt-1 flex justify-between">
                                <p>${item.partnumber}</p>
                                <img src="./assets/img/add.svg" alt="plus icon" class="cursor-pointer" />
                            </div>
                        `;
                    }
                    search_container.innerHTML = template;
                })
                .catch(function(error) {
                    console.log(error);
                });
            selectedPartNumber = null;
            message.innerHTML = "";
        } else {
            selectedPartNumber = null;
            message.innerHTML = "";
        }
    }

    function searchPartNumbers(pattern, page = 1) {
        if (pattern.length > 6) {
            const partialSelectedGoods = document.getElementById('partialSelectedGoods');
            var params = new URLSearchParams();
            params.append('searchPartNumbers', 'searchPartNumbers');
            params.append('pattern', pattern);

            axios.post(goodsApi, params)
                .then(function(response) {
                    const goods = response.data;
                    if (goods.length > 0) {
                        let template = ``;
                        let counter = null;

                        if (page == 1) {
                            counter = 1;
                        } else {
                            counter = (Number(page) - 1) * 50 + 1;
                        }
                        for (good of goods) {
                            template += `
                        <tr class="even:bg-gray-200 odd:bg-white">
                            <td class="p-3 text-md font-semibold">${counter}</td>
                            <td class="p-3 text-md font-semibold">${good.partNumber}</td>
                            <td class="p-3 text-md font-semibold cursor-pointer" 
                                onclick="deleteGood('${good.id}')">
                                <img src="./assets/img/del.svg" alt="plus icon">
                            </td>
                        </tr>`;
                            counter++;
                        }
                        partialSelectedGoods.innerHTML = template;
                        if (goods.length > 50) {
                            createPagination(goods.length);
                        }
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
            selectedPartNumber = null;
            message.innerHTML = "";
        } else {
            selectedPartNumber = null;
            message.innerHTML = "";
        }
    }

    function selectGood(id, partnumber) {
        partNumber.value = partnumber;
        selectedPartNumber = {
            "id": id,
            "partNumber": partnumber
        }
        search_container.innerHTML = '';
        message.innerHTML = partnumber;
    }

    function addPartNumber() {
        if (selectedPartNumber === null) {
            message.innerHTML = "شما کد فنی ای تا به حال انتخاب نکرده اید.";
            return false;
        }

        var params = new URLSearchParams();
        params.append('addPartNumber', 'addPartNumber');

        params.append('selectedPartNumber', JSON.stringify(selectedPartNumber));

        axios.post(goodsApi, params)
            .then(function(response) {
                const data = response.data;
                if (data == 'true') {
                    message.innerHTML = "عملیلت موفقانه صورت گرفت";

                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else if (data == 'exists') {
                    message.innerHTML = "این کد فنی قبلا انتخاب شده است";
                } else {
                    message.innerHTML = "مشکلی در انجام عملیات پیش آمده است";
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    }


    function toggleModalDisplay() {
        modal_container.style.display = modal_container.style.display === 'none' ? 'flex' : 'none';
    }

    getPartialsSelectedGoods();
    getSelectedGoodsCount();
</script>
<?php
require_once './components/footer.php';
?>