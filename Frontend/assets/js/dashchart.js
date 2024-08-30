const url="http://localhost/realhouse_api/Listings/getNumRooms.php"

const authToken = sessionStorage.getItem('token');
    const fetchRooms = async () => {
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `${authToken}`
            }
        });
        const data = await res.json();
        if (data.status == true) {
          console.log(data)
          const rooms=data.data
          const htype=data.houseType
          let countrent=0
          let countsale=0
          for (let i = 0; i < htype.length; i++) {
            if (htype[i] === 'Rent') {
              countrent ++;
            }
            else if(htype[i] === 'Sale') {
              countsale ++;
            }
          }

          const roomsInt = rooms.map(room => parseInt(room));

          const countRooms = roomsInt.reduce((acc, room) => {
            if (room in acc) {
              acc[room]++;
            } else {
              acc[room] = 1;
            }
            return acc;
          }, {});

          const countRoomsValues = Object.values(countRooms);
                      
         const roomsLabel=[]
         roomsInt.forEach(room=>{
          const label=room
          if (roomsLabel.includes(label)){
            return
          }
          roomsLabel.push(label)
          }
          )

          const barChartData = {
            labels: roomsLabel,
            datasets: [{
              label: 'Rooms',
              data: countRoomsValues, 
              backgroundColor: 'rgba(75, 192, 192, 0.2)', 
              borderColor: 'rgba(75, 192, 192, 1)', 
              borderWidth: 1,
            }],
          }; 

          const barChartCanvas = document.getElementById('barChart');
          const barChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: {
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
            },
          });

          const pieChartCanvas = document.getElementById('pieChart');
          const pieChart = new Chart(pieChartCanvas, {
            type: 'pie',
            data: {
              
              labels: ["Rent", "Sale"],
              datasets: [{
                data: [countrent, countsale],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', '#d9d9ff'],
              }],
            },
          });

        }
    }
  

  fetchRooms()

  // const download_CSV=document.getElementById('downloadCSV')
// async function fetchListings() {
//   downloadCSV.addEventListener('click', async (e) => {
//     e.preventDefault()
//   const downloadUrl="http://localhost/realhouse_api/Functions/download_report.php"
//   const res = await fetch(downloadUrl, {
//     method:'GET',
//     headers: {
//       'Content-Type': 'application/json',
//       'Authorization': `${authToken}`
//     }
//   });
//   // const blob=await res.blob()
//   const data=await res.json()
//   if (!res.ok){
//     alert('Failed to download CSV file')
//     return
//   }
  
//   else{
//     // data comes as a json object and we need to convert it to csv
//     console.log(data)
//     const csv = convertToCSV(data.data);


//   }
  
//   } 
//   )
// }

  
//   function convertToCSV(data) {  
//     const headers = Object.keys(data[0]);  
//     const csvRows = [  
//         headers.join(','), // Header row  
//         ...data.map(row => headers.map(header => escapeCSV(row[header] || '')).join(',')) // Data rows  
//     ];  
//     return csvRows.join('\n');  
// }  

// function escapeCSV(value) {  
//     if (typeof value === 'string') {  
//         return '"' + value.replace(/"/g, '""') + '"';  
//     }  
//     return value;  
// }  

// function getCSV(data) {  
//     const csv = convertToCSV(data);  
//     const blob = new Blob([csv], { type: 'text/csv' });  
//     const url = window.URL.createObjectURL(blob);  
//     const a = document.createElement('a');  
//     a.setAttribute('href', url);  
//     a.setAttribute('download', 'listings.csv');  
//     document.body.appendChild(a);  
//     a.click();  
//     document.body.removeChild(a);  
// }  

// // Example usage:  
// fetchListings().then(data => {  
//     if (data.status) {  
//         getCSV(data.data);  
//     }  
// });



// async function fetchListings() {  
//   try {  
//       const response = await fetch('http://localhost/realhouse_api/Functions/download_report.php', {  
//           method: 'GET', // or 'POST' depending on your API  
//           headers: {  
//               'Authorization':`${authToken}`, // Adjust as necessary  
//           },  
//       });  

//       const data = await response.json();  

//       if (data.status) {  
//           const csv = convertToCSV(data.data);  
//           getCSV(csv);  
//       } else {  
//           console.error(data.message);  
//       }  
//   } catch (error) {  
//       console.error('Error fetching listings:', error);  
//   }  
// }  

// function convertToCSV(jsonData) {  
//   const headers = Object.keys(jsonData[0]);  
//   const csvRows = [  
//       headers.join(','), // Header row  
//       ...jsonData.map(row => headers.map(header => JSON.stringify(row[header] || '')).join(',')) // Data rows  
//   ];  
//   return csvRows.join('\n');  
// }  

// function getCSV(csv) {  
//   const blob = new Blob([csv], { type: 'text/csv' });  
//   const url = window.URL.createObjectURL(blob);  
//   const a = document.createElement('a');  
//   a.setAttribute('href', url);  
//   a.setAttribute('download', 'listings.csv');  
//   document.body.appendChild(a);  
//   a.click();  
//   document.body.removeChild(a);  
// }  

// // Call the function to fetch listings  
// fetchListings();


// fetchListings();
