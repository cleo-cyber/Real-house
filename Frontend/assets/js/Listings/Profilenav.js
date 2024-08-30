const link_dropdown = document.querySelectorAll('.link_dropdown');
const menu_links_inner = document.querySelectorAll('.menu_links_inner');
const users=document.querySelector(".link_dropdown.users")
console.log(users)
const dtoken=sessionStorage.getItem('token')
if(dtoken){
    const decodedToken = atob(dtoken);
    const role = decodedToken.split('|')[2];
    console.log(role)
    
//  if role is not admin hide the users link
    if(role!=4){
        users.style.display="none"
    }

}

link_dropdown.forEach((link) => {
    link.addEventListener('click', () => {
        menu_links_inner.forEach((menu) => {
            menu.classList.toggle('open');
        });
    });
});

