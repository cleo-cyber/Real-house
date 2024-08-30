// get listing id from the header
const list_detail_url ='http://localhost/realhouse_api/Listings/getListingDetail.php';

const urlParams = new URLSearchParams(window.location.search);
const listing_id = urlParams.get('listing_id');

document.addEventListener('DOMContentLoaded', () => {
const listingDetail = async () => {
    const authToken = sessionStorage.getItem('token');
    const response = await fetch(`${list_detail_url}?listing_id=${listing_id}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `${authToken}`
        }
    });
    const data = await response.json();
    return data;
}

    listingDetail().then(data => {
        let listing = data.data;
        let images = listing.file_url;
        let imagesArray = JSON.parse(images);
        console.log(imagesArray);
        console.log(listing)
        let mainImage = imagesArray[0];
        const sampleImage = [
            '../assets/images/houses/bg1.jpg',
            '../assets/images/houses/bg2.jpg',
            '../assets/images/houses/bg3.jpg',
            '../assets/images/houses/bg4.jpg',
        ];
        
        function addSlider(swiperContainerSelector, imageSources) {
            const swiperContainer = document.querySelector(swiperContainerSelector + ' .swiper-wrapper');
            
            if (!swiperContainer) {
                console.error('Swiper container not found for selector:', swiperContainerSelector);
                return;
            }
            
            imageSources.forEach(src => {
                const swiperSlide = document.createElement('div');
                swiperSlide.classList.add('swiper-slide');
        
                const img = document.createElement('img');
                img.src ="./assets/uploads/"+ src;
        
                swiperSlide.appendChild(img);
                swiperContainer.appendChild(swiperSlide);
            });
        }
        
        // Add slides to both Swiper instances
        addSlider('.mySwiper2', imagesArray);
        addSlider('.mySwiper', imagesArray);
        
        // Initialize Swiper instances after adding slides
        const swiper = new Swiper('.mySwiper', {
            spaceBetween: 10,
            slidesPerView: 3,
            freeMode: true,
            watchSlidesProgress: true,
        });

        const swiper2 = new Swiper('.mySwiper2', {
            spaceBetween: 10,
            thumbs: {
                swiper: swiper,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        let listingContainer = document.querySelector('.image__container-text');

        addContent(listing,listingContainer);

         });
    

        });
        
        
    function addContent(listing,listingContainer) {
        const h1 = document.createElement('h1');
        h1.textContent = listing.title;
        listingContainer.appendChild(h1);
        const h2 = document.createElement('h2');
        h2.textContent = listing.Location;
        listingContainer.appendChild(h2);
        const p = document.createElement('p');
        p.textContent = listing.description;
        listingContainer.appendChild(p);

        const priceDiv = document.createElement('div');
        priceDiv.classList.add('details__container-text-price');
        const priceTitle = document.createElement('h5');
        priceTitle.textContent = 'Price:';
        priceDiv.appendChild(priceTitle);
        const price = document.createElement('p');
        price.textContent = listing.Price;
        priceDiv.appendChild(price);
        listingContainer.appendChild(priceDiv);

        const bedDiv = document.createElement('div');
        bedDiv.classList.add('details__container-text-bed');
        const bedTitle = document.createElement('h5');
        bedTitle.textContent = 'Bedrooms:';
        bedDiv.appendChild(bedTitle);
        const bed = document.createElement('p');
        bed.textContent = listing.bedrooms;
        bedDiv.appendChild(bed);
        listingContainer.appendChild(bedDiv);

        const bathDiv = document.createElement('div');
        bathDiv.classList.add('details__container-text-bath');
        const bathTitle = document.createElement('h5');
        bathTitle.textContent = 'Bathrooms:';
        bathDiv.appendChild(bathTitle);
        const bath = document.createElement('p');
        bath.textContent = listing.baths;
        bathDiv.appendChild(bath);
        listingContainer.appendChild(bathDiv);

        const sizeDiv = document.createElement('div');
        sizeDiv.classList.add('details__container-text-size');
        const sizeTitle = document.createElement('h5');
        sizeTitle.textContent = 'Size:';
        sizeDiv.appendChild(sizeTitle);
        const size = document.createElement('p');
        size.textContent = listing.size;
        sizeDiv.appendChild(size);
        listingContainer.appendChild(sizeDiv);

        const typeDiv = document.createElement('div');
        typeDiv.classList.add('details__container-text-type');
        const typeTitle = document.createElement('h5');
        typeTitle.textContent = 'Type:';
        typeDiv.appendChild(typeTitle);
        const type = document.createElement('p');
        type.textContent = listing.house_type;
        typeDiv.appendChild(type);
        listingContainer.appendChild(typeDiv);

        const statusDiv = document.createElement('div');
        statusDiv.classList.add('details__container-text-status');
        const statusTitle = document.createElement('h5');
        statusTitle.textContent = 'Status:';
        statusDiv.appendChild(statusTitle);
        const status = document.createElement('p');
        status.textContent = listing.status;
        statusDiv.appendChild(status);
        listingContainer.appendChild(statusDiv);

        const amenitiesDiv = document.createElement('div');
        amenitiesDiv.classList.add('details__container-text-amenities');
        const amenitiesTitle = document.createElement('h5');
        amenitiesTitle.textContent = 'Amenities:';
        amenitiesDiv.appendChild(amenitiesTitle);
        const amenities = document.createElement('p');
        amenities.textContent = listing.Amenities;
        amenitiesDiv.appendChild(amenities);
        listingContainer.appendChild(amenitiesDiv);

        



    }







        
    