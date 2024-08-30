burger=document.querySelector('.burger')
navbar=document.querySelector('nav')
addlisting=document.querySelector('.addlisting')
dash_link=document.querySelector('.dashboard_link')
burger.addEventListener('click',()=>{
    navbar.classList.toggle('open-nav')
    burger.classList.toggle('toggle')
})

// const token = sessionStorage.getItem('token');
const roleCheckToken = sessionStorage.getItem('token');
const decoded=atob(roleCheckToken);
// console.log(decoded)

// get role
const role=decoded.split('|')[2];
// if (role==1){
//     addlisting.style.display='block'
// }
// else{
//     addlisting.style.display='none'
// }

if (role!=4 && role!=1){
    dash_link.style.display='none'  
}

// if (!token) {
//     window.location.href = "Login.html";
// } else {
//     const decodedToken = atob(token);
//     const expiry = parseInt(decodedToken.split('|')[1], 10); // Parsing expiry as an integer
//     // expiry in milliseconds

//     const expMili=expiry*1000;
//     const now=Date.now();
//     // console.log(expMili)


//     if(now>expMili){
//         sessionStorage.removeItem('token');
//         window.location.href = "Login.html";



//     }

// }
