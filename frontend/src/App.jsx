import React, { useState } from 'react';
import './App.css';

// Importăm componentele pe care tocmai le-am creat
import BrowseRooms from './components/BrowseRooms';
import MyBookings from './components/MyBookings';

export default function App() {
  const [activeView, setActiveView] = useState('browse');

  return (
      <div>
        <h1>Hotel Room Booking System</h1>

        <nav className="nav-bar">
          <button
              className={`nav-btn ${activeView === 'browse' ? 'active' : ''}`}
              onClick={() => setActiveView('browse')}
          >
            Browse Rooms
          </button>
          <button
              className={`nav-btn ${activeView === 'bookings' ? 'active' : ''}`}
              onClick={() => setActiveView('bookings')}
          >
            My Bookings
          </button>
        </nav>

        {/* Componentele rulate curat aici */}
        {activeView === 'browse' ? <BrowseRooms /> : <MyBookings />}
      </div>
  );
}