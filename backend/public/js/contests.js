// Contest management
class ContestManager {
    constructor() {
        this.contests = [];
        this.init();
    }

    async init() {
        try {
            await this.loadContests();
            this.renderContests();
            this.setupEventListeners();
        } catch (error) {
            console.error('Error initializing contests:', error);
            this.showError('Failed to load contests');
        }
    }

    async loadContests() {
        const response = await fetchWithAuth(`${API_URL}/contests.php`);
        this.contests = await response.json();
    }

    renderContests() {
        const container = document.getElementById('contests-container');
        if (!container) return;

        container.innerHTML = this.contests.map(contest => `
            <div class="contest-card" data-id="${contest.id}">
                <h3>${contest.title}</h3>
                <p>${contest.description}</p>
                <div class="contest-info">
                    <span>Creator: ${contest.creator_name}</span>
                    <span>Votes: ${contest.total_votes}</span>
                    <span>Contestants: ${contest.total_contestants}</span>
                </div>
                <div class="contest-dates">
                    <span>Start: ${new Date(contest.start_date).toLocaleDateString()}</span>
                    <span>End: ${new Date(contest.end_date).toLocaleDateString()}</span>
                </div>
                <button class="vote-btn" data-id="${contest.id}">Vote Now</button>
            </div>
        `).join('');
    }

    setupEventListeners() {
        const container = document.getElementById('contests-container');
        if (!container) return;

        container.addEventListener('click', async (e) => {
            if (e.target.classList.contains('vote-btn')) {
                const contestId = e.target.dataset.id;
                await this.showVotingModal(contestId);
            }
        });
    }

    async showVotingModal(contestId) {
        const contest = this.contests.find(c => c.id === parseInt(contestId));
        if (!contest) return;

        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Vote for ${contest.title}</h2>
                <div class="contestants-list">
                    ${await this.getContestantsList(contestId)}
                </div>
                <button class="close-modal">Close</button>
            </div>
        `;

        document.body.appendChild(modal);
        this.setupModalEvents(modal, contestId);
    }

    async getContestantsList(contestId) {
        try {
            const response = await fetchWithAuth(`${API_URL}/contestants.php?contest_id=${contestId}`);
            const contestants = await response.json();
            return contestants.map(contestant => `
                <div class="contestant-card">
                    <img src="${contestant.image || 'default.jpg'}" alt="${contestant.name}">
                    <h4>${contestant.name}</h4>
                    <p>${contestant.description}</p>
                    <button class="select-contestant" data-id="${contestant.id}">Select</button>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading contestants:', error);
            return '<p>Error loading contestants</p>';
        }
    }

    setupModalEvents(modal, contestId) {
        const closeBtn = modal.querySelector('.close-modal');
        closeBtn.onclick = () => modal.remove();

        const selectButtons = modal.querySelectorAll('.select-contestant');
        selectButtons.forEach(button => {
            button.onclick = async () => {
                const contestantId = button.dataset.id;
                try {
                    await submitVote(contestId, contestantId);
                    this.showSuccess('Vote submitted successfully');
                    modal.remove();
                    await this.loadContests();
                    this.renderContests();
                } catch (error) {
                    this.showError('Failed to submit vote');
                }
            };
        });
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    }

    showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.textContent = message;
        document.body.appendChild(successDiv);
        setTimeout(() => successDiv.remove(), 3000);
    }
}

// Initialize contest manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ContestManager();
});