document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const tourId = urlParams.get('id');

    if (!tourId) {
        console.error('Không tìm thấy tourId trong URL');
        return;
    }

    fetch(`back_end/get_tour_detail.php?id=${tourId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Tour data:', data);

            // Cập nhật tiêu đề
            const titleEl = document.getElementById('tourTitle');
            if (titleEl && data.title) {
                titleEl.textContent = data.title;
            } else {
                console.error('Không tìm thấy tourTitle element hoặc data.title');
            }

            // Cập nhật giá
            const priceEl = document.getElementById('tourPrice');
            if (priceEl && data.price) {
                priceEl.textContent = `$${parseFloat(data.price).toLocaleString()}`;
            } else {
                console.error('Không tìm thấy tourPrice element hoặc data.price');
            }

            // Đổ ảnh chính (main swiper)
            const mainWrapper = document.getElementById('swiperMainWrapper');
            const thumbWrapper = document.getElementById('swiperThumbWrapper');

            if (!mainWrapper || !thumbWrapper) {
                console.error('Không tìm thấy wrapper: mainWrapper hoặc thumbWrapper');
                return;
            }

            // Xóa các slide cũ
            mainWrapper.innerHTML = '';
            thumbWrapper.innerHTML = '';

            // Kiểm tra và thêm slide từ data.extra_images
            if (!data.extra_images || !Array.isArray(data.extra_images) || data.extra_images.length === 0) {
                console.error('data.extra_images không hợp lệ hoặc rỗng:', data.extra_images);
                return;
            }

            data.extra_images.forEach((imgUrl, index) => {
                if (imgUrl) {
                    // Slide ảnh chính
                    const mainSlide = document.createElement('div');
                    mainSlide.className = 'swiper-slide';
                    mainSlide.innerHTML = `
                        <div class="inner-image">
                            <img src="${imgUrl}" alt="Tour Image ${index + 1}" onerror="this.src='assets/imagesTour/default.jpg';">
                        </div>
                    `;
                    mainWrapper.appendChild(mainSlide);

                    // Slide thumbnail
                    const thumbSlide = document.createElement('div');
                    thumbSlide.className = 'swiper-slide';
                    thumbSlide.innerHTML = `
                        <div class="inner-image">
                            <img src="${imgUrl}" alt="Thumbnail ${index + 1}" onerror="this.src='assets/imagesTour/default.jpg';">
                        </div>
                    `;
                    thumbWrapper.appendChild(thumbSlide);
                }
            });

            // Khởi tạo Swiper
            const swiperThumb = new Swiper('.swiper-box-images-thumb', {
                slidesPerView: Math.min(4, data.extra_images.length),
                spaceBetween: 10,
                freeMode: true,
                watchSlidesProgress: true,
                loop: false, // Tắt loop cho thumbnail
            });

            const swiperMain = new Swiper('.swiper-box-images-main', {
                loop: false, // Tắt loop cho slider chính
                navigation: {
                    nextEl: '.swiper-button-next-main',
                    prevEl: '.swiper-button-prev-main',
                },
                thumbs: {
                    swiper: swiperThumb,
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
            });
        })
        .catch(error => {
            console.error('Lỗi khi fetch dữ liệu:', error);
        });
});