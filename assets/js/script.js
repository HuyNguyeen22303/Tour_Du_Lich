// Menu Mobile
const buttonMenuMobile = document.querySelector(".header .inner-menu-mobile");
if(buttonMenuMobile) {
    const menu = document.querySelector(".header .inner-menu");

    // Click vào button thì mở menu
    buttonMenuMobile.addEventListener("click", () => {
        menu.classList.add("active");
    })

    // Khi click vào overlay thì đóng menu
    const overlay = document.querySelector(".header .inner-overlay, header-chi-tiet-tour .inner-overlay");
    if(overlay) {
        overlay.addEventListener("click", () => {
            menu.classList.remove("active");
        })
    }
}
// End Menu Mobile

// Menu Mobile
const buttonMenuMobileCTT = document.querySelector(".header-chi-tiet-tour .inner-menu-mobile");
if(buttonMenuMobileCTT) {
    const menuCTT = document.querySelector(".header-chi-tiet-tour .inner-menu");

    // Click vào button thì mở menu
    buttonMenuMobileCTT.addEventListener("click", () => {
        menuCTT.classList.add("active");
    })

    // Khi click vào overlay thì đóng menu
    const overlay = document.querySelector(".header-chi-tiet-tour .inner-overlay");
    if(overlay) {
        overlay.addEventListener("click", () => {
            menuCTT.classList.remove("active");
        })
    }
}
// End Menu Mobile

// Section 1 
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý phần Location (Gợi ý địa điểm)
    const locationItem = document.querySelector(".search-item:nth-child(1)");
    const locationDesc = locationItem.querySelector(".search-desc");

    const locations = [
        "Paris, France",
        "New York, USA",
        "Tokyo, Japan",
        "Sydney, Australia",
        "California, USA",
        "Alaska, USA",
        "Dubai, UAE",
        "London, UK",
        "Delhi, India"
    ];

    let locationDropdown = document.createElement("ul");
    locationDropdown.classList.add("dropdown-list");

    locations.forEach(loc => {
        let li = document.createElement("li");
        li.innerText = loc;
        li.addEventListener("click", (event) => {
            event.stopPropagation(); // Ngăn sự kiện click lan ra ngoài
            locationDesc.innerText = loc;
            locationDropdown.classList.remove("active");
        });
        locationDropdown.appendChild(li);
    });

    locationItem.appendChild(locationDropdown);

    locationItem.addEventListener("click", (event) => {
        event.stopPropagation();
        locationDropdown.classList.toggle("active");
    });

    // Xử lý phần Guests (Chọn số lượng khách)
    const guestsItem = document.querySelector(".search-item:nth-child(2)");
    const guestsDesc = guestsItem.querySelector(".search-desc");

    let guestsDropdown = document.createElement("ul");
    guestsDropdown.classList.add("dropdown-list");

    for (let i = 1; i <= 10; i++) {
        let li = document.createElement("li");
        li.innerText = `${i} Guest${i > 1 ? "s" : ""}`;
        li.addEventListener("click", (event) => {
            event.stopPropagation();
            guestsDesc.innerText = li.innerText;
            guestsDropdown.classList.remove("active");
        });
        guestsDropdown.appendChild(li);
    }

    guestsItem.appendChild(guestsDropdown);

    guestsItem.addEventListener("click", (event) => {
        event.stopPropagation();
        guestsDropdown.classList.toggle("active");
    });

    // Xử lý phần Date (Chọn ngày)
    const dateItem = document.querySelector(".search-item:nth-child(3)");
    const dateDesc = dateItem.querySelector(".search-desc");

    dateItem.addEventListener("click", (event) => {
        event.stopPropagation();

        let existingDatePicker = document.getElementById("date-picker");
        if (existingDatePicker) return;

        let datePicker = document.createElement("input");
        datePicker.type = "date";
        datePicker.id = "date-picker";
        datePicker.style.position = "absolute";
        datePicker.style.opacity = 0;
        datePicker.style.zIndex = -1;

        dateItem.appendChild(datePicker);

        setTimeout(() => {
            datePicker.showPicker();
        }, 50);

        datePicker.addEventListener("change", () => {
            if (datePicker.value) {
                dateDesc.innerText = datePicker.value;
            }
            datePicker.remove();
        });

        datePicker.addEventListener("blur", () => {
            datePicker.remove();
        });
    });

    // Xử lý khi nhấn nút Search
    const searchButton = document.querySelector(".search-button");
    searchButton.addEventListener("click", () => {
        alert(`Tìm kiếm với thông tin:
        - Điểm đến: ${locationDesc.innerText}
        - Số lượng khách: ${guestsDesc.innerText}
        - Ngày: ${dateDesc.innerText}`);
    });

    // Đóng dropdown khi click ra ngoài
    document.addEventListener("click", (event) => {
        if (!locationItem.contains(event.target)) {
            locationDropdown.classList.remove("active");
        }
        if (!guestsItem.contains(event.target)) {
            guestsDropdown.classList.remove("active");
        }
    });
});
// End Section 1 

