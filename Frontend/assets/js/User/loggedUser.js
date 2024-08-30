
const username=document.querySelector(".name")
const dash__image=document.querySelector(".dash__image")
const nameUrl='http://localhost/realhouse_api/User/getUserInfo.php'
const nameToken = sessionStorage.getItem('token');
const fetchName = async () => {
    const res = await fetch(nameUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `${nameToken}`
        }
    });

    const data = await res.json();
    if (data.status == true) {
                    // create p tag for name 
                    const namep = document.createElement('p');
                    
                    namep.textContent = data.data.Firstname + " " + data.data.LastName;
                    username.appendChild(namep);
        
                    // image tag for profile image
                    const img = document.createElement('img');
                    // default image if user has no image
                    img.src = `${data.data.file_url ? data.data.file_url : './assets/images/defaultprof.jpg'}`;
                    img.alt = 'Profile Image';
                    dash__image.appendChild(img);
    }
    else {
        console.log(data.message);
    }


    
    
    
}
fetchName();

