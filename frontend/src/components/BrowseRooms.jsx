import React, { useState, useEffect } from 'react';
import './BrowseRooms.css';

const API_BASE = 'http://localhost/lab4';

export default function BrowseRooms() {
    const [rooms, setRooms] = useState([]);
    const [page, setPage] = useState(1);
    const [filters, setFilters] = useState({ hotel: '', category: '', price: '' });
    const [activeFilters, setActiveFilters] = useState({ hotel: '', category: '', price: '' });

    const [bookingModal, setBookingModal] = useState({ isOpen: false, roomId: null, hotelName: '' });
    const [bookingForm, setBookingForm] = useState({ clientName: '', startDate: '', endDate: '' });

    useEffect(() => {
        fetchRooms();
    }, [page, activeFilters]);

    const fetchRooms = async () => {
        let url = `${API_BASE}/get_rooms.php?page=${page}`;
        if (activeFilters.hotel) url += `&hotel=${encodeURIComponent(activeFilters.hotel)}`;
        if (activeFilters.category) url += `&category=${encodeURIComponent(activeFilters.category)}`;
        if (activeFilters.price) url += `&price=${encodeURIComponent(activeFilters.price)}`;

        try {
            const response = await fetch(url);
            const data = await response.json();
            setRooms(data.error ? [] : data);
        } catch (err) {
            console.error("Eroare la încărcarea camerelor.");
        }
    };

    const handleApplyFilters = () => {
        setPage(1);
        setActiveFilters(filters);
    };

    const handleBookSubmit = async (e) => {
        e.preventDefault();
        const requestData = {
            id_camera: bookingModal.roomId,
            nume_client: bookingForm.clientName,
            data_start: bookingForm.startDate,
            data_final: bookingForm.endDate
        };

        try {
            const response = await fetch(`${API_BASE}/book_room.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestData)
            });
            const data = await response.json();
            alert(data.message);

            if (data.status === 'success') {
                setBookingModal({ isOpen: false, roomId: null, hotelName: '' });
                setBookingForm({ clientName: '', startDate: '', endDate: '' });
            }
        } catch (err) {
            alert("Eroare la trimiterea datelor.");
        }
    };

    return (
        <div>
            <div className="filter-section">
                <div className="form-group">
                    <label>Hotel:</label>
                    <input type="text" value={filters.hotel} onChange={e => setFilters({...filters, hotel: e.target.value})} placeholder="Ex: Hilton" />
                </div>
                <div className="form-group">
                    <label>Category:</label>
                    <select value={filters.category} onChange={e => setFilters({...filters, category: e.target.value})}>
                        <option value="">All Categories</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>
                <div className="form-group">
                    <label>Max Price:</label>
                    <input type="number" value={filters.price} onChange={e => setFilters({...filters, price: e.target.value})} placeholder="Ex: 300" />
                </div>
                <button className="btn-primary" onClick={handleApplyFilters}>Apply Filters</button>
            </div>
            <hr />

            <div className="room-list">
                {rooms.length === 0 ? <p>No rooms found.</p> : null}

                {rooms.map(room => (
                    <div key={room.id} className="room-card">
                        <h3>{room.hotel}</h3>
                        <p><strong>Category:</strong> {room.category}</p>
                        <p><strong>Price:</strong> ${room.price} / night</p>
                        <button className="btn-success" onClick={() => setBookingModal({ isOpen: true, roomId: room.id, hotelName: room.hotel })}>Book Now</button>
                    </div>
                ))}
            </div>

            <div style={{ marginTop: '20px' }}>
                <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>Previous</button>
                <span style={{ margin: '0 15px', fontWeight: 'bold' }}>Page {page}</span>
                <button onClick={() => setPage(p => p + 1)}>Next</button>
            </div>

            {bookingModal.isOpen && (
                <div className="modal-overlay">
                    <div className="modal-content">
                        <h2>Book Room at {bookingModal.hotelName}</h2>
                        <form onSubmit={handleBookSubmit}>
                            <div className="form-group">
                                <label>Your Name:</label>
                                <input type="text" required value={bookingForm.clientName} onChange={e => setBookingForm({...bookingForm, clientName: e.target.value})} />
                            </div>
                            <div className="form-group">
                                <label>Start Date:</label>
                                <input type="date" required value={bookingForm.startDate} onChange={e => setBookingForm({...bookingForm, startDate: e.target.value})} />
                            </div>
                            <div className="form-group">
                                <label>End Date:</label>
                                <input type="date" required value={bookingForm.endDate} onChange={e => setBookingForm({...bookingForm, endDate: e.target.value})} />
                            </div>
                            <div className="modal-actions">
                                <button type="submit" className="btn-success">Confirm Booking</button>
                                <button type="button" className="btn-danger" onClick={() => setBookingModal({ isOpen: false, roomId: null, hotelName: '' })}>Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}