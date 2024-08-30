// Listing detail

// Get the listing id from the URL
const urlParams = new URLSearchParams(window.location.search);
const listing_id = urlParams.get('listing_id');
console.log(listing_id);

