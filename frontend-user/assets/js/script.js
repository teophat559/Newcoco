// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Password strength meter
function checkPasswordStrength(password) {
    const meter = document.getElementById('password-strength-meter');
    const feedback = document.getElementById('password-feedback');
    if (!meter || !feedback) return;

    let strength = 0;
    let feedbackText = '';

    // Length check
    if (password.length >= 8) {
        strength += 25;
    }

    // Uppercase check
    if (/[A-Z]/.test(password)) {
        strength += 25;
    }

    // Lowercase check
    if (/[a-z]/.test(password)) {
        strength += 25;
    }

    // Number check
    if (/[0-9]/.test(password)) {
        strength += 25;
    }

    // Update meter
    meter.style.width = strength + '%';

    // Update feedback
    if (strength <= 25) {
        meter.className = 'progress-bar bg-danger';
        feedbackText = 'Mật khẩu yếu';
    } else if (strength <= 50) {
        meter.className = 'progress-bar bg-warning';
        feedbackText = 'Mật khẩu trung bình';
    } else if (strength <= 75) {
        meter.className = 'progress-bar bg-info';
        feedbackText = 'Mật khẩu khá';
    } else {
        meter.className = 'progress-bar bg-success';
        feedbackText = 'Mật khẩu mạnh';
    }

    feedback.textContent = feedbackText;
}

// Image preview
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (!preview) return;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

// Handle vote
function handleVote(contestId, contestantId) {
    if (!confirm('Bạn có chắc chắn muốn bình chọn cho thí sinh này?')) {
        return;
    }

    $.ajax({
        url: SITE_URL + '/ajax/vote.php',
        type: 'POST',
        data: {
            contest_id: contestId,
            contestant_id: contestantId
        },
        success: function(response) {
            if (response.success) {
                alert('Bình chọn thành công!');
                location.reload();
            } else {
                alert(response.message || 'Có lỗi xảy ra, vui lòng thử lại sau!');
            }
        },
        error: function() {
            alert('Có lỗi xảy ra, vui lòng thử lại sau!');
        }
    });
}

// Document ready
$(document).ready(function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Add fade-in animation to cards
    $('.card').addClass('fade-in');

    // Handle form validation
    $('form').on('submit', function() {
        return validateForm(this.id);
    });

    // Handle input validation
    $('input[required], textarea[required], select[required]').on('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
        } else {
            this.classList.add('is-invalid');
        }
    });
});