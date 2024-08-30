const pred_url="http://localhost:3389/predict"

// King county data
const calcBtn=document.querySelector(".calc")   
const bedrooms=document.querySelector("#bedrooms")
const bathrooms=document.querySelector("#bathrooms")
const floors=document.querySelector("#floors")
const waterfront=document.querySelector("#waterfront")
const view=document.querySelector("#view")
const condition=document.querySelector("#condition")
const grade=document.querySelector("#grade")
const sqft_above=document.querySelector("#sqft_above")
const sqft_basement=document.querySelector("#sqft_basement")
const yr_built=document.querySelector("#yr_built")
const yr_renovated=document.querySelector("#yr_renovated")
const month_sold=document.querySelector("#month")
const sqft_living15 = document.querySelector("#sqft_living15")
const sqft_lot15 = document.querySelector("#sqft_lot15")
const sqft_lot=document.querySelector("#sqft_lot")
const sqft_living=document.querySelector("#sqft_living")
const year=document.querySelector("#Year")
const result=document.querySelector("#result")

const button = document.querySelector("button")


button.addEventListener("click",(e)=>{
    e.preventDefault()
    const user_data={
        bedrooms:bedrooms.value,
        bathrooms:bathrooms.value,
        sqft_living:sqft_living.value,
        sqft_lot:sqft_lot.value,
        floors:floors.value,
        waterfront:waterfront.value,
        view:view.value,
        condition:condition.value,
        grade:grade.value,
        sqft_above:sqft_above.value,
        sqft_basement:sqft_basement.value,
        yr_built:yr_built.value,
        yr_renovated:yr_renovated.value,
        sqft_living15:sqft_living15.value,
        sqft_lot15:sqft_lot15.value,
        month:month_sold.value,
        year:year.value
    }
    // convert data to float64
    for (const [key, value] of Object.entries(user_data)) {
        user_data[key] = parseFloat(value)
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
        console.log(price["prediction"][0])  
        
        let html=`<div class="card">
        
        <div class="card">
            <div class="card-text">
                <p>Price Estimate: ${price["prediction"][0]}</p>
            </div>
        </div>
    </div>`
    result.innerHTML=html

    })

}
)