// Swiper Section 6 
const swiperSection6 = document.querySelector(".swiper-section-6");
if(swiperSection6 ) {
  new Swiper(".swiper-section-6", {
        slidesPerView: 3,
        spaceBetween: 30,
      autoplay :  {
        delay :  4000 ,
        disableOnInteraction :  false ,
    },
      loop :  true ,
      pagination :  {
        el :  " .swiper-pagination " ,
        clickable :  true ,
    },
      breakpoints :  {
        576 :  {
          slidesPerView :  2 ,
        },
        992 :  {
          slidesPerView :  3 ,
      },
    },
  });
}



// // End Swiper Section 6 

// Swiper Section 8 
document.addEventListener("DOMContentLoaded", function () { 
    let nextBtn = document.querySelector(".greaterthan"); 
    let prevBtn = document.querySelector(".lessthan");
    let container = document.querySelector(".inner-trip"); 
    let items = document.querySelectorAll(".inner-product");
    let totalItems = items.length;
    let visibleCount = 5; 
    let currentIndex = 0;

    function updateVisibility() {
        items.forEach((item, index) => {
            if (index >= currentIndex && index < currentIndex + visibleCount) {
                item.style.display = "block"; 
            } else {
                item.style.display = "none"; 
            }
        });

        prevBtn.style.opacity = currentIndex === 0 ? "0.5" : "1";
        nextBtn.style.opacity = currentIndex + visibleCount >= totalItems ? "0.5" : "1";
    }

    nextBtn.addEventListener("click", function (event) {
        event.preventDefault();
        if (currentIndex + visibleCount < totalItems) {
            currentIndex++;
            updateVisibility();
        }
    });

    prevBtn.addEventListener("click", function (event) {
        event.preventDefault();
        if (currentIndex > 0) {
            currentIndex--;
            updateVisibility();
        }
    });

    updateVisibility();
});
// End Swiper Section 8 

// Swiper Section 15 
document.addEventListener("DOMContentLoaded", function () {
    let nextBtns = document.querySelectorAll(".greaterthan-15");  
    let prevBtns = document.querySelectorAll(".lessthan-15");      
    let containers = document.querySelectorAll(".inner-trip");

    containers.forEach(container => {
        let items = container.querySelectorAll(".box");
        let totalItems = items.length;
        let visibleCount = 4;
        let currentIndex = 0;

       
        if (nextBtns.length > 0 && prevBtns.length > 0) {
            let nextBtn = nextBtns[0];  
            let prevBtn = prevBtns[0];  

            
            function updateVisibility() {
                items.forEach((item, index) => {
                    item.style.display = (index >= currentIndex && index < currentIndex + visibleCount) ? "block" : "none";
                });

                
                prevBtn.style.opacity = currentIndex === 0 ? "0.5" : "1";
                nextBtn.style.opacity = currentIndex + visibleCount >= totalItems ? "0.5" : "1";
            }

           
            nextBtn.addEventListener("click", function (event) {
                event.preventDefault();
                if (currentIndex + visibleCount < totalItems) {
                    currentIndex++;
                    updateVisibility();
                }
            });

            prevBtn.addEventListener("click", function (event) {
                event.preventDefault();
                if (currentIndex > 0) {
                    currentIndex--;
                    updateVisibility();
                }
            });

            updateVisibility();
        } else {
            console.error("Next or Prev buttons not found");
        }
    });
}); 
// End Swiper Section 15 

