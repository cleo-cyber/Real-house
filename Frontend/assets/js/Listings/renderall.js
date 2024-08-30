const url = 'http://localhost/realhouse_api/Listings/getAll.php';
const delUrl = 'http://localhost/realhouse_api/Listings/Deletelisting.php/';
const authToken = sessionStorage.getItem('token');

const listings = document.querySelector("table tbody");
const filter = document.querySelector(".filter select");

const fetchAllListings = async () => {
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': authToken
            }
        });

        const data = await response.json();

        if (data.status === true) {
            renderListings(data.data);
            setupFilter(data.data);
        }
    } catch (error) {
        console.error('Error fetching listings:', error);
    }
};

const renderListings = (listingsData) => {
    listings.innerHTML = listingsData.map(element => `
        <tr>
            <td>${element.listing_id}</td>
            <td>${element.title}</td>
            <td>${element.house_type}</td>
            <td>${element.Price}</td>
            <td>${element.Location}</td>
            <td>${element.status}</td>
            <td class="property_image"><img src="./assets/uploads/${element.file_url}" alt=""></td>
            <td data-id=${element.listing_id}><a href="EditListing.html?listing_id=${element.listing_id}"><i class='edit bx bx-edit'></i></a></td>
            <td data-id=${element.listing_id} class='deletedata'><i class='delete bx bx-trash-alt'></i></td>
        </tr>
    `).join('');

    setupDeleteButtons();
};

const setupFilter = (listingsData) => {
    filter.addEventListener('change', () => {
        const filterValue = filter.value;
        let filteredData = listingsData;

        if (filterValue === "Rent") {
            filteredData = listingsData.filter(listing => listing.house_type === "Rent");
        } else if (filterValue === "Sale") {
            filteredData = listingsData.filter(listing => listing.house_type === "Sale");
        }

        renderListings(filteredData);
    });
};

const setupDeleteButtons = () => {
    document.querySelectorAll('.deletedata').forEach(button => {
        console.log(button);

        button.addEventListener('click', async (event) => {
            const listingId = event.currentTarget.dataset.id;
            try {
                const deleteResponse = await fetch(`${delUrl}${listingId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': authToken
                    }
                });

                const deleteData = await deleteResponse.json();
                if (deleteData.success) {
                    // Re-fetch listings to reflect the deletion
                    fetchAllListings();
                } else {
                    console.error('Deletion failed:', deleteData.message);
                }
            } catch (error) {
                console.error('Error during deletion:', error);
                // Log the response text for debugging
                const text = await error.response.text();
                console.error('Response text:', text);
            }
        });
    });
};

document.addEventListener('DOMContentLoaded', fetchAllListings);
