// Get listing details
const detail_token = sessionStorage.getItem('token');
const all_listings=document.querySelector(".all_listings")
const predict_page=document.querySelector(".predict_page")
const rent = document.querySelector(".rent")
const sale=document.querySelector(".sale")
const predict = document.querySelector(".predict")

all_listings.addEventListener("click",(e)=>{
    e.preventDefault()
    window.location.href="Listing.html"
}
)
predict.addEventListener("click",(e)=>{
    e.preventDefault()
    window.location.href="priceprediction/index.html"
}
)

sale.addEventListener("click",(e)=>{
    e.preventDefault()
    window.location.href="sale.html"
}
)



rent.addEventListener("click",(e)=>{
    e.preventDefault()
    window.location.href="rent.html"
}
)

predict_page.addEventListener("click",(e)=>{
    e.preventDefault()
    window.location.href="/priceprediction/index.html"
}
)

    document.addEventListener('click', (e) => {
        e.preventDefault();
        const body = document.querySelector('body');
        // body.classList.add('overflow-hidden');
        // if (e.target.classList.contains('single_list')) {
        //     const listing_id = e.target
        //     console.log('Clicked listing ID:', listing_id);
        // }
        let data_id=document.querySelectorAll(".listing span.single_list")
        data_id.forEach((item)=>{
            item.addEventListener("click",(e)=>{
                let listing_id=item.getAttribute("data-id")
                window.location.href=`details.html?listing_id=${listing_id}`
            })

        }
        )
                                
    });

    
    