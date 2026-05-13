<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Room Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .filter-section { display: flex; gap: 15px; align-items: center; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }

        #booking-modal {
            display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: white; padding: 20px; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        #room-list { display: flex; flex-wrap: wrap; gap: 20px; }
        .room-card { border: 1px solid #ddd; padding: 15px; width: 200px; cursor: pointer; }
        .room-card:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>

<h1>Hotel Room Booking System</h1>

<nav style="margin-bottom: 20px;">
    <a href="index.php" style="text-decoration: none; font-weight: bold; color: blue;">Browse Rooms</a> |
    <a href="my_bookings.php" style="text-decoration: none; font-weight: bold; color: blue;">My Bookings</a>
</nav>

<div class="filter-section" id="filters">
    <div class="form-group">
        <label for="filter-hotel">Hotel:</label>
        <input type="text" id="filter-hotel" placeholder="Hotel name...">
    </div>

    <div class="form-group">
        <label for="filter-category">Category:</label>
        <select id="filter-category">
            <option value="">All Categories</option>
            <option value="Single">Single</option>
            <option value="Double">Double</option>
            <option value="Suite">Suite</option>
        </select>
    </div>

    <div class="form-group">
        <label for="filter-price">Max Price:</label>
        <input type="number" id="filter-price" placeholder="e.g. 300">
    </div>

    <button id="btn-apply-filters">Apply Filters</button>
</div>

<hr>

<div id="room-list">
    <p>Loading rooms...</p>
</div>

<div id="pagination" style="margin-top: 20px;">
    <button id="btn-prev-page">Previous</button>
    <span id="current-page">Page 1</span>
    <button id="btn-next-page">Next</button>
</div>

<div id="booking-modal">
    <h2>Book Room <span id="modal-room-name"></span></h2>

    <form id="booking-form">
        <input type="hidden" id="hidden-room-id">

        <div class="form-group">
            <label for="client-name">Your Name:</label>
            <input type="text" id="client-name" required>
        </div>
        <br>
        <div class="form-group">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date" required>
        </div>
        <br>
        <div class="form-group">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date" required>
        </div>
        <br>
        <button type="submit">Confirm Booking</button>
        <button type="button" id="btn-close-modal" onclick="closeBookingModal()">Cancel</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const roomListContainer = document.getElementById("room-list");
        let currentPage = 1;

        function fetchRooms() {
            const hotelFilter = document.getElementById('filter-hotel').value;
            const categoryFilter = document.getElementById('filter-category').value;
            const priceFilter = document.getElementById('filter-price').value;

            let url = `get_rooms.php?page=${currentPage}`;
            if (hotelFilter) url += `&hotel=${encodeURIComponent(hotelFilter)}`;
            if (categoryFilter) url += `&category=${encodeURIComponent(categoryFilter)}`;
            if (priceFilter) url += `&price=${encodeURIComponent(priceFilter)}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(roomsData => {
                    roomListContainer.innerHTML = '';

                    if(roomsData.error) {
                        roomListContainer.innerHTML = `<p>${roomsData.error}</p>`;
                        return;
                    }

                    if (roomsData.length === 0) {
                        roomListContainer.innerHTML = '<p>No rooms found for these filters/page.</p>';
                        return;
                    }

                    roomsData.forEach(room => {
                        const card = document.createElement('div');
                        card.className = 'room-card';
                        card.innerHTML = `
                            <h3>${room.hotel}</h3>
                            <p><strong>Category:</strong> ${room.category}</p>
                            <p><strong>Price:</strong> $${room.price} / night</p>
                            <button onclick="openBookingModal(${room.id}, '${room.hotel}')">Book Now</button>
                        `;
                        roomListContainer.appendChild(card);
                    });

                    document.getElementById('current-page').innerText = `Page ${currentPage}`;
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    roomListContainer.innerHTML = '<p>Error loading rooms.</p>';
                });
        }

        fetchRooms();

        document.getElementById('btn-next-page').addEventListener('click', () => {
            currentPage++;
            fetchRooms();
        });

        document.getElementById('btn-prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchRooms();
            }
        });

        document.getElementById('btn-apply-filters').addEventListener('click', () => {
            currentPage = 1;
            fetchRooms();
        });

        const bookingForm = document.getElementById('booking-form');
        bookingForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const hiddenId = document.getElementById('hidden-room-id').value;
            const clientName = document.getElementById('client-name').value;
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;

            const requestData = {
                id_camera: hiddenId,
                nume_client: clientName,
                data_start: startDate,
                data_final: endDate
            };

            fetch('book_room.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestData)
            })
                .then(response => response.json())
                .then(data =>{
                    alert(data.message);
                    if(data.status === 'success') {
                        closeBookingModal();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Error while sending data.");
                })
        });
    });

    function openBookingModal(roomId, hotelName){
        const modal = document.getElementById("booking-modal");
        modal.style.display = 'block';
        document.getElementById('hidden-room-id').value = roomId;
        document.getElementById('modal-room-name').innerText = hotelName;
    }

    function closeBookingModal(){
        document.getElementById("booking-modal").style.display = 'none';
        document.getElementById('booking-form').reset();
    }
</script>

</body>
</html>