const listings_csv = document.querySelector('.listing_csv');
const user_csv = document.querySelector('.users_csv');
const analyses_csv = document.querySelector('.analyses_csv');
const logs_csv = document.querySelector('.logs_csv');

const reportToken = sessionStorage.getItem('token');

// decode token
const decoded = atob(reportToken);

// split token
const splitToken = decoded.split('|');

// get role
const role = splitToken[2];

// check role
console.log(role);


async function fetchListings() {  
    try {  
        const response = await fetch('http://localhost/realhouse_api/Functions/download_report.php', {  
            method: 'GET', 
            headers: {  
                'Authorization': `${reportToken}`, 
            },  
        });  
  
        if (response.ok) {
            const blob = await response.blob();
            downloadCSV(blob, 'listings.csv');
        } else {  
            const data = await response.json();  
            console.error(data.message);  
        }  
    } catch (error) {  
        console.error('Error fetching listings:', error);  
    }  
  }  
  
  function downloadCSV(blob, filename) {  
    const url = window.URL.createObjectURL(blob);  
    const a = document.createElement('a');  
    a.style.display = 'none';
    a.href = url;  
    a.download = filename || 'report.csv';  
    document.body.appendChild(a);  
    a.click();  
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);  
  }  
  
  listings_csv.addEventListener('click', fetchListings);


//   user

async function fetchUsers() {
    try {
        const response = await fetch('http://localhost/realhouse_api/Functions/download_user_report.php', {
            method: 'GET',
            headers: {
                'Authorization': `${reportToken}`,
            },
        });

        if (response.ok) {
            const blob = await response.blob();
            downloadCSV(blob, 'users.csv');
        } else {
            const data = await response.json();
            console.error(data.message);
        }
    } catch (error) {
        console.error('Error fetching users:', error);
    }
}

if (role != 4) {
    // hide download buttons
    user_csv.style.display = 'none';
    analyses_csv.style.display = 'none';
    logs_csv.style.display = 'none';


}
user_csv.addEventListener('click', fetchUsers);

// logs
async function fetchLogs() {
    try {
        const response = await fetch('http://localhost/realhouse_api/Functions/download_logs.php', {
            method: 'GET',
            headers: {
                'Authorization': `${reportToken}`,
            },
        });

        if (response.ok) {
            const blob = await response.blob();
            downloadCSV(blob, 'logs.csv');
        } else {
            const data = await response.json();
            console.error(data.message);
        }
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
}

logs_csv.addEventListener('click', fetchLogs);