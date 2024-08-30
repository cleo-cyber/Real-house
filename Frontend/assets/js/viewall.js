const url = 'http://localhost/realhouse_api/Listings/viewall.php';
const lists = document.querySelector(".lists");
const authToken = sessionStorage.getItem('token');

const fetchData = async () => {
    try {
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': authToken ? `${authToken}` : ''
            }
        });

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            const data = await res.json();
            console.log(data);
            if (data.status) {
                renderListings(data.data);
                setupFilters(data.data);
            } else {
                lists.innerHTML = "<p>No listings found.</p>";
            }
        } else {
            const text = await res.text();
            throw new Error(`Unexpected content-type: ${contentType}. Response: ${text}`);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        lists.innerHTML = `<p>Error fetching listings: ${error.message}</p>`;
    }
};

fetchData();

const renderListings = (listingsData) => {
    let html = "";
    listingsData.forEach(element => {
        html += `
        <div class="listing listing_card" data-id=${element.listing_id}>
            <div class="listing_Image">
                <img src="./assets/uploads/${element.file_url}" alt="${element.title}">
            </div>
            <div class="listing_info">
                <div class="l_price">
                    <h3 class="price"><span>Ksh</span> ${element.Price}</h3>
                    <p class="purpose">${element.house_type}</p>
                </div>
                <div class="l_details">
                    <h2>${element.title}</h2>
                    <p>${element.Location}</p>
                </div>
                <div class="l_amenities icons">
                    <small><i class='bx bx-bed'></i> <span>${element.bedrooms} beds</span></small>
                    <small><i class='bx bx-bath'></i> <span>${element.baths} baths</span></small>
                    <small><i class='bx bx-area'></i> <span>${element.size} sqft</span></small>
                </div>
            </div>
        </div>`;
    });
    lists.innerHTML = html;

    const listingCards = document.querySelectorAll('.listing_card');
    listingCards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
            const listingId = card.getAttribute('data-id');
            window.location.href = `details.html?listing_id=${listingId}`;
        });
    });
};

const setupFilters = (data) => {
    const salerent = document.querySelector('.select_container #type');
    const FurnishedUnfurnished = document.querySelector('.select_container #status');

    const applyFilters = () => {
        let filteredData = data;

        if (salerent.value) {
            filteredData = filteredData.filter(listing => listing.house_type === salerent.value);
        }

        if (FurnishedUnfurnished.value) {
            filteredData = filteredData.filter(listing => listing.status === FurnishedUnfurnished.value);
        }

        // Render the filtered data if either filter is applied, otherwise render all data
        renderListings(filteredData.length > 0 ? filteredData : data);
    };

    // Apply filters when the user changes the selection in either dropdown
    salerent.addEventListener('change', applyFilters);
    FurnishedUnfurnished.addEventListener('change', applyFilters);
};


const minPriceSlider = document.getElementById('minPriceRange');
const maxPriceSlider = document.getElementById('maxPriceRange');
const minPriceInput = document.getElementById('minPriceInput');
const maxPriceInput = document.getElementById('maxPriceInput');
const applyFiltersButton = document.getElementById('applyFilters');

// Synchronize slider with input fields
const syncSlidersWithInputs = () => {
    minPriceInput.value = minPriceSlider.value;
    maxPriceInput.value = maxPriceSlider.value;
};

// Synchronize input fields with sliders
const syncInputsWithSliders = () => {
    minPriceSlider.value = minPriceInput.value;
    maxPriceSlider.value = maxPriceInput.value;
};

// Prevent the min slider from going beyond the max slider and vice versa
const enforceSliderLimits = () => {
    if (parseInt(minPriceSlider.value) > parseInt(maxPriceSlider.value)) {
        minPriceSlider.value = maxPriceSlider.value;
    }
    if (parseInt(maxPriceSlider.value) < parseInt(minPriceSlider.value)) {
        maxPriceSlider.value = minPriceSlider.value;
    }
    syncSlidersWithInputs();
};

// Add event listeners
minPriceSlider.addEventListener('input', () => {
    enforceSliderLimits();
    syncSlidersWithInputs();
});
maxPriceSlider.addEventListener('input', () => {
    enforceSliderLimits();
    syncSlidersWithInputs();
});

minPriceInput.addEventListener('input', () => {
    enforceSliderLimits();
    syncInputsWithSliders();
});
maxPriceInput.addEventListener('input', () => {
    enforceSliderLimits();
    syncInputsWithSliders();
});

applyFiltersButton.addEventListener('click', () => {
    const minPrice = parseInt(minPriceInput.value, 10);
    const maxPrice = parseInt(maxPriceInput.value, 10);

    // Apply the filters to your data
    applyFilters(minPrice, maxPrice);
});

// Function to filter and render listings based on price
const applyFilters = (minPrice, maxPrice) => {
    let filteredData = data;

    filteredData = filteredData.filter(listing => listing.Price >= minPrice && listing.Price <= maxPrice);

    renderListings(filteredData);
};