// Swiper Section 16 
document.addEventListener("DOMContentLoaded", function () {
    let nextBtns = document.querySelectorAll(".greaterthan-16");  
    let prevBtns = document.querySelectorAll(".lessthan-16");      
    let containers = document.querySelectorAll(".inner-trip");

    containers.forEach(container => {
        let items = container.querySelectorAll(".box");
        let totalItems = items.length;
        let visibleCount = 4;
        let currentIndex = 0;

        
        if (nextBtns.length > 0 && prevBtns.length > 0) {
            let nextBtn = nextBtns[0];  
            let prevBtn = prevBtns[0];  

         
            function updateVisibility() {
                items.forEach((item, index) => {
                    item.style.display = (index >= currentIndex && index < currentIndex + visibleCount) ? "block" : "none";
                });

               
                prevBtn.style.opacity = currentIndex === 0 ? "0.5" : "1";
                nextBtn.style.opacity = currentIndex + visibleCount >= totalItems ? "0.5" : "1";
            }

            
            nextBtn.addEventListener("click", function (event) {
                event.preventDefault();
                if (currentIndex + visibleCount < totalItems) {
                    currentIndex++;
                    updateVisibility();
                }
            });

            prevBtn.addEventListener("click", function (event) {
                event.preventDefault();
                if (currentIndex > 0) {
                    currentIndex--;
                    updateVisibility();
                }
            });

            updateVisibility();
        } else {
            console.error("Next or Prev buttons not found");
        }
    });
}); 
// End Swiper Section 16 

// // Swiper Box Images
const boxImages = document.querySelector(".box-images");
if (boxImages) {
  // Thumbnail Swiper
  const swiperThumb = new Swiper(".swiper-box-images-thumb", {
    spaceBetween: 10,
    slidesPerView: 6,
    freeMode: true,
    watchSlidesProgress: true,
    breakpoints: {
      576: {
        spaceBetween: 10,
      },
    },
  });

  // Main Swiper
  const swiperMain = new Swiper(".swiper-box-images-main", {
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next-main",
      prevEl: ".swiper-button-prev-main",
    },
    thumbs: {
      swiper: swiperThumb,
    },
  });
}
// // End Swiper Box Images

// Zoom Box Images Main
const boxImagesMain = document.querySelector(".box-images .inner-images-main");
if(boxImagesMain) {
  new Viewer(boxImagesMain);
}
// End Zoom Box Images Main

// Khởi tạo AOS
AOS.init();
// End Khởi tạo AOS

