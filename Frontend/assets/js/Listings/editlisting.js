document.addEventListener('DOMContentLoaded', (event) => {
    const price = document.querySelector('.Price');
    const houseLocation = document.querySelector('.Location');
    const image = document.querySelector('.Image');
    const title = document.querySelector('.Name');
    const description = document.querySelector('.Description');
    const amenities = document.querySelector('.Amenities');
    const houseType = document.querySelector('.house_type');
    const houseStatus = document.querySelector('.status');
    const bedrooms = document.querySelector('.Bedrooms');
    const baths = document.querySelector('.Bathrooms');
    const size = document.querySelector('.Size');
    const submitBtn = document.querySelector('.SubmitBtn');
    const list_detail ='http://localhost/realhouse_api/Listings/getListingDetail.php';

    const urlParams = new URLSearchParams(window.location.search);
    const listingId = urlParams.get('listing_id');
    // fetch listing details and populate the form
    const fetchListingDetails = async () => {
        const url = `${list_detail}?listing_id=${listingId}`;
        const listToken = sessionStorage.getItem('token');
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `${listToken}`

                }
                
            });

            const data = await response.json();
            console.log(data);
            if (data.status === true) {
                const listing = data.data;
                price.value = listing.Price;
                houseLocation.value = listing.Location;
                title.value = listing.title;
                description.value = listing.Description;
                amenities.value = listing.amenities;
                houseType.value = listing.house_type;
                houseStatus.value = listing.status;
                bedrooms.value = listing.bedrooms;
                baths.value = listing.baths;
                size.value = listing.size;
            }
        }
        catch (error) {
            console.error('Error fetching listing details:', error);
        }
    }

    fetchListingDetails();


    const authToken = sessionStorage.getItem('token');
    submitBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        const fileData = [];

        if (!listingId) {
            alert('Listing ID is missing.');
            return;
        }

        const data = {
            listing_id: listingId,
            title: title.value,
            price: price.value,
            description: description.value,
            amenities: amenities.value,
            location: houseLocation.value,
            house_type: houseType.options[houseType.selectedIndex].text,
            status: houseStatus.options[houseStatus.selectedIndex].text,
            bedrooms: bedrooms.value,
            baths: baths.value,
            size: size.value,
            image: []
        };

        console.log(data);

        const files = image.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                fileData.push({
                    name: file.name,
                    type: file.type,
                    data: reader.result.split(',')[1]
                });

                if (fileData.length === files.length) {
                    data.image = fileData;
                    sendToServer(data);
                }
            };
            reader.readAsDataURL(file);
        }

        if (files.length === 0) {
            // If no new images are selected, just send the data without images
            sendToServer(data);
        }
    });

    async function sendToServer(data) {
        const url = 'http://localhost/realhouse_api/Listings/editListing.php';
        const res = await fetch(url, {
            method: 'PUT',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                Authorization: `${authToken}`
            }
        });
        const resData = await res.json();
        if (resData.status == true) {
            alert(resData.message);
        } else {
            alert(resData.message);
        }
    }
});
