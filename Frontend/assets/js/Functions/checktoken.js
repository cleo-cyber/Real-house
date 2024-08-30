const token = sessionStorage.getItem('token');

if (!token) {
    window.location.href = "Login.html";
} else {
    const decodedToken = atob(token);
    const expiry = parseInt(decodedToken.split('|')[1], 10); // Parsing expiry as an integer
    
    // expiry in milliseconds

    const expMili=expiry*1000;
    const now=Date.now();
    // console.log(expMili)


    if(now>expMili){
        sessionStorage.removeItem('token');
        window.location.href = "Login.html";
        



    }

}