// Section 10
document.addEventListener('DOMContentLoaded', function() {
   
    const viewAllBtn = document.getElementById('viewAllBtn');
    const hideAllBtn = document.getElementById('hideAllBtn');
    const imageList = document.getElementById('imageList');

    if (viewAllBtn && hideAllBtn && imageList) {  
        
        viewAllBtn.addEventListener('click', function(e) {
            e.preventDefault();  

            // Lấy tất cả các phần tử .inner-item bị ẩn
            const hiddenItems = imageList.querySelectorAll('.inner-item:nth-child(n+5)');
            
            // Hiển thị tất cả các hình ảnh bị ẩn
            hiddenItems.forEach(item => {
                item.style.display = 'block';
            });

            // Ẩn nút "View All Images" và hiển thị nút "Hide Images"
            viewAllBtn.style.display = 'none';
            hideAllBtn.style.display = 'inline-block';  
        });

        // Xử lý sự kiện khi nhấn "Hide Images"
        hideAllBtn.addEventListener('click', function(e) {
            e.preventDefault();  

            
            const allItems = imageList.querySelectorAll('.inner-item:nth-child(n+5)');
            
            // Ẩn tất cả các hình ảnh đã hiển thị
            allItems.forEach(item => {
                item.style.display = 'none';
            });

            // Hiển thị lại nút "View All Images" và ẩn nút "Hide Images"
            viewAllBtn.style.display = 'inline-block';
            hideAllBtn.style.display = 'none';
        });
    } else {
        console.log("View All button or hide button or image list not found");
    }
});
// End Section 10

// Section 11
document.addEventListener('DOMContentLoaded', function() {
    // Lấy các phần tử cần thiết
    const viewAllPost = document.getElementById('viewAllPost');
    const hidePost = document.getElementById('hidePost');
    const postList = document.getElementById('postList');

    if (viewAllPost && hidePost && postList) {
        
        // Khi nhấn vào "View All Posts"
        viewAllPost.addEventListener('click', function(e) {
            e.preventDefault();  // Cần có dấu ngoặc đơn

            // Lấy tất cả các phần tử .inner-item bị ẩn
            const hiddenItems = postList.querySelectorAll('.inner-item.hidden');
            
            // Hiển thị tất cả các phần tử bị ẩn
            hiddenItems.forEach(item => {
                item.classList.remove('hidden');  
            });

            // Ẩn nút "View All Posts" và hiển thị nút "Hide Posts"
            viewAllPost.style.display = 'none';
            hidePost.style.display = 'inline-block';  
        });

        // Khi nhấn vào "Hide Posts"
        hidePost.addEventListener('click', function(e) {
            e.preventDefault();  // Cần có dấu ngoặc đơn

            // Lấy tất cả các phần tử đã hiển thị (bao gồm các bài viết không bị ẩn)
            const visibleItems = postList.querySelectorAll('.inner-item');
            
            // Ẩn tất cả các phần tử đã hiển thị
            visibleItems.forEach(item => {
                item.classList.add('hidden');  // Thêm class 'hidden' để ẩn
            });

            // Hiển thị lại nút "View All Posts" và ẩn nút "Hide Posts"
            viewAllPost.style.display = 'inline-block';
            hidePost.style.display = 'none';
        });
    } else {
        console.log("View All button or hide button or post list not found");
    }
});
// End Section 11 


// Section 3, 4, 5 
const countryData = {
    "new-york": {
        title: "New York",
        image: "assets/images/NewYork.jpg",
        description: "New York is known for its iconic skyline, vibrant culture, and endless entertainment options."
    },
    "california": {
        title: "California",
        image: "assets/images/California.jpg",
        description: "California offers a variety of landscapes from beaches to mountains, and is the heart of entertainment."
    },
    "alaska": {
        title: "Alaska",
        image: "assets/images/alaska.jpg",
        description: "Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet."
    },
    "sydney": {
        title: "Sydney",
        image: "assets/images/Sydney.jpg",
        description: "Sydney is famous for its beautiful harbor, Opera House, and lively atmosphere."
    },
    "dubai": {
        title: "Dubai",
        image: "assets/images/Dubai.jpg",
        description: "Dubai blends modern luxury with traditional culture and is known for its skyscrapers and shopping."
    },
    "london": {
        title: "London",
        image: "assets/images/London.jpg",
        description: "London is a global city rich in history, culture, and iconic landmarks."
    },
    "tokyo": {
        title: "Tokyo",
        image: "assets/images/Tokyo.jpg",
        description: "Tokyo combines cutting-edge technology with deep-rooted tradition and cuisine."
    },
    "delhi": {
        title: "Delhi",
        image: "assets/images/Delhi.jpg",
        description: "Delhi is a bustling city full of culture, history, and flavorful food."
    }
};
// Sự kiện click cho các box chọn quốc gia
document.querySelectorAll(".section-4 .box").forEach(box => {
box.addEventListener("click", () => {
    const selectedCountry = box.getAttribute("data-country");
    const data = countryData[selectedCountry];

    // Gán ảnh và thông tin
    document.querySelector(".section-5 .img").src = data.image;
    document.querySelector(".section-5 .img").alt = data.title;
    document.querySelector(".section-5 .title").textContent = data.title;
    document.querySelector(".section-5 .inner-infor-1 .inner-nd").textContent = data.description;

    // Active state
    document.querySelectorAll(".section-4 .box").forEach(b => b.classList.remove("active"));
    box.classList.add("active");
});
});
// End Section 3, 4, 5 

