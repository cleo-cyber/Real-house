// const userUrl = 'http://localhost/realhouse_api/User/getUserInfo.php';
// const userToken = sessionStorage.getItem('token');



// const fetchUser = async () => {
//     try {
//         const res = await fetch(userUrl, {
//             method: 'GET',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'Authorization': userToken ? `${userToken}` : ''
//             }
//         });

//         if (!res.ok) {
//             throw new Error(`HTTP error! status: ${res.status}`);
//         }

//         const contentType = res.headers.get("content-type");
//         if (contentType && contentType.includes("application/json")) {
//             const data = await res.json();
//             console.log(data);
//         } else {
//             const text = await res.text();
//             throw new Error(`Unexpected content-type: ${contentType}. Response: ${text}`);
//         }
//     } catch (error) {
//         console.error('Error fetching user data:', error);
//     }
// };

// fetchUser();
