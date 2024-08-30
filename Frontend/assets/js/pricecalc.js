const pred_url="http://localhost:8080/predict"

const calcBtn=document.querySelector(".calc")
const numrooms=document.querySelector("#num-rooms")
const parking_space=document.querySelector("#area")
const h_location=document.querySelector("#location")
const title=document.querySelector("#title")
const b_rooms=document.querySelector("#bathroom")
const result=document.querySelector("#result")





calcBtn.addEventListener("click",(e)=>{
    e.preventDefault()
    // check if the user has entered all the required fields change the color of the input field to red if the user has not entered the required fields
    if (numrooms.value=="" || parking_space.value=="" || h_location.value=="" || title.value=="" || b_rooms.value==""){
        if (numrooms.value==""){
            numrooms.style.borderColor="red"
        }
        if (parking_space.value==""){
            parking_space.style.borderColor="red"
        }
        if (h_location.value==""){
            h_location.style.borderColor="red"
        }
        if (title.value==""){
            title.style.borderColor="red"
        }
        if (b_rooms.value==""){
            b_rooms.style.borderColor="red"
        }
        return
    }
    numrooms.style.borderColor="green"
    parking_space.style.borderColor="green"
    h_location.style.borderColor="green"
    title.style.borderColor="green"
    b_rooms.style.borderColor="green"

    const user_data={
        loc:h_location.value,
        bedroom:numrooms.value,
        parking_space:parking_space.value,
        title:title.value,
        bathroom:b_rooms.value

    }
    console.log(user_data,"userdata")
    fetch(pred_url,{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify(user_data)
    }).then(res=>res.json())
    .then(data=>{
        console.log(JSON.stringify(data)); 
        const dt=JSON.stringify(data)
        const price=JSON.parse(dt)
        // limit decimanls to 2 in price
        const price_limit = price["prediction"].toFixed(2)


        console.log(price["prediction"])  
        const html=`<p>The price of a house with ${user_data.bedroom} bedrooms, ${user_data.bathroom} bathrooms, ${user_data.parking_space} area in ${user_data.loc} is <br> <b>ksh ${price_limit}</b></p>`
        


    result.innerHTML=html

    })

}
)
