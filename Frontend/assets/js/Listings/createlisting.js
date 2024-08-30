const listing_url = 'http://localhost/realhouse_api/Listings/getRecent.php';

const fetchListingData = async () => {
    try {
        const authToken = sessionStorage.getItem('token');
        const response = await fetch(listing_url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `${authToken}`
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data.data;
    } catch (error) {
        console.error('Fetch error: ', error);
        return [];
    }
};

const displayListings = (listings, filterType) => {
    const listingsContainer = document.getElementById('listingsContainer');
    listingsContainer.innerHTML = '';
    const filteredListings = listings.filter(listing => listing.house_type === filterType);
    filteredListings.forEach(listing => {
        listingsContainer.appendChild(createListing(listing.file_url, listing.Price, listing.title, listing.Location, listing.bedrooms, listing.baths, listing.size, listing.listing_id));
    });
};

document.addEventListener('DOMContentLoaded', async () => {
    const listings = await fetchListingData();
    displayListings(listings, 'Rent');
});

document.getElementById('rentButton').addEventListener('click', async function (e) {
    e.preventDefault();
    const listings = await fetchListingData();
    displayListings(listings, 'Rent');
    toggleActiveButton('rentButton', 'saleButton');
});

document.getElementById('saleButton').addEventListener('click', async function (e) {
    e.preventDefault();
    const listings = await fetchListingData();
    displayListings(listings, 'Sale');
    toggleActiveButton('saleButton', 'rentButton');
});

const toggleActiveButton = (activeId, inactiveId) => {
    document.getElementById(activeId).classList.add('active');
    document.getElementById(inactiveId).classList.remove('active');
};

const createListing = (imageSrc, price, title, location, beds, baths, size, listing_id) => {
    const listing = document.createElement('div');
    listing.classList.add('listing');
    listing.innerHTML = `
        <span class="single_list" data-id="${listing_id}">
            <div class="listing_img">
                <img src="./assets/uploads/${imageSrc}" alt="">
                <div class="listing_txt">
                    <div class="price">
                        <h2> ${price} sh</h2>
                    </div>
                    <div class="txt">
                        <h2>${title}</h2>
                        <small>${location}</small>
                    </div>
                    <div class="icons">
                        <small><i class='bx bx-bed'></i> ${beds} beds</small>
                        <small><i class='bx bx-bath'></i> ${baths} bath</small>
                        <small><i class='bx bx-area'></i> ${size} Sqft</small>
                    </div>
                </div>
            </div>
        </span>
    `;

    listing.addEventListener('click', function (e) {
        e.preventDefault();
        const listingId = e.currentTarget.querySelector('.single_list').getAttribute('data-id');
        console.log(listingId, 'listing_id');
        window.location.href = `details.html?listing_id=${listingId}`;
    });

    return listing;
};
