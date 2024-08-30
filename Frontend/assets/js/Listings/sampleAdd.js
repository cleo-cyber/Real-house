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
const size=document.querySelector('.Size');
const submitBtn = document.querySelector('.SubmitBtn');

// Prevent text input in number fields except for numbers


const authToken = sessionStorage.getItem('token');
submitBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    const fileData=[];
    // if (price.value === '' || houseLocation.value === '' || image.files === '' || title.value === '' || description.value === '' || amenities.value === '' || houseType.value === '' || houseStatus.value === '') {
    //     console.log('Please fill in all fields.');
    //     return;
    // }

    data={
        title:title.value,
        price:price.value,
        image:image.files,
        description:description.value,
        amenities:amenities.value,
        location:houseLocation.value,
        house_type:houseType.options[houseType.selectedIndex].text,
        status:houseStatus.options[houseStatus.selectedIndex].text,
        bedrooms:bedrooms.value,
        baths:baths.value,
        size:size.value

    }
    const files=data.image;
    // console.log(files);
    for (let i=0; files.length>i; i++){
        const file=files[i];
        const reader=new FileReader();
        
        reader.onload=function(e){
             fileData.push({
                name:file.name,
                type:file.type,
                // size:file.size,
                data:reader.result.split(',')[1]
            });
            // console.log(fileData);
            if(fileData.length===files.length){
                data.image=fileData;
                console.log(fileData);
                sendToServer(data)
            }
            
        }
        reader.readAsDataURL(file);



    }

});

async function sendToServer(data){
    const url = 'http://localhost/realhouse_api/Listings/addListing.php';
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
}