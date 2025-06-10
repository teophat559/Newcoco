import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { Spinner } from '../components/common/Spinner';
import { ThemePreview } from '../components/admin/ThemePreview';

// Validation schema
const settingsSchema = yup.object().shape({
  site_name: yup.string().required('Site name is required'),
  contact_email: yup.string().email('Invalid email').required('Contact email is required'),
  support_phone: yup.string().matches(/^[0-9+\-\s()]*$/, 'Invalid phone number'),
  max_file_size: yup.number().min(1, 'Must be at least 1MB').max(100, 'Maximum 100MB'),
  smtp_host: yup.string().when('smtp_enabled', {
    is: true,
    then: yup.string().required('SMTP host is required')
  }),
  smtp_port: yup.number().when('smtp_enabled', {
    is: true,
    then: yup.number().required('SMTP port is required')
  }),
  smtp_username: yup.string().when('smtp_enabled', {
    is: true,
    then: yup.string().required('SMTP username is required')
  }),
  smtp_password: yup.string().when('smtp_enabled', {
    is: true,
    then: yup.string().required('SMTP password is required')
  }),
  webhook_url: yup.string().url('Invalid URL').when('webhook_enabled', {
    is: true,
    then: yup.string().required('Webhook URL is required')
  }),
  rate_limit_requests: yup.number().when('rate_limit_enabled', {
    is: true,
    then: yup.number().min(1, 'Must be at least 1').required('Requests per period is required')
  }),
  rate_limit_period: yup.number().when('rate_limit_enabled', {
    is: true,
    then: yup.number().min(1, 'Must be at least 1 second').required('Period is required')
  })
});

