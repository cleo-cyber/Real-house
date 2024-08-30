const url = 'http://localhost/realhouse_api/Listings/addListing.php';
const price = document.querySelector('.Price');
const houseLocation = document.querySelector('.Location');
const image = document.querySelector('.Image');
const title = document.querySelector('.Name');
const description = document.querySelector('.Description');
const amenities = document.querySelector('.Amenities');
const houseType = document.querySelector('.house_type');
const houseStatus = document.querySelector('.status');
const bedrooms = document.querySelector('.Bedrooms');
const baths = document.querySelector('.Baths');
const size = document.querySelector('.Size');
const submitBtn = document.querySelector('.SubmitBtn');

const authToken = sessionStorage.getItem('token');
submitBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    if (price.value === '' || houseLocation.value === '' || image.value === '' || title.value === '' || description.value === '' || amenities.options[amenities.selectedIndex].text === '' || houseType.options[houseType.selectedIndex].text === '' || houseStatus.options[houseStatus.selectedIndex].text === '') {
        console.log('Please fill in all fields.');
        return;
    }

    
    const data = {
        title: title.value,
        price: price.value,
        images: image.files[0],
        description: description.value,
        amenities: amenities.value,
        location: houseLocation.value,
        house_type: houseType.options[houseType.selectedIndex].text,
        status: houseStatus.options[houseStatus.selectedIndex].text,
        bedrooms: bedrooms.value

    };
    console.log(data);

    const res = await fetch(url, {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json',
            Authorization: `${authToken}`
        }
    });
    const resdata = await res.json();
    if (resdata.status == true) {
        alert(resdata.message);}
    else {
        alert(resdata.message);
        // console.log("else part")
    }
    
});
