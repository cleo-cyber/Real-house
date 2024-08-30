document.addEventListener('DOMContentLoaded', (e) => {
    
const userprof= document.querySelector('.userprof');
const settings= document.querySelector('.settings');
const account_main= document.querySelector('.account_main');
const account_link= document.querySelector('.account_link');
const security_link= document.querySelector('.security_link');
settings.addEventListener('click', (e) => {
    e.preventDefault();
    userprof.classList.toggle('show');
    
});


const userUrl = 'http://localhost/realhouse_api/User/getUserInfo.php';
const userToken = sessionStorage.getItem('token');



const fetchUser = async () => {
    try {
        const res = await fetch(userUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': userToken ? `${userToken}` : ''
            }
        });

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            const data = await res.json();
            console.log(data);

            // Display user data
            const user = data.data;
            const name = user.Firstname + ' ' + user.LastName;
            const email = user.Email;
            const password = user.Password.substring(0, 3) + '****';
            



        showAcount(user);

        // Update profile
        const updateBtn = document.querySelector('.profile_image');
        updateBtn.addEventListener('click', (e) => {
            e.preventDefault();
            showUpdate(user);
        });

        // cancel update
        
        // Delete account
        const deleteBtn = document.querySelector('.delete_account button');
        deleteBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const userId = e.target.getAttribute('data-id');
            const confirmDelete = confirm('Are you sure you want to delete your account? This action cannot be undone.');
            if (confirmDelete) {
                const deleteUrl = `http://localhost/realhouse_api/User/deleteUser.php?user_id=${userId}`;
                const res = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': userToken ? `${userToken}` : ''
                    }
                });
                const data = await res.json();
                if (data.status) {
                    alert('Account deleted successfully.');
                    sessionStorage.removeItem('token');
                    window.location.href = 'Login.html';
                } else {
                    alert('Error deleting account. Please try again.');
                }
            }
        });
        } else {
            const text = await res.text();
            throw new Error(`Unexpected content-type: ${contentType}. Response: ${text}`);
        }
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
};

fetchUser();
function showAcount(user){
    // shortening the password for security reasons 
    const password = user.Password.substring(0, 3) + '****';
    account_main.innerHTML = `
    <div class="account_header" id="acc_header>
    <p>Manage your account Information</p>
    </div>
    <div class="account_profile">
    <h2 class="profile">Profile</h2>
    <div class="profile_information">
        <div class="profile_image">
            <div class="image_profile">
                <img src="./assets/images/cleo.jpg" alt="Profile Image">
                <p>${user.Firstname + ' ' + user.LastName}</p>
            </div>
            <div class="next_icon">

                <i class='bx bx-right-arrow-alt'></i>
            </div>
        </div>
    </div>
    <div class="name_details">
        <h2>Email</h2>
        <p>${user.Email}</p>
    </div>
    <div class="name_details">
        <h2>Password</h2>
        <p>${password}</p>
    </div>
</div>
<div class="security_sect" id="userprofile">
<div class="security_heder">
    <h1>Security</h1>
    <p class="manage_acc">Manage your account security preferences</p>
</div>
<div class="delete_account">
    <p>Permanently delete your account</p>
    <button data-id=${user.User_id}>Delete Account</button>
</div>
</div>
`;
// change update cursor to pointer
const updateBtn = document.querySelector('.profile_image');
updateBtn.style.cursor = 'pointer';

}

function showUpdate(user){
    const password = user.Password.substring(0, 3) + '****';
    const profile_image = document.querySelector('.profile_image');
    profile_image.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('clicked');
        account_main.innerHTML = `
        <div class="url">
        <a href="#acc_header">Account</a> <span> > </span> <a href="UserProfile.html">Update profile</a>
    </div>
    <div class="update">
        <div class="update_header">
            <h1>Update Profile</h1>
        </div>
        <div class="update_form">
            <form action="">
                <div class="form_group">
                    <label for="name">Firstname</label>
                    <input type="text" name="FirstName" id="FirstName" placeholder=${user.Firstname }>
                    <label for="name">Firstname</label>

                    <input type="text" name="LastName" id="LastName" placeholder=${user.LastName}>


                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder=${user.Email}>
                    <label for="Password">Password</label>
                    <input type="password" name="password" id="password" placeholder=${password}>
                    <label for="profile image">Profile Image</label>
                    <input type="file" name="image" class="Image">
                    <div class="edit_btns">
                        <button type="button" class="cancel">Cancel</button>
                        <button type="submit" data-id=${user.User_id} class="update_button">Update</button>
                    </div>

                    </div>
            </form>

    </div>
    </div>`;
    // change cursor to pointer
    
// cancel update
const cancelBtn = document.querySelector('.cancel');
cancelBtn.addEventListener('click', (e) => {
    e.preventDefault();
    showAcount(user);
    // Keep the event listener for the update button
    showUpdate(user);
}
);const update_button = document.querySelector('.update_button');
const dataId = update_button.getAttribute('data-id');

update_button.addEventListener('click', async (e) => {
    e.preventDefault();

    const FirstName = document.querySelector('#FirstName').value.trim();
    const LastName = document.querySelector('#LastName').value.trim();
    const email = document.querySelector('#email').value.trim();
    const password = document.querySelector('#password').value.trim();
    const userId = dataId;

    const image = document.querySelector('.Image');
    
    updateData = {
        first_name: FirstName,
        last_name: LastName,
        email: email,
        password: password,
        user_id: userId,
        image: ''
    };
    ;

    const reader = new FileReader();
    reader.onload = function(e) {
       const fileData={
            name: image.files[0].name,
            data: reader.result.split(',')[1]
        };
        updateData.image = fileData;        
    }
    reader.readAsDataURL(image.files[0]);
    // console.log(updateData);
    

    const updateUrl = `http://localhost/realhouse_api/User/UpdateUser.php?user_id=${userId}`;
    
    try {
        const res = await fetch(updateUrl,{
            method:'PUT',
            headers: {
                'Authorization': userToken ? `${userToken}` : ''
            },
            body:JSON.stringify(updateData)


        })

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const data = await res.json();
        
        if (data.status) {
            alert('Profile updated successfully.');
            showAcount(data.data);
        } else {
            alert('Error updating profile. Please try again.');
            console.log(data);
        }
    } catch (error) {
        console.error('Error updating profile:', error);
        alert('Error updating profile. Please try again.');
    }
});

    
});
}

});