// Section 17
document.addEventListener("DOMContentLoaded", function () {
    const ratingCards = document.querySelectorAll(".rating-card");
    const viewMoreBtn = document.querySelector(".view-more a");

    const visibleCount = 1; 
    let expanded = false;   

    function updateView() {
        ratingCards.forEach((card, index) => {
            if (!expanded && index >= visibleCount) {
                card.style.display = "none";
            } else {
                card.style.display = "flex"; 
            }
        });

        // Cập nhật nội dung nút
        viewMoreBtn.textContent = expanded ? "Hidden Comments" : "View More Comments";
    }

    // Gọi lần đầu để ẩn bớt
    updateView();

    // Khi click toggle
    viewMoreBtn.addEventListener("click", function (e) {
        e.preventDefault();
        expanded = !expanded;
        updateView();
    });
});
// End Section 17 

// Swiper Section 20 
document.addEventListener("DOMContentLoaded", function () {
    const sections = document.querySelectorAll(".box-item-1, .box-item-2, .box-item-3");

    sections.forEach(section => {
        const boxes = section.querySelectorAll(".box");
        const prevBtn = section.querySelector(".lessthan-20");
        const nextBtn = section.querySelector(".greaterthan-20");
        const totalItems = boxes.length;
        const visibleCount = 4; // Hiển thị 4 item cùng lúc
        let currentIndex = 0;

        function updateVisibility() {
            boxes.forEach((box, index) => {
                box.style.display = (index >= currentIndex && index < currentIndex + visibleCount) ? "block" : "none";
            });

            // Làm mờ nút khi đến giới hạn
            prevBtn.style.opacity = currentIndex === 0 ? "0.5" : "1";
            nextBtn.style.opacity = currentIndex + visibleCount >= totalItems ? "0.5" : "1";
        }

        // Gán sự kiện cho nút Next
        nextBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (currentIndex + visibleCount < totalItems) {
                currentIndex++;
                updateVisibility();
            }
        });

        // Gán sự kiện cho nút Prev
        prevBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
                updateVisibility();
            }
        });

        // Hiển thị ban đầu
        updateVisibility();
    });
});
// End Swiper Section 20 

// Section 21 
document.addEventListener('DOMContentLoaded', function () {
    const viewAllBtn = document.getElementById('viewAllBtn');
    const hideAllBtn = document.getElementById('hideAllBtn');
    const imageList = document.getElementById('imageList');

    if (viewAllBtn && hideAllBtn && imageList) {
        const allItems = imageList.querySelectorAll('.inner-item');

        // Mặc định chỉ hiển thị 4 ảnh đầu tiên
        allItems.forEach((item, index) => {
            item.style.display = index < 4 ? 'block' : 'none';
        });

        // Sự kiện khi nhấn "View All Images"
        viewAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            allItems.forEach(item => {
                item.style.display = 'block';
            });
            viewAllBtn.style.display = 'none';
            hideAllBtn.style.display = 'inline-block';
        });

        // Sự kiện khi nhấn "Hidden Images"
        hideAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            allItems.forEach((item, index) => {
                item.style.display = index < 4 ? 'block' : 'none';
            });
            viewAllBtn.style.display = 'inline-block';
            hideAllBtn.style.display = 'none';
        });
    } else {
        console.log("View All button, Hide button hoặc Image List không tồn tại.");
    }
});
// End Section 21 


