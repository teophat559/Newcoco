import React, { useState, useEffect } from 'react';
import axios from 'axios';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js';
import { Line, Bar, Pie } from 'react-chartjs-2';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
);

const Reports = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [reportData, setReportData] = useState({
    userStats: {},
    contestStats: {},
    voteStats: {},
    revenueStats: {}
  });
  const [dateRange, setDateRange] = useState({
    start_date: '',
    end_date: ''
  });

  useEffect(() => {
    fetchReportData();
  }, [dateRange]);

  const fetchReportData = async () => {
    try {
      const params = new URLSearchParams(dateRange);
      const response = await axios.get(`/api/admin/reports?${params}`);
      setReportData(response.data);
    } catch (error) {
      setError('Failed to fetch report data');
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleDateRangeChange = (e) => {
    const { name, value } = e.target;
    setDateRange(prev => ({
      ...prev,
      [name]: value
    }));
  };

  // User Growth Chart
  const userGrowthData = {
    labels: reportData.userStats?.growth?.labels || [],
    datasets: [
      {
        label: 'New Users',
        data: reportData.userStats?.growth?.data || [],
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
      }
    ]
  };

  // Contest Participation Chart
  const contestParticipationData = {
    labels: reportData.contestStats?.participation?.labels || [],
    datasets: [
      {
        label: 'Contestants',
        data: reportData.contestStats?.participation?.data || [],
        backgroundColor: 'rgba(54, 162, 235, 0.5)'
      }
    ]
  };

  // Vote Distribution Chart
  const voteDistributionData = {
    labels: reportData.voteStats?.distribution?.labels || [],
    datasets: [
      {
        data: reportData.voteStats?.distribution?.data || [],
        backgroundColor: [
          'rgba(255, 99, 132, 0.5)',
          'rgba(54, 162, 235, 0.5)',
          'rgba(255, 206, 86, 0.5)',
          'rgba(75, 192, 192, 0.5)'
        ]
      }
    ]
  };

  if (loading) return <div>Loading...</div>;
  if (error) return <div className="text-red-500">{error}</div>;

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-6">Reports & Analytics</h1>

      {/* Date Range Filter */}
      <div className="bg-white shadow-md rounded-lg p-6 mb-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">Start Date</label>
            <input
              type="date"
              name="start_date"
              value={dateRange.start_date}
              onChange={handleDateRangeChange}
              className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">End Date</label>
            <input
              type="date"
              name="end_date"
              value={dateRange.end_date}
              onChange={handleDateRangeChange}
              className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Users</h3>
          <p className="text-3xl font-bold text-indigo-600">
            {reportData.userStats?.total || 0}
          </p>
          <p className="text-sm text-gray-500">
            {reportData.userStats?.growth_rate || 0}% growth
          </p>
        </div>
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700">Active Contests</h3>
          <p className="text-3xl font-bold text-indigo-600">
            {reportData.contestStats?.active || 0}
          </p>
          <p className="text-sm text-gray-500">
            {reportData.contestStats?.total || 0} total contests
          </p>
        </div>
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Votes</h3>
          <p className="text-3xl font-bold text-indigo-600">
            {reportData.voteStats?.total || 0}
          </p>
          <p className="text-sm text-gray-500">
            {reportData.voteStats?.average_per_contest || 0} per contest
          </p>
        </div>
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700">Revenue</h3>
          <p className="text-3xl font-bold text-indigo-600">
            ${reportData.revenueStats?.total || 0}
          </p>
          <p className="text-sm text-gray-500">
            {reportData.revenueStats?.growth_rate || 0}% growth
          </p>
        </div>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* User Growth Chart */}
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700 mb-4">User Growth</h3>
          <Line data={userGrowthData} />
        </div>

        {/* Contest Participation Chart */}
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700 mb-4">Contest Participation</h3>
          <Bar data={contestParticipationData} />
        </div>

        {/* Vote Distribution Chart */}
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700 mb-4">Vote Distribution</h3>
          <Pie data={voteDistributionData} />
        </div>

        {/* Additional Statistics */}
        <div className="bg-white shadow-md rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-700 mb-4">Additional Statistics</h3>
          <div className="space-y-4">
            <div>
              <p className="text-sm text-gray-500">Average Contest Duration</p>
              <p className="text-lg font-semibold">
                {reportData.contestStats?.average_duration || 0} days
              </p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Most Active Time</p>
              <p className="text-lg font-semibold">
                {reportData.voteStats?.most_active_time || 'N/A'}
              </p>
            </div>
            <div>
              <p className="text-sm text-gray-500">User Retention Rate</p>
              <p className="text-lg font-semibold">
                {reportData.userStats?.retention_rate || 0}%
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Reports;