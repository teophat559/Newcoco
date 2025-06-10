import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Dashboard = () => {
  const [stats, setStats] = useState({
    totalUsers: 0,
    totalContests: 0,
    totalContestants: 0,
    totalVotes: 0
  });

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await axios.get('/api/admin/stats');
      setStats(response.data);
    } catch (error) {
      console.error('Failed to fetch stats:', error);
    }
  };

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">Admin Dashboard</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Users</h3>
          <p className="text-3xl font-bold text-indigo-600">{stats.totalUsers}</p>
          <Link to="/admin/users" className="text-indigo-500 hover:text-indigo-700">
            View all users →
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Contests</h3>
          <p className="text-3xl font-bold text-indigo-600">{stats.totalContests}</p>
          <Link to="/admin/contests" className="text-indigo-500 hover:text-indigo-700">
            View all contests →
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Contestants</h3>
          <p className="text-3xl font-bold text-indigo-600">{stats.totalContestants}</p>
          <Link to="/admin/contestants" className="text-indigo-500 hover:text-indigo-700">
            View all contestants →
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Votes</h3>
          <p className="text-3xl font-bold text-indigo-600">{stats.totalVotes}</p>
          <Link to="/admin/votes" className="text-indigo-500 hover:text-indigo-700">
            View vote history →
          </Link>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-bold mb-4">Quick Actions</h2>
          <div className="space-y-4">
            <Link
              to="/admin/contests/new"
              className="block w-full text-center py-2 px-4 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            >
              Create New Contest
            </Link>
            <Link
              to="/admin/contestants/new"
              className="block w-full text-center py-2 px-4 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            >
              Add New Contestant
            </Link>
            <Link
              to="/admin/notifications/new"
              className="block w-full text-center py-2 px-4 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            >
              Send Notification
            </Link>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-bold mb-4">Recent Activity</h2>
          <div className="space-y-4">
            {/* Add recent activity list here */}
            <p className="text-gray-500">Loading recent activity...</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;