// Section 22 
document.addEventListener('DOMContentLoaded', function() {
    const viewAllPost = document.getElementById('viewAllPost');
    const hidePost = document.getElementById('hidePost');
    const postList = document.getElementById('postList');

    if (viewAllPost && hidePost && postList) {
        // Khi nhấn vào "View All Posts"
        viewAllPost.addEventListener('click', function(e) {
            e.preventDefault();

            // Lấy tất cả các bài viết có class 'hidden'
            const hiddenItems = postList.querySelectorAll('.inner-item.hidden');

            hiddenItems.forEach(item => {
                item.classList.remove('hidden');
            });

            viewAllPost.style.display = 'none';
            hidePost.style.display = 'inline-block';
        });

        // Khi nhấn vào "Hidden Posts"
        hidePost.addEventListener('click', function(e) {
            e.preventDefault();

            // Lấy tất cả các bài viết
            const allItems = postList.querySelectorAll('.inner-item');

            allItems.forEach(item => {
                item.classList.add('hidden');
            });

            viewAllPost.style.display = 'inline-block';
            hidePost.style.display = 'none';
        });
    } else {
        console.log("Không tìm thấy nút hoặc danh sách bài viết.");
    }
});
// End Section 22 

// Section 19 
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.inner-button');
    const hiddenForm = document.querySelector('.box-2 .inner-form');

    if (toggleBtn && hiddenForm) {
        toggleBtn.addEventListener('click', function () {
            hiddenForm.classList.toggle('expanded');
            toggleBtn.textContent = hiddenForm.classList.contains('expanded') ? 'Thu gọn' : 'Xem thêm';
        });
    }

    const showMoreButtons = document.querySelectorAll('.show-more-btn');

    showMoreButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const form = btn.previousElementSibling;
            form.classList.toggle('expanded');
            btn.textContent = form.classList.contains('expanded') ? 'Show Less' : 'Show More Destinations';
        });
    });

    const headTitles = document.querySelectorAll('.head-title');

    headTitles.forEach(head => {
        const icon = head.querySelector('i');
        const form = head.nextElementSibling;

        head.addEventListener('click', () => {
            form.classList.toggle('expanded');
            icon.classList.toggle('fa-sort-down');
            icon.classList.toggle('fa-sort-up');
        });
    });
});
// End Section 19 

document.addEventListener('DOMContentLoaded', function () {
   
    const saveButtons = document.querySelectorAll('.profile-list-top button, .profile-list-bottom button');

    saveButtons.forEach(button => {
        button.addEventListener('click', function () {
            alert('Thông tin hồ sơ của bạn đã được lưu thành công!');
        });
    });

    
    const editBtn = document.querySelector('.inner-image .edit');

    if (editBtn) {
        editBtn.addEventListener('click', function () {
            alert('Bạn có thể cập nhật ảnh đại diện tại đây!');
        });
    }

    const sections = document.querySelectorAll('.inner-active div');
    const contentRight = document.querySelector('.inner-right');

    sections.forEach((section, index) => {
        section.addEventListener('click', function () {
            // Bỏ active cũ
            sections.forEach(s => s.classList.remove('active'));
            section.classList.add('active');

            
            if (index === 0) {
                contentRight.style.display = 'block';
            } else {
                contentRight.style.display = 'none';
                alert('Phần này đang được phát triển, vui lòng quay lại sau!');
            }
        });
    });
});


const opendetail = (e) => {
    console.log(e);
}