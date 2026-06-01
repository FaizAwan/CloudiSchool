/**
 * Exam System JavaScript
 * Real-time scoring, timer, and exam interface functionality
 * Compatible with Pakistan Examination System
 */

class ExamSystem {
    constructor(options = {}) {
        this.examId = options.examId || null;
        this.totalQuestions = options.totalQuestions || 0;
        this.duration = options.duration || 0; // in minutes
        this.autoSubmit = options.autoSubmit || true;
        this.currentScore = 0;
        this.attemptedQuestions = 0;
        this.timeRemaining = this.duration * 60; // convert to seconds
        this.timerInterval = null;
        this.autoSaveInterval = null;
        this.isSubmitting = false;
        
        this.init();
    }

    init() {
        this.initTimer();
        this.initQuestionNavigation();
        this.initAnswerHandlers();
        this.initAutoSave();
        this.initKeyboardShortcuts();
        this.initWarnings();
        
        console.log('Exam System initialized');
    }

    // Timer functionality
    initTimer() {
        if (this.duration > 0) {
            this.startTimer();
            this.updateTimerDisplay();
        }
    }

    startTimer() {
        this.timerInterval = setInterval(() => {
            this.timeRemaining--;
            this.updateTimerDisplay();
            
            // Show warnings
            if (this.timeRemaining === 300) { // 5 minutes warning
                this.showTimeWarning('5 minutes remaining!', 'warning');
            } else if (this.timeRemaining === 60) { // 1 minute warning
                this.showTimeWarning('1 minute remaining!', 'danger');
            } else if (this.timeRemaining <= 0) {
                this.handleTimeUp();
            }
        }, 1000);
    }

    updateTimerDisplay() {
        const hours = Math.floor(this.timeRemaining / 3600);
        const minutes = Math.floor((this.timeRemaining % 3600) / 60);
        const seconds = this.timeRemaining % 60;
        
        const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        const timerElement = document.getElementById('exam-timer');
        if (timerElement) {
            timerElement.textContent = timeString;
            
            // Change color based on remaining time
            if (this.timeRemaining <= 300) { // 5 minutes
                timerElement.className = 'text-danger font-weight-bold';
            } else if (this.timeRemaining <= 600) { // 10 minutes
                timerElement.className = 'text-warning font-weight-bold';
            } else {
                timerElement.className = 'text-success font-weight-bold';
            }
        }
        
        // Update progress bar
        const progressElement = document.getElementById('time-progress');
        if (progressElement) {
            const totalTime = this.duration * 60;
            const progress = ((totalTime - this.timeRemaining) / totalTime) * 100;
            progressElement.style.width = progress + '%';
        }
    }

    handleTimeUp() {
        clearInterval(this.timerInterval);
        
        if (this.autoSubmit && !this.isSubmitting) {
            this.showTimeWarning('Time is up! Submitting exam automatically...', 'danger');
            setTimeout(() => {
                this.submitExam(true);
            }, 2000);
        }
    }

    showTimeWarning(message, type = 'warning') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="mr-auto">Exam Timer</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span>&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        // Add to toast container
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'position-fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.appendChild(toast);
        
        // Show toast
        $(toast).toast({ delay: 5000 }).toast('show');
        
