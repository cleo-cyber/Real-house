const listings=document.querySelector("table tbody")
const filter=document.querySelector(".filter select")
const edit = document.querySelector(".edit")
const urls='http://localhost/realhouse_api/User/getUsers.php'
const usrToken = sessionStorage.getItem('token');
const fetchData = async () => {
    const res = await fetch(urls, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `${usrToken}`
        }
    });

    const data = await res.json();
    // console.log(data);
    // render users with role tenant only
    const tenants = data.users.filter(user => user.role === 'Realtor');

    // User info
    if (data.status == true) {
        let html = "";
        tenants.forEach(element => {
            html += `<tr>
                <td>${element.User_id}</td>
                <td>${element.Firstname}</td>
                <td>${element.LastName}</td>
                <td>${element.role}</td>
                <td>${element.Email}</td>
                <td data-id=${element.User_id}><a href="EditUser.html/${element.User_id}"><i class='edit bx bx-edit '></i></a></td>
                <td data-id=${element.User_id} class='deletedata'><i class='delete bx bx-trash-alt'></i></td>
            </tr>`;
        });
        listings.innerHTML = html;
    }
    else {
        console.log(data.message);
    }
}
fetchData();

