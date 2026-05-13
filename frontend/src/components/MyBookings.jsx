import React, { useState, useEffect } from 'react';
import './MyBookings.css';

const API_BASE = 'http://localhost/lab4';

export default function MyBookings() {
    const [bookings, setBookings] = useState([]);
    const [editModal, setEditModal] = useState({ isOpen: false, data: {} });

    useEffect(() => {
        fetchBookings();
    }, []);

    const fetchBookings = async () => {
        try {
            const response = await fetch(`${API_BASE}/get_bookings.php`);
            const data = await response.json();
            setBookings(data);
        } catch (err) {
            console.error(err);
        }
    };

    const cancelBooking = async (id) => {
        if (window.confirm("Are you sure you want to cancel this booking?")) {
            try {
                const response = await fetch(`${API_BASE}/cancel_booking.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await response.json();
                alert(data.message);
                if (data.status === 'success') fetchBookings();
            } catch (error) {
                alert("Eroare la ștergere.");
            }
        }
    };

    const handleEditSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await fetch(`${API_BASE}/update_booking.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(editModal.data)
            });
            const data = await response.json();
            alert(data.message);
            if (data.status === 'success') {
                setEditModal({ isOpen: false, data: {} });
                fetchBookings();
            }
        } catch (error) {
            alert("Eroare de rețea.");
        }
    };

    return (
        <div>
            <h2>My Bookings</h2>
            {bookings.length === 0 ? <p>No bookings found.</p> : null}

            {bookings.map(b => (
                <div key={b.id} className="booking-card">
                    <p><strong>Hotel:</strong> {b.hotel} (Room #{b.room_id})</p>
                    <p><strong>Client:</strong> {b.client_name}</p>
                    <p><strong>Period:</strong> {b.start_date} to {b.end_date}</p>

                    <div style={{ marginTop: '10px' }}>
                        <button className="btn-primary" style={{ marginRight: '10px' }}
                                onClick={() => setEditModal({ isOpen: true, data: { id: b.id, client_name: b.client_name, start_date: b.start_date, end_date: b.end_date } })}>
                            Edit
                        </button>
                        <button className="btn-danger" onClick={() => cancelBooking(b.id)}>Cancel Reservation</button>
                    </div>
                </div>
            ))}

            {editModal.isOpen && (
                <div className="modal-overlay">
                    <div className="modal-content">
                        <h2>Edit Booking</h2>
                        <form onSubmit={handleEditSubmit}>
                            <div className="form-group">
                                <label>Your Name:</label>
                                <input type="text" required value={editModal.data.client_name}
                                       onChange={e => setEditModal({...editModal, data: {...editModal.data, client_name: e.target.value}})} />
                            </div>
                            <div className="form-group">
                                <label>Start Date:</label>
                                <input type="date" required value={editModal.data.start_date}
                                       onChange={e => setEditModal({...editModal, data: {...editModal.data, start_date: e.target.value}})} />
                            </div>
                            <div className="form-group">
                                <label>End Date:</label>
                                <input type="date" required value={editModal.data.end_date}
                                       onChange={e => setEditModal({...editModal, data: {...editModal.data, end_date: e.target.value}})} />
                            </div>
                            <div className="modal-actions">
                                <button type="submit" className="btn-success">Save Changes</button>
                                <button type="button" onClick={() => setEditModal({ isOpen: false, data: {} })}>Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}