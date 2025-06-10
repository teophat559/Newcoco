import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Judges = () => {
  const [judges, setJudges] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [showEditModal, setShowEditModal] = useState(false);
  const [editingJudge, setEditingJudge] = useState(null);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [judgeToDelete, setJudgeToDelete] = useState(null);
  const [showAssignModal, setShowAssignModal] = useState(false);
  const [judgeToAssign, setJudgeToAssign] = useState(null);
  const [contests, setContests] = useState([]);
  const [selectedContests, setSelectedContests] = useState([]);

  useEffect(() => {
    fetchJudges();
    fetchContests();
  }, [currentPage, searchTerm, statusFilter]);

  const fetchJudges = async () => {
    try {
      const params = new URLSearchParams({
        page: currentPage,
        search: searchTerm,
        status: statusFilter !== 'all' ? statusFilter : ''
      });
      const response = await axios.get(`/api/admin/judges?${params}`);
      setJudges(response.data.judges);
      setTotalPages(response.data.total_pages);
    } catch (error) {
      setError('Failed to fetch judges');
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchContests = async () => {
    try {
      const response = await axios.get('/api/admin/contests');
      setContests(response.data.contests);
    } catch (error) {
      console.error('Error fetching contests:', error);
    }
  };

  const handleSearch = (e) => {
    setSearchTerm(e.target.value);
    setCurrentPage(1);
  };

  const handleStatusFilter = (e) => {
    setStatusFilter(e.target.value);
    setCurrentPage(1);
  };

  const handleEditClick = (judge) => {
    setEditingJudge({ ...judge });
    setShowEditModal(true);
  };

  const handleUpdateJudge = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');

    try {
      await axios.put(`/api/admin/judges/${editingJudge.id}`, editingJudge);
      setSuccess('Judge updated successfully');
      setShowEditModal(false);
      setEditingJudge(null);
      fetchJudges();
    } catch (error) {
      setError('Failed to update judge');
      console.error('Error:', error);
    }
  };

  const handleDeleteClick = (judge) => {
    setJudgeToDelete(judge);
    setShowDeleteModal(true);
  };

  const handleDeleteConfirm = async () => {
    try {
      await axios.delete(`/api/admin/judges/${judgeToDelete.id}`);
      setSuccess('Judge deleted successfully');
      setShowDeleteModal(false);
      setJudgeToDelete(null);
      fetchJudges();
    } catch (error) {
      setError('Failed to delete judge');
      console.error('Error:', error);
    }
  };

  const handleAssignClick = (judge) => {
    setJudgeToAssign(judge);
    setSelectedContests(judge.contests.map(c => c.id));
    setShowAssignModal(true);
  };

  const handleAssignSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');

    try {
      await axios.post(`/api/admin/judges/${judgeToAssign.id}/assign`, {
        contest_ids: selectedContests
      });
      setSuccess('Contests assigned successfully');
      setShowAssignModal(false);
      setJudgeToAssign(null);
      setSelectedContests([]);
      fetchJudges();
    } catch (error) {
      setError('Failed to assign contests');
      console.error('Error:', error);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setEditingJudge(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleContestSelect = (contestId) => {
    setSelectedContests(prev => {
      if (prev.includes(contestId)) {
        return prev.filter(id => id !== contestId);
      } else {
        return [...prev, contestId];
      }
    });
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-6">Judge Management</h1>

      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {error}
        </div>
      )}

      {success && (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {success}
        </div>
      )}

      {/* Filters */}
      <div className="bg-white shadow-md rounded-lg p-6 mb-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">Search</label>
            <input
              type="text"
              value={searchTerm}
              onChange={handleSearch}
              placeholder="Search by name or email..."
              className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">Status</label>
            <select
              value={statusFilter}
              onChange={handleStatusFilter}
              className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>

      {/* Judges Table */}
      <div className="bg-white shadow-md rounded-lg overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Judge
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Assigned Contests
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Reviews Completed
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {judges.map((judge) => (
              <tr key={judge.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center">
                    <div className="flex-shrink-0 h-10 w-10">
                      <img
                        className="h-10 w-10 rounded-full"
                        src={judge.avatar || 'https://via.placeholder.com/40'}
                        alt=""
                      />
                    </div>
                    <div className="ml-4">
                      <div className="text-sm font-medium text-gray-900">{judge.name}</div>
                      <div className="text-sm text-gray-500">{judge.email}</div>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    judge.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  }`}>
                    {judge.status}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <div className="text-sm text-gray-900">
                    {judge.contests.map(contest => contest.title).join(', ')}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {judge.reviews_completed}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    onClick={() => handleEditClick(judge)}
                    className="text-indigo-600 hover:text-indigo-900 mr-4"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleAssignClick(judge)}
                    className="text-green-600 hover:text-green-900 mr-4"
                  >
                    Assign Contests
                  </button>
                  <button
                    onClick={() => handleDeleteClick(judge)}
                    className="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
        <div className="flex-1 flex justify-between sm:hidden">
          <button
            onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
            disabled={currentPage === 1}
            className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </button>
          <button
            onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
            disabled={currentPage === totalPages}
            className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </button>
        </div>
        <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p className="text-sm text-gray-700">
              Showing page <span className="font-medium">{currentPage}</span> of{' '}
              <span className="font-medium">{totalPages}</span>
            </p>
          </div>
          <div>
            <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
                disabled={currentPage === 1}
                className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Previous
              </button>
              <button
                onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
                disabled={currentPage === totalPages}
                className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>

      {/* Edit Judge Modal */}
      {showEditModal && (
        <div className="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
          <div className="bg-white rounded-lg p-6 max-w-md w-full">
            <h2 className="text-xl font-semibold mb-4">Edit Judge</h2>
            <form onSubmit={handleUpdateJudge}>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    type="text"
                    name="name"
                    value={editingJudge.name}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    type="email"
                    name="email"
                    value={editingJudge.email}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Status</label>
                  <select
                    name="status"
                    value={editingJudge.status}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
              </div>
              <div className="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  onClick={() => setShowEditModal(false)}
                  className="bg-white text-gray-700 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
                >
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Delete Confirmation Modal */}
      {showDeleteModal && (
        <div className="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
          <div className="bg-white rounded-lg p-6 max-w-md w-full">
            <h2 className="text-xl font-semibold mb-4">Delete Judge</h2>
            <p className="text-gray-600 mb-4">
              Are you sure you want to delete {judgeToDelete.name}? This action cannot be undone.
            </p>
            <div className="flex justify-end space-x-3">
              <button
                onClick={() => setShowDeleteModal(false)}
                className="bg-white text-gray-700 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                onClick={handleDeleteConfirm}
                className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Assign Contests Modal */}
      {showAssignModal && (
        <div className="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
          <div className="bg-white rounded-lg p-6 max-w-md w-full">
            <h2 className="text-xl font-semibold mb-4">Assign Contests</h2>
            <form onSubmit={handleAssignSubmit}>
              <div className="space-y-4">
                <div>
                  <p className="text-sm text-gray-600">Judge: {judgeToAssign.name}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Select Contests</label>
                  <div className="mt-2 space-y-2 max-h-60 overflow-y-auto">
                    {contests.map(contest => (
                      <div key={contest.id} className="flex items-center">
                        <input
                          type="checkbox"
                          id={`contest-${contest.id}`}
                          checked={selectedContests.includes(contest.id)}
                          onChange={() => handleContestSelect(contest.id)}
                          className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label
                          htmlFor={`contest-${contest.id}`}
                          className="ml-2 block text-sm text-gray-900"
                        >
                          {contest.title}
                        </label>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
              <div className="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  onClick={() => setShowAssignModal(false)}
                  className="bg-white text-gray-700 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
                >
                  Assign Contests
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Judges;