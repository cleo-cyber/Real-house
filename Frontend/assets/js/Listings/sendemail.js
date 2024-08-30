const submitBtn = document.querySelector('.SubmitBtn'); // Fixed class name to follow convention
console.log(submitBtn);
// Listing ID from URL
const url_Params = new URLSearchParams(window.location.search);
const listing_Id = url_Params.get('listing_id');
console.log(listing_Id);

submitBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    const name = document.querySelector('#name').value;
    const email = document.querySelector('#email').value;
    const message = document.querySelector('#message').value;
    const phone = document.querySelector('#phone').value;
    const data = {
        name: name,
        email: email,
        message: message,
        phone: phone
    };
    console.log(data);
    try {
        const url_email = 'http://localhost/realhouse_api/Functions/send_email.php?listing_id=' + listing_Id; 
        const response = await fetch(url_email, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const responseData = await response.json();
        if (responseData.status) {
            alert('Email sent successfully');
            // console.log('Email sent successfully:', responseData);
            // Optionally clear form fields or show success message
        } else {
            console.error('Error sending email:', responseData);
            alert('Failed to send email: ' + responseData.message); // Fixed alert message
        }
    } catch (error) {
        console.error('Error sending email:', error.message);
        alert('Failed to send email. Please try again later.');
    }
});