        // Remove after hiding
        $(toast).on('hidden.bs.toast', function() {
            toast.remove();
        });
    }

    // Question navigation
    initQuestionNavigation() {
        const questionButtons = document.querySelectorAll('.question-nav-btn');
        questionButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const questionNumber = btn.dataset.question;
                this.navigateToQuestion(questionNumber);
            });
        });
        
        // Previous/Next buttons
        const prevBtn = document.getElementById('prev-question');
        const nextBtn = document.getElementById('next-question');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.navigateToPrevious());
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.navigateToNext());
        }
    }

    navigateToQuestion(questionNumber) {
        // Hide all questions
        document.querySelectorAll('.question-container').forEach(container => {
            container.style.display = 'none';
        });
        
        // Show target question
        const targetQuestion = document.getElementById(`question-${questionNumber}`);
        if (targetQuestion) {
            targetQuestion.style.display = 'block';
            
            // Update navigation buttons
            this.updateQuestionNavigation(parseInt(questionNumber));
            
            // Update question indicator
            this.updateQuestionIndicator(questionNumber);
        }
    }

    updateQuestionNavigation(currentQuestion) {
        const prevBtn = document.getElementById('prev-question');
        const nextBtn = document.getElementById('next-question');
        
        if (prevBtn) {
            prevBtn.disabled = currentQuestion <= 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentQuestion >= this.totalQuestions;
        }
        
        // Update current question number display
        const currentQuestionDisplay = document.getElementById('current-question-number');
        if (currentQuestionDisplay) {
            currentQuestionDisplay.textContent = currentQuestion;
        }
    }

    updateQuestionIndicator(questionNumber) {
        // Update sidebar navigation
        document.querySelectorAll('.question-nav-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.question === questionNumber.toString()) {
                btn.classList.add('active');
            }
        });
    }

    navigateToPrevious() {
        const currentQuestion = this.getCurrentQuestionNumber();
        if (currentQuestion > 1) {
            this.navigateToQuestion(currentQuestion - 1);
        }
    }

    navigateToNext() {
        const currentQuestion = this.getCurrentQuestionNumber();
        if (currentQuestion < this.totalQuestions) {
            this.navigateToQuestion(currentQuestion + 1);
        }
    }

    getCurrentQuestionNumber() {
        const activeQuestion = document.querySelector('.question-container[style*="block"]');
        if (activeQuestion) {
            return parseInt(activeQuestion.id.replace('question-', ''));
        }
        return 1;
    }

    // Answer handling
    initAnswerHandlers() {
        // MCQ option selection
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('mcq-option')) {
                this.handleMcqAnswer(e.target);
            }
        });
        
        // Text answer input (for short/long questions)
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('text-answer')) {
                this.handleTextAnswer(e.target);
            }
        });
        
        // True/False questions
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('true-false-option')) {
                this.handleTrueFalseAnswer(e.target);
            }
        });
    }

    handleMcqAnswer(optionElement) {
        const questionId = optionElement.dataset.questionId;
        const selectedOption = optionElement.value;
        const questionContainer = optionElement.closest('.question-container');
        
        // Update question status
        this.updateQuestionStatus(questionId, 'attempted');
        
        // Auto-calculate score for MCQ (if correct answer is available)
        const correctOption = questionContainer.dataset.correctOption;
        if (correctOption) {
            const isCorrect = selectedOption === correctOption;
            const marks = parseInt(questionContainer.dataset.marks) || 1;
            
            if (isCorrect) {
                this.updateScore(questionId, marks);
                this.showAnswerFeedback(questionContainer, 'correct');
            } else {
                this.updateScore(questionId, 0);
                this.showAnswerFeedback(questionContainer, 'incorrect');
            }
        }
        
        // Save answer
        this.saveAnswer(questionId, 'mcq', { selected_option: selectedOption });
    }

    handleTextAnswer(textElement) {
        const questionId = textElement.dataset.questionId;
        const answerText = textElement.value.trim();
        
        if (answerText.length > 0) {
            this.updateQuestionStatus(questionId, 'attempted');
        } else {
            this.updateQuestionStatus(questionId, 'not-attempted');
        }
        
        // Save answer (debounced)
        clearTimeout(this.textAnswerTimeout);
        this.textAnswerTimeout = setTimeout(() => {
            this.saveAnswer(questionId, 'text', { answer_text: answerText });
        }, 1000);
    }

    handleTrueFalseAnswer(optionElement) {
        const questionId = optionElement.dataset.questionId;
        const selectedOption = optionElement.value;
        
        this.updateQuestionStatus(questionId, 'attempted');
        this.saveAnswer(questionId, 'mcq', { selected_option: selectedOption });
    }

    updateQuestionStatus(questionId, status) {
        const navBtn = document.querySelector(`[data-question="${questionId}"]`);
        if (navBtn) {
            navBtn.classList.remove('btn-outline-secondary', 'btn-warning', 'btn-success');
            
            switch (status) {
                case 'attempted':
                    navBtn.classList.add('btn-warning');
                    break;
                case 'correct':
                    navBtn.classList.add('btn-success');
                    break;
                case 'not-attempted':
                    navBtn.classList.add('btn-outline-secondary');
                    break;
            }
        }
        
        // Update attempt counter
        this.updateAttemptedCounter();
    }

    updateAttemptedCounter() {
        const attemptedCount = document.querySelectorAll('.question-nav-btn.btn-warning, .question-nav-btn.btn-success').length;
        this.attemptedQuestions = attemptedCount;
        
        const counterElement = document.getElementById('attempted-count');
        if (counterElement) {
            counterElement.textContent = attemptedCount;
        }
        
        const remainingElement = document.getElementById('remaining-count');
        if (remainingElement) {
            remainingElement.textContent = this.totalQuestions - attemptedCount;
        }
    }

    updateScore(questionId, marks) {
        // This would be implemented based on your scoring logic
        // For now, just update the display
        const scoreElement = document.getElementById('current-score');
        if (scoreElement) {
            this.currentScore += marks;
            scoreElement.textContent = this.currentScore;
        }
    }

    showAnswerFeedback(questionContainer, type) {
        // Remove existing feedback
        const existingFeedback = questionContainer.querySelector('.answer-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Add new feedback
        const feedback = document.createElement('div');
        feedback.className = `answer-feedback alert alert-${type === 'correct' ? 'success' : 'danger'} mt-2`;
        feedback.innerHTML = type === 'correct' ? 
            '<i class="fas fa-check"></i> Correct!' : 
            '<i class="fas fa-times"></i> Incorrect';
        
        questionContainer.appendChild(feedback);
        
        // Remove feedback after 3 seconds
        setTimeout(() => {
            if (feedback.parentNode) {
                feedback.remove();
            }
        }, 3000);
    }

    // Auto-save functionality
    initAutoSave() {
        this.autoSaveInterval = setInterval(() => {
            this.autoSaveProgress();
        }, 30000); // Auto-save every 30 seconds
    }

    autoSaveProgress() {
        if (this.isSubmitting) return;
        
        // This would send current progress to server
        console.log('Auto-saving progress...');
        
        // Show auto-save indicator
        const indicator = document.getElementById('auto-save-indicator');
        if (indicator) {
            indicator.textContent = 'Saving...';
            setTimeout(() => {
                indicator.textContent = 'All changes saved';
            }, 1000);
        }
    }

    saveAnswer(questionId, type, data) {
        if (this.isSubmitting) return;
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        if (type === 'mcq') {
            formData.append('selected_option', data.selected_option);
        } else {
            formData.append('answer_text', data.answer_text);
        }
        
        fetch(`/student/exams/${this.examId}/questions/${questionId}/answer`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Answer saved successfully');
                // Update remaining time from server
                if (data.time_remaining !== undefined) {
                    this.timeRemaining = data.time_remaining * 60; // convert to seconds
                }
            } else {
                console.error('Failed to save answer:', data.error);
            }
        })
        .catch(error => {
            console.error('Error saving answer:', error);
        });
    }

    // Keyboard shortcuts
    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Only handle shortcuts when not typing in input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            
            switch (e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    this.navigateToPrevious();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.navigateToNext();
                    break;
                case 'Enter':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        this.confirmAndSubmit();
                    }
                    break;
            }
        });
    }

    // Warning handlers
    initWarnings() {
        // Prevent accidental page refresh
        window.addEventListener('beforeunload', (e) => {
            if (!this.isSubmitting) {
                e.preventDefault();
                e.returnValue = 'You have an exam in progress. Are you sure you want to leave?';
            }
        });
        
        // Handle browser back button
        window.addEventListener('popstate', (e) => {
            if (!this.isSubmitting) {
                e.preventDefault();
                alert('Please use the exam navigation buttons or submit your exam properly.');
                history.pushState(null, null, location.href);
            }
        });
        
        // Disable right-click context menu
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        });
        
        // Disable F12, Ctrl+Shift+I, Ctrl+U, etc.
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
                alert('Developer tools are disabled during the exam.');
            }
        });
    }

    // Exam submission
    confirmAndSubmit() {
        const unattemptedCount = this.totalQuestions - this.attemptedQuestions;
        
        let message = 'Are you sure you want to submit your exam?';
        if (unattemptedCount > 0) {
            message += `\n\nYou have ${unattemptedCount} unanswered questions.`;
        }
        
        if (confirm(message)) {
            this.submitExam(false);
        }
    }

    submitExam(autoSubmit = false) {
        if (this.isSubmitting) return;
        
        this.isSubmitting = true;
        
        // Clear intervals
        if (this.timerInterval) clearInterval(this.timerInterval);
        if (this.autoSaveInterval) clearInterval(this.autoSaveInterval);
        
        // Show loading
        const submitBtn = document.getElementById('submit-exam-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        }
        
        // Submit form
        const form = document.getElementById('exam-form');
        if (form) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'auto_submit';
            hiddenInput.value = autoSubmit ? '1' : '0';
            form.appendChild(hiddenInput);
            
            form.submit();
        } else {
            // Fallback: direct POST request
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('auto_submit', autoSubmit ? '1' : '0');
            
            fetch(`/student/exams/${this.examId}/submit`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = `/student/exams/${this.examId}/result`;
                } else {
                    throw new Error('Submission failed');
                }
            })
            .catch(error => {
                alert('Failed to submit exam. Please try again.');
                this.isSubmitting = false;
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Exam';
                }
            });
        }
    }

    // Public methods for external use
    destroy() {
        if (this.timerInterval) clearInterval(this.timerInterval);
        if (this.autoSaveInterval) clearInterval(this.autoSaveInterval);
        console.log('Exam System destroyed');
    }
}

// Initialize exam system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on an exam page
    const examContainer = document.getElementById('exam-container');
    if (examContainer) {
        const options = {
            examId: examContainer.dataset.examId,
            totalQuestions: parseInt(examContainer.dataset.totalQuestions) || 0,
            duration: parseInt(examContainer.dataset.duration) || 0,
            autoSubmit: examContainer.dataset.autoSubmit === 'true'
        };
        
        window.examSystem = new ExamSystem(options);
    }
});

// Utility functions
function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    const container = document.getElementById('notification-container') || document.body;
    container.insertBefore(notification, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $(notification).alert('close');
    }, 5000);
}
