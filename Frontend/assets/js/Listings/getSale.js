const saleurl='http://localhost/realhouse_api/Listings/getAll.php'
const listings=document.querySelector('.listings')
const saleToken = sessionStorage.getItem('token');


const fetchRent = async () => {
    const res = await fetch(saleurl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `${saleToken}`
        }
    });

    const data = await res.json();

    const filteredData = data.data.filter(listing => listing.listing_type === 'Sale');
    console.log(filteredData);
    renderListings(filteredData);


   
}

const renderListings = (listingsData) => {
    // Create a listing card for each listing
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


};

fetchRent();

