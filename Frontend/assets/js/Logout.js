const logoutbtn=document.querySelector('.logoutbtn');
// Check if the user is logged in
if(sessionStorage.getItem('token')===null){
    window.location.href="Login.html";
}

// Logout
logoutbtn.addEventListener('click',()=>{
    sessionStorage.removeItem('token');
    window.location.href="Login.html";
}
);
