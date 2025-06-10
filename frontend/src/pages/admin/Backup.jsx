import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Backup = () => {
  const [backups, setBackups] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [backupInProgress, setBackupInProgress] = useState(false);
  const [restoreInProgress, setRestoreInProgress] = useState(false);

  useEffect(() => {
    fetchBackups();
  }, []);

  const fetchBackups = async () => {
    try {
      const response = await axios.get('/api/admin/backups');
      setBackups(response.data);
    } catch (error) {
      setError('Failed to fetch backups');
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateBackup = async () => {
    setBackupInProgress(true);
    setError('');
    setSuccess('');

    try {
      await axios.post('/api/admin/backups');
      setSuccess('Backup created successfully');
      fetchBackups();
    } catch (error) {
      setError('Failed to create backup');
      console.error('Error:', error);
    } finally {
      setBackupInProgress(false);
    }
  };

  const handleRestoreBackup = async (backupId) => {
    if (!window.confirm('Are you sure you want to restore this backup? This will overwrite current data.')) {
      return;
    }

    setRestoreInProgress(true);
    setError('');
    setSuccess('');

    try {
      await axios.post(`/api/admin/backups/${backupId}/restore`);
      setSuccess('Backup restored successfully');
    } catch (error) {
      setError('Failed to restore backup');
      console.error('Error:', error);
    } finally {
      setRestoreInProgress(false);
    }
  };

  const handleDeleteBackup = async (backupId) => {
    if (!window.confirm('Are you sure you want to delete this backup?')) {
      return;
    }

    try {
      await axios.delete(`/api/admin/backups/${backupId}`);
      setSuccess('Backup deleted successfully');
      fetchBackups();
    } catch (error) {
      setError('Failed to delete backup');
      console.error('Error:', error);
    }
  };

  const handleDownloadBackup = async (backupId) => {
    try {
      const response = await axios.get(`/api/admin/backups/${backupId}/download`, {
        responseType: 'blob'
      });

      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `backup-${backupId}.zip`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      setError('Failed to download backup');
      console.error('Error:', error);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-6">Database Backup</h1>

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

      {/* Create Backup Button */}
      <div className="mb-6">
        <button
          onClick={handleCreateBackup}
          disabled={backupInProgress}
          className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
        >
          {backupInProgress ? 'Creating Backup...' : 'Create New Backup'}
        </button>
      </div>

      {/* Backups List */}
      <div className="bg-white shadow-md rounded-lg overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Backup ID
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created At
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Size
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {backups.map((backup) => (
              <tr key={backup.id}>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {backup.id}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(backup.created_at).toLocaleString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {backup.size}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    backup.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {backup.status}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    onClick={() => handleDownloadBackup(backup.id)}
                    className="text-indigo-600 hover:text-indigo-900 mr-4"
                  >
                    Download
                  </button>
                  <button
                    onClick={() => handleRestoreBackup(backup.id)}
                    disabled={restoreInProgress}
                    className="text-green-600 hover:text-green-900 mr-4"
                  >
                    {restoreInProgress ? 'Restoring...' : 'Restore'}
                  </button>
                  <button
                    onClick={() => handleDeleteBackup(backup.id)}
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

      {/* Backup Information */}
      <div className="mt-8 bg-white shadow-md rounded-lg p-6">
        <h2 className="text-xl font-semibold mb-4">Backup Information</h2>
        <div className="space-y-4">
          <p className="text-gray-600">
            <strong>Last Backup:</strong>{' '}
            {backups.length > 0
              ? new Date(backups[0].created_at).toLocaleString()
              : 'No backups available'}
          </p>
          <p className="text-gray-600">
            <strong>Total Backups:</strong> {backups.length}
          </p>
          <p className="text-gray-600">
            <strong>Storage Used:</strong>{' '}
            {backups.reduce((total, backup) => total + backup.size_bytes, 0).toLocaleString()} bytes
          </p>
        </div>
      </div>
    </div>
  );
};

export default Backup;