const url='http://localhost/realhouse_api/User/login.php'

const email=document.querySelector(".loginEmail")
const password=document.querySelector(".loginPassword")
const loginBtn=document.querySelector(".loginBTN")

loginBtn.addEventListener("click",async(e)=>{
    e.preventDefault()
    const emailValue = email.value.trim();
    const passwordValue = password.value.trim();

        if (emailValue === '' || passwordValue === '') {
        alert('Please fill in all fields.');
        return; 
    }
    const data={
        email:emailValue,
        password:passwordValue
    }
    const res=await fetch(url,
        {
            method:'POST',
            body:JSON.stringify(data),
            headers:{
                'Content-Type':'application/json'
            }
        }

    )
    const resdata=await res.json()
   if(resdata.status==true){
    localStorage.setItem('user',JSON.stringify(resdata.data))
    window.location.href="index.html"

// After successful login
const token = resdata.token;
if (token) {
    sessionStorage.setItem('token', token);
    // check role to decide where to redirect to
    const decodedToken = atob(token);
    const role = decodedToken.split('|')[2];
    if (role == 1) {
        window.location.href = "Dashboard.html";
    } else if(role==2) {
        window.location.href = "index.html";
    }
    else if(role==4){
        window.location.href = "Dashboard.html";
    }
    else{
        window.location.href = "Login.html";
    }
history.replaceState(null, null, window.location.href);
window.addEventListener('popstate', function() {
    history.pushState(null, null, window.location.href);
}

);
}




    

    }
    else{
window.location.href="Login.html"
alert(resdata.message)   


}

})

