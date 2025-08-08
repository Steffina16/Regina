document.addEventListener('DOMContentLoaded', function() {
    // Get all navigation links and content sections
    const navLinks = document.querySelectorAll('.navbar a');
    const contentSections = document.querySelectorAll('.content-section');
    
    // Initialize - hide all sections except active one
    contentSections.forEach(section => {
        section.style.display = section.classList.contains('active-section') ? 'block' : 'none';
    });

    // Handle navigation clicks
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get target section ID
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            
            // If target doesn't exist, exit
            if (!targetSection) {
                console.error(`Section with ID ${targetId} not found`);
                return;
            }
            
            // Update active states
            navLinks.forEach(link => link.classList.remove('active-link'));
            contentSections.forEach(section => {
                section.classList.remove('active-section');
                section.style.display = 'none';
            });
            
            // Activate new section
            this.classList.add('active-link');
            targetSection.classList.add('active-section');
            targetSection.style.display = 'block';
            
            // Special handling for specific sections
            if (targetId === 'pictures') {
                initializePictureCategories();
            } else if (targetId === 'quiz') {
                resetQuiz(); // Reset quiz when navigating to it
            }
        });
    });

    // Initialize picture categories if needed
    function initializePictureCategories() {
        const categoryLinks = document.querySelectorAll('[data-category]');
        
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const category = this.getAttribute('data-category');
                loadPictureCategory(category);
            });
        });
        
        // Load default category
        loadPictureCategory('cute');
    }

    function loadPictureCategory(category) {
        console.log(`Loading ${category} pictures`);
        // Your category loading logic here
    }

    // ====================== QUIZ FUNCTIONALITY ======================
    const quizQuestions = [
        {
            question: "Where did we first meet?",
            answers: [
                "At church",
                "In school",
                "Through mutual friends",
                "Social Media"
            ],
            correctAnswer: 0
        },
        {
            question: "What's my favorite thing about you?",
            answers: [
                "Your smile",
                "Your kindness",
                "Your sense of humor",
                "All of the above"
            ],
            correctAnswer: 3
        },
        {
            question: "Where was our first date?",
            answers: [
                "Coffee shop",
                "Movie night",
                "Church event",
                "Dinner date"
            ],
            correctAnswer: 3
        },
        {
            question: "What's my first gift for you?",
            answers: [
                "Necklace",
                "Ballpen",
                "R5-15",
                "Bracelet"
            ],
            correctAnswer: 3
        },
        {
            question: "What's my favorite food?",
            answers:  [
                "Stir fry baguio beans",
                "Adobong baboy",
                "Pares",
                "Sinigang"
            ],
            correctAnswer: 0
        }    
    ];

    let currentQuestion = 0;
    let score = 0;

    // Initialize quiz if quiz section exists
    const quizSection = document.getElementById('quiz');
    if (quizSection) {
        document.getElementById('start-quiz').addEventListener('click', startQuiz);
    }

    function startQuiz() {
        currentQuestion = 0;
        score = 0;
        document.querySelector('.quiz-intro').classList.add('hidden');
        document.getElementById('quiz-questions').classList.remove('hidden');
        showQuestion();
    }

    function showQuestion() {
        const questionContainer = document.getElementById('quiz-questions');
        const question = quizQuestions[currentQuestion];
        
        questionContainer.innerHTML = `
            <div class="question">
                <h2 class="question-text">${question.question}</h2>
                ${question.answers.map((answer, index) => `
                    <button class="answer-option" onclick="selectAnswer(${index})">
                        ${answer}
                    </button>
                `).join('')}
            </div>
        `;
    }

    // Note: This needs to be in global scope to work with inline onclick
    window.selectAnswer = function(answerIndex) {
        if (answerIndex === quizQuestions[currentQuestion].correctAnswer) {
            score++;
        }
        
        currentQuestion++;
        
        if (currentQuestion < quizQuestions.length) {
            showQuestion();
        } else {
            showResults();
        }
    };

    function showResults() {
        const quizContainer = document.getElementById('quiz-questions');
        quizContainer.classList.add('hidden');
        
        const resultsContainer = document.getElementById('quiz-results');
        resultsContainer.classList.remove('hidden');
        
        const percentage = Math.round((score / quizQuestions.length) * 100);
        
        resultsContainer.innerHTML = `
            <h2>Quiz Complete!</h2>
            <p>You scored ${score} out of ${quizQuestions.length} (${percentage}%)</p>
            <p>${getResultMessage(percentage)}</p>
            <button onclick="resetQuiz()" class="quiz-button">Try Again</button>
        `;
    }

    function getResultMessage(percentage) {
        if (percentage >= 90) {
            return "Wow perfect mahal na mahal talaga naten ang isa't isa <3";
        } else if (percentage >= 50) {
            return "Bakit may Mali, Hindi mo bako mahal?";
        } else {
            return "Siguro May Iba Kana :<";
        }
    }

    window.resetQuiz = function() {
        document.getElementById('quiz-results').classList.add('hidden');
        document.querySelector('.quiz-intro').classList.remove('hidden');
    };
});

document.addEventListener('DOMContentLoaded', function () {
    // ... existing nav click & section toggle logic ...

    // ===== Hash-based navigation on load (e.g. #pictures) =====
    const initialHash = window.location.hash.replace('#', '');
    if (initialHash) {
        const matchingLink = document.querySelector(`.navbar a[data-target="${initialHash}"]`);
        if (matchingLink) {
            matchingLink.click(); // Simulate a click to load correct section
        }
    }
});