const Settings = () => {
  const [settings, setSettings] = useState({
    site_name: '',
    site_description: '',
    contact_email: '',
    support_phone: '',
    max_file_size: '',
    allowed_file_types: '',
    registration_enabled: true,
    maintenance_mode: false,
    default_currency: 'USD',
    payment_gateway: 'stripe',
    smtp_host: '',
    smtp_port: '',
    smtp_username: '',
    smtp_password: '',
    smtp_encryption: 'tls',
    google_analytics_id: '',
    facebook_pixel_id: '',
    recaptcha_site_key: '',
    recaptcha_secret_key: '',
    default_language: 'en',
    available_languages: ['en'],
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    og_image: '',
    twitter_card: '',
    auto_backup_enabled: false,
    backup_frequency: 'daily',
    backup_retention: 7,
    backup_time: '00:00',
    backup_storage: 'local',
    backup_s3_bucket: '',
    backup_s3_region: '',
    backup_s3_key: '',
    backup_s3_secret: '',
    two_factor_enabled: false,
    password_expiry: 90,
    session_timeout: 30,
    max_login_attempts: 5,
    ip_whitelist: '',
    current_theme: 'default',
    custom_css: '',
    custom_js: '',
    stripe_public_key: '',
    stripe_secret_key: '',
    paypal_client_id: '',
    paypal_secret: '',
    razorpay_key_id: '',
    razorpay_key_secret: '',
    webhook_url: '',
    webhook_secret: '',
    webhook_events: [],
    rate_limit_enabled: false,
    rate_limit_requests: 100,
    rate_limit_period: 60,
    rate_limit_by_ip: true,
    rate_limit_by_user: true
  });

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [activeTab, setActiveTab] = useState('general');
  const [testEmail, setTestEmail] = useState('');
  const [testEmailLoading, setTestEmailLoading] = useState(false);
  const [testEmailResult, setTestEmailResult] = useState(null);
  const [showThemePreview, setShowThemePreview] = useState(false);
  const [previewTheme, setPreviewTheme] = useState(null);
  const [availableThemes, setAvailableThemes] = useState([
    { id: 'default', name: 'Default Theme' },
    { id: 'dark', name: 'Dark Theme' },
    { id: 'light', name: 'Light Theme' }
  ]);
  const [webhookEventTypes] = useState([
    'user.created',
    'user.updated',
    'user.deleted',
    'contest.created',
    'contest.updated',
    'contest.deleted',
    'vote.created',
    'payment.success',
    'payment.failed'
  ]);

  const [telegramSettings, setTelegramSettings] = useState({
    enabled: false,
    bot_token: '',
    chat_id: '',
    notifications: {
      new_user: true,
      new_contest: true,
      new_vote: true,
      payment: true,
      system: true
    }
  });
  const [telegramTestLoading, setTelegramTestLoading] = useState(false);
  const [telegramTestResult, setTelegramTestResult] = useState(null);

  const { register, handleSubmit, formState: { errors }, setValue, watch } = useForm({
    resolver: yupResolver(settingsSchema),
    defaultValues: settings
  });

  useEffect(() => {
    fetchSettings();
  }, []);

  const fetchSettings = async () => {
    try {
      setLoading(true);
      const response = await axios.get('/api/admin/settings');
      const data = response.data;
      setSettings(data);

      // Set form values
      Object.keys(data).forEach(key => {
        setValue(key, data[key]);
      });
    } catch (error) {
      console.error('Error:', error);
      toast.error('Failed to fetch settings');
      setError('Failed to fetch settings');
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    const newValue = type === 'checkbox' ? checked : value;

    setSettings(prev => ({
      ...prev,
      [name]: newValue
    }));

    setValue(name, newValue);
  };

  const onSubmit = async (data) => {
    try {
      setSaving(true);
      await axios.put('/api/admin/settings', data);
      setSuccess('Settings updated successfully');
      toast.success('Settings updated successfully');
    } catch (error) {
      console.error('Error:', error);
      const errorMessage = error.response?.data?.message || 'Failed to update settings';
      setError(errorMessage);
      toast.error(errorMessage);
    } finally {
      setSaving(false);
    }
  };

  const handleTestEmail = async () => {
    // Validate email
    if (!testEmail) {
      toast.error('Please enter an email address');
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(testEmail)) {
      toast.error('Please enter a valid email address');
      return;
    }

    try {
      setTestEmailLoading(true);
      setTestEmailResult(null);

      const response = await axios.post('/api/admin/settings/test-email', {
        email: testEmail,
        smtp_settings: {
          host: settings.smtp_host,
          port: settings.smtp_port,
          username: settings.smtp_username,
          password: settings.smtp_password,
          encryption: settings.smtp_encryption
        }
      });

      setTestEmailResult({
        success: true,
        message: 'Test email sent successfully',
        details: response.data
      });
      toast.success('Test email sent successfully');
    } catch (error) {
      console.error('Error:', error);
      setTestEmailResult({
        success: false,
        message: error.response?.data?.message || 'Failed to send test email',
        details: error.response?.data
      });
      toast.error('Failed to send test email');
    } finally {
      setTestEmailLoading(false);
    }
  };

  const handleTestWebhook = async () => {
    if (!settings.webhook_url) {
      toast.error('Please enter a webhook URL');
      return;
    }

    try {
      await axios.post('/api/admin/settings/test-webhook');
      toast.success('Test webhook sent successfully');
    } catch (error) {
      console.error('Error:', error);
      toast.error('Failed to send test webhook');
    }
  };

  const handleThemePreview = (themeId) => {
    setPreviewTheme(themeId);
    setShowThemePreview(true);
  };

  const handleCloseThemePreview = () => {
    setShowThemePreview(false);
    setPreviewTheme(null);
  };

  const handleTelegramTest = async () => {
    if (!telegramSettings.bot_token || !telegramSettings.chat_id) {
      toast.error('Please enter both Bot Token and Chat ID');
      return;
    }

    try {
      setTelegramTestLoading(true);
      setTelegramTestResult(null);

      const response = await axios.post('/api/admin/settings/test-telegram', {
        bot_token: telegramSettings.bot_token,
        chat_id: telegramSettings.chat_id
      });

      setTelegramTestResult({
        success: true,
        message: 'Test message sent successfully',
        details: response.data
      });
      toast.success('Test message sent successfully');
    } catch (error) {
      console.error('Error:', error);
      setTelegramTestResult({
        success: false,
        message: error.response?.data?.message || 'Failed to send test message',
        details: error.response?.data
      });
      toast.error('Failed to send test message');
    } finally {
      setTelegramTestLoading(false);
    }
  };

  const handleTelegramNotificationChange = (event) => {
    const { name, checked } = event.target;
    setTelegramSettings(prev => ({
      ...prev,
      notifications: {
        ...prev.notifications,
        [name]: checked
      }
    }));
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <Spinner size="lg" />
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <ToastContainer position="top-right" autoClose={5000} />

      <h1 className="text-3xl font-bold mb-6">System Settings</h1>

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

      <div className="bg-white shadow-md rounded-lg overflow-hidden">
        {/* Tabs */}
        <div className="border-b border-gray-200">
          <nav className="flex -mb-px overflow-x-auto">
            <button
              onClick={() => setActiveTab('general')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'general'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              General
            </button>
            <button
              onClick={() => setActiveTab('email')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'email'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Email
            </button>
            <button
              onClick={() => setActiveTab('payment')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'payment'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Payment
            </button>
            <button
              onClick={() => setActiveTab('integration')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'integration'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Integration
            </button>
            <button
              onClick={() => setActiveTab('language')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'language'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Language
            </button>
            <button
              onClick={() => setActiveTab('seo')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'seo'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              SEO
            </button>
            <button
              onClick={() => setActiveTab('backup')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'backup'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Backup
            </button>
            <button
              onClick={() => setActiveTab('security')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'security'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Security
            </button>
            <button
              onClick={() => setActiveTab('theme')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'theme'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Theme
            </button>
            <button
              onClick={() => setActiveTab('api')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'api'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              API
            </button>
            <button
              onClick={() => setActiveTab('webhook')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'webhook'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Webhook
            </button>
            <button
              onClick={() => setActiveTab('rate-limit')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'rate-limit'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Rate Limit
            </button>
            <button
              onClick={() => setActiveTab('maintenance')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'maintenance'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Maintenance
            </button>
            <button
              onClick={() => setActiveTab('notifications')}
              className={`py-4 px-6 text-sm font-medium whitespace-nowrap ${
                activeTab === 'notifications'
                  ? 'border-b-2 border-indigo-500 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Notifications
            </button>
          </nav>
        </div>

        {/* Settings Form */}
        <form onSubmit={handleSubmit(onSubmit)} className="p-6">
          {/* General Settings */}
          {activeTab === 'general' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Site Name</label>
                <input
                  type="text"
                  name="site_name"
                  value={settings.site_name}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Site Description</label>
                <textarea
                  name="site_description"
                  value={settings.site_description}
                  onChange={handleInputChange}
                  rows="3"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Contact Email</label>
                <input
                  type="email"
                  name="contact_email"
                  value={settings.contact_email}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Support Phone</label>
                <input
                  type="tel"
                  name="support_phone"
                  value={settings.support_phone}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Max File Size (MB)</label>
                <input
                  type="number"
                  name="max_file_size"
                  value={settings.max_file_size}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Allowed File Types</label>
                <input
                  type="text"
                  name="allowed_file_types"
                  value={settings.allowed_file_types}
                  onChange={handleInputChange}
                  placeholder="e.g., jpg,png,pdf,doc"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="registration_enabled"
                  checked={settings.registration_enabled}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Enable User Registration
                </label>
              </div>
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="maintenance_mode"
                  checked={settings.maintenance_mode}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Maintenance Mode
                </label>
              </div>
            </div>
          )}

          {/* Email Settings */}
          {activeTab === 'email' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">SMTP Host</label>
                <input
                  type="text"
                  name="smtp_host"
                  value={settings.smtp_host}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">SMTP Port</label>
                <input
                  type="number"
                  name="smtp_port"
                  value={settings.smtp_port}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">SMTP Username</label>
                <input
                  type="text"
                  name="smtp_username"
                  value={settings.smtp_username}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">SMTP Password</label>
                <input
                  type="password"
                  name="smtp_password"
                  value={settings.smtp_password}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">SMTP Encryption</label>
                <select
                  name="smtp_encryption"
                  value={settings.smtp_encryption}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                  <option value="none">None</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Test Email</label>
                <div className="mt-1 flex rounded-md shadow-sm">
                  <input
                    type="email"
                    value={testEmail}
                    onChange={(e) => setTestEmail(e.target.value)}
                    placeholder="Enter email to test"
                    className="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <button
                    type="button"
                    onClick={handleTestEmail}
                    className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Send Test
                  </button>
                </div>
              </div>

              {/* Test Email Section */}
              <div className="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Test Email Configuration</h3>

                <div className="space-y-4">
                  <div>
                    <label htmlFor="test-email" className="block text-sm font-medium text-gray-700">
                      Test Email Address
                    </label>
                    <div className="mt-1 flex rounded-md shadow-sm">
                      <input
                        type="email"
                        id="test-email"
                        value={testEmail}
                        onChange={(e) => setTestEmail(e.target.value)}
                        className="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter email address to test"
                      />
                      <button
                        type="button"
                        onClick={handleTestEmail}
                        disabled={testEmailLoading || !settings.smtp_enabled}
                        className={`ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ${
                          testEmailLoading || !settings.smtp_enabled ? 'opacity-50 cursor-not-allowed' : ''
                        }`}
                      >
                        {testEmailLoading ? (
                          <>
                            <Spinner size="sm" className="mr-2" />
                            Sending...
                          </>
                        ) : (
                          'Send Test Email'
                        )}
                      </button>
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                      Enter an email address to test your SMTP configuration
                    </p>
                  </div>

                  {/* Test Result */}
                  {testEmailResult && (
                    <div className={`mt-4 p-4 rounded-md ${
                      testEmailResult.success ? 'bg-green-50' : 'bg-red-50'
                    }`}>
                      <div className="flex">
                        <div className="flex-shrink-0">
                          {testEmailResult.success ? (
                            <svg className="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                            </svg>
                          ) : (
                            <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                            </svg>
                          )}
                        </div>
                        <div className="ml-3">
                          <h3 className={`text-sm font-medium ${
                            testEmailResult.success ? 'text-green-800' : 'text-red-800'
                          }`}>
                            {testEmailResult.message}
                          </h3>
                          {testEmailResult.details && (
                            <div className="mt-2 text-sm text-gray-700">
                              <pre className="whitespace-pre-wrap">
                                {JSON.stringify(testEmailResult.details, null, 2)}
                              </pre>
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* Payment Settings */}
          {activeTab === 'payment' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Default Currency</label>
                <select
                  name="default_currency"
                  value={settings.default_currency}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                  <option value="GBP">GBP</option>
                  <option value="JPY">JPY</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Payment Gateway</label>
                <select
                  name="payment_gateway"
                  value={settings.payment_gateway}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="stripe">Stripe</option>
                  <option value="paypal">PayPal</option>
                  <option value="razorpay">Razorpay</option>
                </select>
              </div>
            </div>
          )}

          {/* Integration Settings */}
          {activeTab === 'integration' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Google Analytics ID</label>
                <input
                  type="text"
                  name="google_analytics_id"
                  value={settings.google_analytics_id}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Facebook Pixel ID</label>
                <input
                  type="text"
                  name="facebook_pixel_id"
                  value={settings.facebook_pixel_id}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">reCAPTCHA Site Key</label>
                <input
                  type="text"
                  name="recaptcha_site_key"
                  value={settings.recaptcha_site_key}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">reCAPTCHA Secret Key</label>
                <input
                  type="text"
                  name="recaptcha_secret_key"
                  value={settings.recaptcha_secret_key}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>
          )}

          {/* Language Settings */}
          {activeTab === 'language' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Default Language</label>
                <select
                  name="default_language"
                  value={settings.default_language}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="en">English</option>
                  <option value="es">Spanish</option>
                  <option value="fr">French</option>
                  <option value="de">German</option>
                  <option value="it">Italian</option>
                  <option value="pt">Portuguese</option>
                  <option value="ru">Russian</option>
                  <option value="zh">Chinese</option>
                  <option value="ja">Japanese</option>
                  <option value="ko">Korean</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Available Languages</label>
                <div className="mt-2 space-y-2">
                  {['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ko'].map(lang => (
                    <div key={lang} className="flex items-center">
                      <input
                        type="checkbox"
                        name="available_languages"
                        value={lang}
                        checked={settings.available_languages.includes(lang)}
                        onChange={(e) => {
                          const { value, checked } = e.target;
                          setSettings(prev => ({
                            ...prev,
                            available_languages: checked
                              ? [...prev.available_languages, value]
                              : prev.available_languages.filter(l => l !== value)
                          }));
                        }}
                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <label className="ml-2 block text-sm text-gray-900">
                        {lang === 'en' ? 'English' :
                         lang === 'es' ? 'Spanish' :
                         lang === 'fr' ? 'French' :
                         lang === 'de' ? 'German' :
                         lang === 'it' ? 'Italian' :
                         lang === 'pt' ? 'Portuguese' :
                         lang === 'ru' ? 'Russian' :
                         lang === 'zh' ? 'Chinese' :
                         lang === 'ja' ? 'Japanese' :
                         'Korean'}
                      </label>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* SEO Settings */}
          {activeTab === 'seo' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Meta Title</label>
                <input
                  type="text"
                  name="meta_title"
                  value={settings.meta_title}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Meta Description</label>
                <textarea
                  name="meta_description"
                  value={settings.meta_description}
                  onChange={handleInputChange}
                  rows="3"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Meta Keywords</label>
                <input
                  type="text"
                  name="meta_keywords"
                  value={settings.meta_keywords}
                  onChange={handleInputChange}
                  placeholder="Separate keywords with commas"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Open Graph Image</label>
                <input
                  type="text"
                  name="og_image"
                  value={settings.og_image}
                  onChange={handleInputChange}
                  placeholder="URL to default social sharing image"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Twitter Card Type</label>
                <select
                  name="twitter_card"
                  value={settings.twitter_card}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="summary">Summary</option>
                  <option value="summary_large_image">Summary Large Image</option>
                  <option value="app">App</option>
                  <option value="player">Player</option>
                </select>
              </div>
            </div>
          )}

          {/* Backup Settings */}
          {activeTab === 'backup' && (
            <div className="space-y-6">
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="auto_backup_enabled"
                  checked={settings.auto_backup_enabled}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Enable Automatic Backups
                </label>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Backup Frequency</label>
                <select
                  name="backup_frequency"
                  value={settings.backup_frequency}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="hourly">Hourly</option>
                  <option value="daily">Daily</option>
                  <option value="weekly">Weekly</option>
                  <option value="monthly">Monthly</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Backup Time</label>
                <input
                  type="time"
                  name="backup_time"
                  value={settings.backup_time}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Backup Retention (days)</label>
                <input
                  type="number"
                  name="backup_retention"
                  value={settings.backup_retention}
                  onChange={handleInputChange}
                  min="1"
                  max="365"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Backup Storage</label>
                <select
                  name="backup_storage"
                  value={settings.backup_storage}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="local">Local Storage</option>
                  <option value="s3">Amazon S3</option>
                </select>
              </div>
              {settings.backup_storage === 's3' && (
                <>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">S3 Bucket</label>
                    <input
                      type="text"
                      name="backup_s3_bucket"
                      value={settings.backup_s3_bucket}
                      onChange={handleInputChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">S3 Region</label>
                    <input
                      type="text"
                      name="backup_s3_region"
                      value={settings.backup_s3_region}
                      onChange={handleInputChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">S3 Access Key</label>
                    <input
                      type="text"
                      name="backup_s3_key"
                      value={settings.backup_s3_key}
                      onChange={handleInputChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">S3 Secret Key</label>
                    <input
                      type="password"
                      name="backup_s3_secret"
                      value={settings.backup_s3_secret}
                      onChange={handleInputChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>
                </>
              )}
            </div>
          )}

          {/* Security Settings */}
          {activeTab === 'security' && (
            <div className="space-y-6">
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="two_factor_enabled"
                  checked={settings.two_factor_enabled}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Enable Two-Factor Authentication
                </label>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Password Expiry (days)</label>
                <input
                  type="number"
                  name="password_expiry"
                  value={settings.password_expiry}
                  onChange={handleInputChange}
                  min="0"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                <input
                  type="number"
                  name="session_timeout"
                  value={settings.session_timeout}
                  onChange={handleInputChange}
                  min="1"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                <input
                  type="number"
                  name="max_login_attempts"
                  value={settings.max_login_attempts}
                  onChange={handleInputChange}
                  min="1"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">IP Whitelist</label>
                <textarea
                  name="ip_whitelist"
                  value={settings.ip_whitelist}
                  onChange={handleInputChange}
                  placeholder="Enter IP addresses, one per line"
                  rows="3"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>
          )}

          {/* Theme Settings */}
          {activeTab === 'theme' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Current Theme</label>
                <select
                  name="current_theme"
                  value={settings.current_theme}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  {availableThemes.map(theme => (
                    <option key={theme.id} value={theme.id}>
                      {theme.name}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Custom CSS</label>
                <textarea
                  name="custom_css"
                  value={settings.custom_css}
                  onChange={handleInputChange}
                  rows="6"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Custom JavaScript</label>
                <textarea
                  name="custom_js"
                  value={settings.custom_js}
                  onChange={handleInputChange}
                  rows="6"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono"
                />
              </div>
              <div>
                <button
                  type="button"
                  onClick={() => handleThemePreview(settings.current_theme)}
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Preview Theme
                </button>
              </div>
            </div>
          )}

          {/* API Settings */}
          {activeTab === 'api' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Stripe Public Key</label>
                <input
                  type="text"
                  name="stripe_public_key"
                  value={settings.stripe_public_key}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Stripe Secret Key</label>
                <input
                  type="password"
                  name="stripe_secret_key"
                  value={settings.stripe_secret_key}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">PayPal Client ID</label>
                <input
                  type="text"
                  name="paypal_client_id"
                  value={settings.paypal_client_id}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">PayPal Secret</label>
                <input
                  type="password"
                  name="paypal_secret"
                  value={settings.paypal_secret}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Razorpay Key ID</label>
                <input
                  type="text"
                  name="razorpay_key_id"
                  value={settings.razorpay_key_id}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Razorpay Key Secret</label>
                <input
                  type="password"
                  name="razorpay_key_secret"
                  value={settings.razorpay_key_secret}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>
          )}

          {/* Webhook Settings */}
          {activeTab === 'webhook' && (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700">Webhook URL</label>
                <input
                  type="url"
                  name="webhook_url"
                  value={settings.webhook_url}
                  onChange={handleInputChange}
                  placeholder="https://your-domain.com/webhook"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Webhook Secret</label>
                <input
                  type="password"
                  name="webhook_secret"
                  value={settings.webhook_secret}
                  onChange={handleInputChange}
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Webhook Events</label>
                <div className="mt-2 space-y-2">
                  {webhookEventTypes.map(event => (
                    <div key={event} className="flex items-center">
                      <input
                        type="checkbox"
                        name="webhook_events"
                        value={event}
                        checked={settings.webhook_events.includes(event)}
                        onChange={(e) => {
                          const { value, checked } = e.target;
                          setSettings(prev => ({
                            ...prev,
                            webhook_events: checked
                              ? [...prev.webhook_events, value]
                              : prev.webhook_events.filter(e => e !== value)
                          }));
                        }}
                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <label className="ml-2 block text-sm text-gray-900">
                        {event}
                      </label>
                    </div>
                  ))}
                </div>
              </div>
              <div>
                <button
                  type="button"
                  onClick={handleTestWebhook}
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Send Test Webhook
                </button>
              </div>
            </div>
          )}

          {/* Rate Limit Settings */}
          {activeTab === 'rate-limit' && (
            <div className="space-y-6">
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="rate_limit_enabled"
                  checked={settings.rate_limit_enabled}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Enable Rate Limiting
                </label>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Requests per Period</label>
                <input
                  type="number"
                  name="rate_limit_requests"
                  value={settings.rate_limit_requests}
                  onChange={handleInputChange}
                  min="1"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Period (seconds)</label>
                <input
                  type="number"
                  name="rate_limit_period"
                  value={settings.rate_limit_period}
                  onChange={handleInputChange}
                  min="1"
                  className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="rate_limit_by_ip"
                  checked={settings.rate_limit_by_ip}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Limit by IP Address
                </label>
              </div>
              <div className="flex items-center">
                <input
                  type="checkbox"
                  name="rate_limit_by_user"
                  checked={settings.rate_limit_by_user}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900">
                  Limit by User Account
                </label>
              </div>
            </div>
          )}

          {/* Maintenance Settings */}
          {activeTab === 'maintenance' && (
            <div className="space-y-6">
              <div>
                <h3 className="text-lg font-medium text-gray-900">Cache Management</h3>
                <p className="mt-1 text-sm text-gray-500">
                  Clear the system cache to ensure you're seeing the most up-to-date content.
                </p>
                <button
                  type="button"
                  onClick={handleClearCache}
                  className="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Clear Cache
                </button>
              </div>

              <div>
                <h3 className="text-lg font-medium text-gray-900">Database Backup</h3>
                <p className="mt-1 text-sm text-gray-500">
                  Create a backup of your database. This will download a SQL file containing your database structure and data.
                </p>
                <button
                  type="button"
                  onClick={handleBackupDatabase}
                  className="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Backup Database
                </button>
              </div>
            </div>
          )}

          {/* Telegram Settings */}
          {activeTab === 'notifications' && (
            <div className="space-y-6">
              <div className="bg-white shadow sm:rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                  <h3 className="text-lg leading-6 font-medium text-gray-900">
                    Telegram Notifications
                  </h3>
                  <div className="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Configure Telegram bot to receive notifications about system events.</p>
                  </div>
                  <div className="mt-5">
                    <div className="space-y-6">
                      {/* Enable Telegram */}
                      <div className="flex items-start">
                        <div className="flex items-center h-5">
                          <input
                            type="checkbox"
                            checked={telegramSettings.enabled}
                            onChange={(e) => setTelegramSettings(prev => ({
                              ...prev,
                              enabled: e.target.checked
                            }))}
                            className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          />
                        </div>
                        <div className="ml-3 text-sm">
                          <label htmlFor="telegram-enabled" className="font-medium text-gray-700">
                            Enable Telegram Notifications
                          </label>
                          <p className="text-gray-500">
                            Receive notifications through Telegram bot
                          </p>
                        </div>
                      </div>

                      {telegramSettings.enabled && (
                        <>
                          {/* Bot Token */}
                          <div>
                            <label htmlFor="bot-token" className="block text-sm font-medium text-gray-700">
                              Bot Token
                            </label>
                            <div className="mt-1">
                              <input
                                type="text"
                                id="bot-token"
                                value={telegramSettings.bot_token}
                                onChange={(e) => setTelegramSettings(prev => ({
                                  ...prev,
                                  bot_token: e.target.value
                                }))}
                                className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter your Telegram bot token"
                              />
                            </div>
                            <p className="mt-2 text-sm text-gray-500">
                              Get this from @BotFather on Telegram
                            </p>
                          </div>

                          {/* Chat ID */}
                          <div>
                            <label htmlFor="chat-id" className="block text-sm font-medium text-gray-700">
                              Chat ID
                            </label>
                            <div className="mt-1">
                              <input
                                type="text"
                                id="chat-id"
                                value={telegramSettings.chat_id}
                                onChange={(e) => setTelegramSettings(prev => ({
                                  ...prev,
                                  chat_id: e.target.value
                                }))}
                                className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter your Telegram chat ID"
                              />
                            </div>
                            <p className="mt-2 text-sm text-gray-500">
                              Get this from @userinfobot on Telegram
                            </p>
                          </div>

                          {/* Notification Types */}
                          <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                              Notification Types
                            </label>
                            <div className="space-y-2">
                              {Object.entries(telegramSettings.notifications).map(([key, value]) => (
                                <div key={key} className="flex items-start">
                                  <div className="flex items-center h-5">
                                    <input
                                      type="checkbox"
                                      id={`notification-${key}`}
                                      name={key}
                                      checked={value}
                                      onChange={handleTelegramNotificationChange}
                                      className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                  </div>
                                  <div className="ml-3 text-sm">
                                    <label htmlFor={`notification-${key}`} className="font-medium text-gray-700">
                                      {key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')}
                                    </label>
                                  </div>
                                </div>
                              ))}
                            </div>
                          </div>

                          {/* Test Button */}
                          <div>
                            <button
                              type="button"
                              onClick={handleTelegramTest}
                              disabled={telegramTestLoading || !telegramSettings.bot_token || !telegramSettings.chat_id}
                              className={`inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ${
                                telegramTestLoading || !telegramSettings.bot_token || !telegramSettings.chat_id ? 'opacity-50 cursor-not-allowed' : ''
                              }`}
                            >
                              {telegramTestLoading ? (
                                <>
                                  <Spinner size="sm" className="mr-2" />
                                  Sending...
                                </>
                              ) : (
                                'Send Test Message'
                              )}
                            </button>
                          </div>

                          {/* Test Result */}
                          {telegramTestResult && (
                            <div className={`mt-4 p-4 rounded-md ${
                              telegramTestResult.success ? 'bg-green-50' : 'bg-red-50'
                            }`}>
                              <div className="flex">
                                <div className="flex-shrink-0">
                                  {telegramTestResult.success ? (
                                    <svg className="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                    </svg>
                                  ) : (
                                    <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                    </svg>
                                  )}
                                </div>
                                <div className="ml-3">
                                  <h3 className={`text-sm font-medium ${
                                    telegramTestResult.success ? 'text-green-800' : 'text-red-800'
                                  }`}>
                                    {telegramTestResult.message}
                                  </h3>
                                  {telegramTestResult.details && (
                                    <div className="mt-2 text-sm text-gray-700">
                                      <pre className="whitespace-pre-wrap">
                                        {JSON.stringify(telegramTestResult.details, null, 2)}
                                      </pre>
                                    </div>
                                  )}
                                </div>
                              </div>
                            </div>
                          )}
                        </>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}

          <div className="mt-6 flex justify-end">
            <button
              type="submit"
              disabled={saving}
              className={`bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ${
                saving ? 'opacity-50 cursor-not-allowed' : ''
              }`}
            >
              {saving ? (
                <span className="flex items-center">
                  <Spinner size="sm" className="mr-2" />
                  Saving...
                </span>
              ) : (
                'Save Settings'
              )}
            </button>
          </div>
        </form>
      </div>

      {/* Theme Preview Modal */}
      {showThemePreview && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-medium">Theme Preview</h3>
              <button
                onClick={handleCloseThemePreview}
                className="text-gray-400 hover:text-gray-500"
              >
                <span className="sr-only">Close</span>
                <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <ThemePreview theme={previewTheme} />
          </div>
        </div>
      )}
    </div>
  );
};

export default Settings;