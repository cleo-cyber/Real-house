const rentUrl='http://localhost/realhouse_api/Listings/getAll.php'


const rentToken = sessionStorage.getItem('token');

const listings=document.querySelector('.listings')




const fetchAll = async () => {
    const res = await fetch(rentUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `${rentToken}`
        }
    });

     data = await res.json();
    console.log(data);

    if (data.status === true) {
        // Filter data to get only rentals
        const filteredData = data.data.filter(listing => listing.listing_type === 'Rent');
        console.log(filteredData);
        renderListings(filteredData);
        
        
    }
};

const renderListings = (listingsData) => {

    listingsData.forEach(listing => {
        const card = document.createElement('div');
        card.classList.add('listing');
        card.innerHTML = `
            <div class="listing_img">
                <img src="./assets/uploads/${listing.file_url}" alt="">
                <div class="listing_txt">
                    <div class="price">
                        <h2>$ ${listing.Price}</h2>
                    </div>
                    <div class="txt">
                        <h2>${listing.title}</h2>
                        <p>${listing.Location}</p>
                        <p>${listing.bedrooms} Bedrooms | ${listing.baths} Baths | ${listing.size} Sqft</p>
                    </div>
                </div>
            </div>
        `;
        listings.appendChild(card);
    }

    );
}

fetchAll();


