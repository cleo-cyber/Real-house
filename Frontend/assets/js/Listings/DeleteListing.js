document.addEventListener('DOMContentLoaded', (e) => {
    setTimeout(() => {
        const deleteBtns = document.querySelectorAll(".deletedata");
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                handleDelete(e, btn); // Pass the event object and the button
            });
        });
    }, 1000);

    const handleDelete = (event, btn) => { 
        event.preventDefault(); 
        const listing_id = btn.dataset.id;
        if (confirm("Are you sure you want to delete this listing?")) {
            deleteData(listing_id);
        }
    };

    const deleteData = async (listing_id) => {
        const delurl = `http://localhost/realhouse_api/Listings/Deletelisting.php/${listing_id}`;
        const authToken = sessionStorage.getItem('token');
        const deleteid = {
            listing_id: listing_id
        };
    
        try {
            console.log(delurl);
            const res = await fetch(delurl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `${authToken}`
                },
                body: JSON.stringify(deleteid ) // Add the listing ID to the request body
            });
            // console.log(res.json());
    
            if (!res.ok) {
                throw new Error('Failed to delete listing');
            }
    
            // Parse the response as JSON
            const data = await res.json();
            console.log(data);
    
            if (data.status === true) {
                // Listing was successfully deleted
                alert("Listing was successfully deleted");
                // Reload the page to reflect the changes
                window.location.reload();
            } else {
                // Server returned an error message
                // Handle the error appropriately
                console.error("Error deleting data:", data.message);
                alert("Error deleting data: " + data.message);
            }
        } catch (error) {
            // Handle other errors, such as network issues
            console.error("Error deleting data:", error);
            alert("Error deleting data. Please try again later.");
        }
    };
    
});
