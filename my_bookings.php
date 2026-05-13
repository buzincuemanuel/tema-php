<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { margin-bottom: 20px; }
        .booking-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .btn-cancel { background-color: #ff4d4d; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .btn-edit { background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; margin-right: 5px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Browse Rooms</a> |
    <a href="my_bookings.php">My Bookings</a>
</nav>

<h1>My Bookings</h1>

<div id="bookings-list">
    <p>Loading your bookings...</p>
</div>

<div id="edit-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <h2>Edit Booking</h2>
    <form id="edit-form">
        <input type="hidden" id="edit-id">

        <div style="display: flex; flex-direction: column; margin-bottom: 10px;">
            <label for="edit-client-name">Your Name:</label>
            <input type="text" id="edit-client-name" required>
        </div>

        <div style="display: flex; flex-direction: column; margin-bottom: 10px;">
            <label for="edit-start-date">Start Date:</label>
            <input type="date" id="edit-start-date" required>
        </div>

        <div style="display: flex; flex-direction: column; margin-bottom: 15px;">
            <label for="edit-end-date">End Date:</label>
            <input type="date" id="edit-end-date" required>
        </div>

        <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Save Changes</button>
        <button type="button" onclick="closeEditModal()" style="background-color: #ccc; border: none; padding: 5px 10px; cursor: pointer;">Cancel</button>
    </form>
</div>

<script>
    function fetchBookings() {
        const listContainer = document.getElementById("bookings-list");

        fetch('get_bookings.php')
            .then(response => response.json())
            .then(data => {
                listContainer.innerHTML = '';
                if (data.length === 0) {
                    listContainer.innerHTML = '<p>No bookings found.</p>';
                    return;
                }

                data.forEach(b => {
                    const div = document.createElement('div');
                    div.className = 'booking-card';
                    div.innerHTML = `
                        <p><strong>Hotel:</strong> ${b.hotel} (Room #${b.room_id})</p>
                        <p><strong>Client:</strong> ${b.client_name}</p>
                        <p><strong>Period:</strong> ${b.start_date} to ${b.end_date}</p>
                        <button class="btn-edit" onclick="openEditModal(${b.id}, '${b.client_name}', '${b.start_date}', '${b.end_date}')">Edit</button>
                        <button class="btn-cancel" onclick="cancelBooking(${b.id})">Cancel Reservation</button>
                    `;
                    listContainer.appendChild(div);
                });
            });
    }

    document.addEventListener("DOMContentLoaded", () => {
        fetchBookings();

        document.getElementById('edit-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const updatedData = {
                id: document.getElementById('edit-id').value,
                client_name: document.getElementById('edit-client-name').value,
                start_date: document.getElementById('edit-start-date').value,
                end_date: document.getElementById('edit-end-date').value
            };

            fetch('update_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updatedData)
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        closeEditModal();
                        fetchBookings();
                    }
                })
                .catch(error => {
                    console.error("Edit error:", error);
                    alert("Network error while saving data.");
                });
        });
    });

    function cancelBooking(id) {
        if (confirm("Are you sure you want to cancel this booking?")) {
            fetch('cancel_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        fetchBookings();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("An error occurred while canceling the reservation.");
                });
        }
    }

    function openEditModal(id, name, start, end) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-client-name').value = name;
        document.getElementById('edit-start-date').value = start;
        document.getElementById('edit-end-date').value = end;

        document.getElementById('edit-modal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }
</script>

</body>
</html>