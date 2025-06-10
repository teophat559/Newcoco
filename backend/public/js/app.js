const API_URL = 'https://your-backend-url.com/api';

// Authentication
function getToken() {
    return localStorage.getItem('token');
}

function setToken(token) {
    localStorage.setItem('token', token);
}

function removeToken() {
    localStorage.removeItem('token');
}

// API calls with authentication
async function fetchWithAuth(url, options = {}) {
    const token = getToken();
    const headers = {
        'Content-Type': 'application/json',
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        ...options.headers
    };

    try {
        const response = await fetch(url, {
            ...options,
            headers
        });

        if (response.status === 401) {
            removeToken();
            window.location.href = '/login.html';
            throw new Error('Unauthorized');
        }

        return response;
    } catch (error) {
        console.error('API call failed:', error);
        throw error;
    }
}

// Example API calls
async function getContests() {
    try {
        const response = await fetchWithAuth(`${API_URL}/contests.php`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching contests:', error);
        throw error;
    }
}

async function submitVote(contestId, contestantId) {
    try {
        const response = await fetchWithAuth(`${API_URL}/votes.php`, {
            method: 'POST',
            body: JSON.stringify({
                contest_id: contestId,
                contestant_id: contestantId
            })
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error submitting vote:', error);
        throw error;
    }
}

// Login function
async function login(username, password) {
    try {
        const response = await fetch(`${API_URL}/auth/login.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });
        const data = await response.json();
        if (data.token) {
            setToken(data.token);
        }
        return data;
    } catch (error) {
        console.error('Login failed:', error);
        throw error;
    }
}

// Initialize app
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const contests = await getContests();
        // Render contests to the page
        const app = document.getElementById('app');
        // Add your rendering logic here
    } catch (error) {
        console.error('Error initializing app:', error);
    }
});