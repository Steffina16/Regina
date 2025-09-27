document.addEventListener('DOMContentLoaded', function() {

    const slideshowWrapper = document.getElementById('slideshow-wrapper');

    // ===== Slideshow =====
    const folder1 = [];
    for (let i = 1; i <= 28; i++) {
        const num = String(i).padStart(3, '0');
        folder1.push(`albums/ourpicture/Ourpicture-${num}.jpg`);
    }

    const folder2 = [];
    for (let i = 1; i <= 228; i++) {
        const num = String(i).padStart(3, '0');
        folder2.push(`albums/picture/picture-ni-chin-${num}.jpg`);
    }

    const folder3 = [];
    for (let i = 1; i <= 6; i++) {
        const num = String(i).padStart(3, '0');
        folder3.push(`albums/Funnypic/Chin-${num}.jpg`);
    }

    let imagePaths = [...folder1, ...folder2, ...folder3];
    imagePaths = imagePaths.sort(() => Math.random() - 0.5);

    let currentIndex = 0;

    function showNextSlide() {
        slideshowWrapper.innerHTML = '';
        for (let i = 0; i < 3; i++) {
            const imgIndex = (currentIndex + i) % imagePaths.length;
            const imgElement = document.createElement('img');
            imgElement.src = imagePaths[imgIndex];
            imgElement.style.flex = '1';
            imgElement.style.width = '33.33%';
            imgElement.style.height = '100%';
            imgElement.style.objectFit = 'cover';
            imgElement.style.transition = 'opacity 0.5s ease-in-out';
            slideshowWrapper.appendChild(imgElement);
        }
        currentIndex = (currentIndex + 3) % imagePaths.length;
    }

    showNextSlide();
    setInterval(showNextSlide, 5000);

    // ===== Dropdown toggle =====
    const dropBtn = document.querySelector(".dropbtn");
    const dropdown = document.querySelector(".dropdown");

    if (dropBtn) {
        dropBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            dropdown.classList.toggle("show");
        });
    }

    window.addEventListener("click", function(event) {
        if (!event.target.closest(".dropdown")) {
            dropdown.classList.remove("show");
        }
    });

    // ===== SPA Navigation =====
    const navLinks = document.querySelectorAll('.navbar a[data-target]');
    const contentSections = document.querySelectorAll('.content-section');

    contentSections.forEach(section => {
        section.style.display = section.classList.contains('active-section') ? 'block' : 'none';
    });

    function showSection(targetId) {
        const targetSection = document.getElementById(targetId);
        if (!targetSection) return;

        navLinks.forEach(link => link.classList.remove('active-link'));
        contentSections.forEach(section => {
            section.classList.remove('active-section');
            section.style.display = 'none';
        });

        const matchingLink = document.querySelector(`.navbar a[data-target="${targetId}"]`);
        if (matchingLink) matchingLink.classList.add('active-link');

        targetSection.classList.add('active-section');
        targetSection.style.display = 'block';

        if (targetId === 'pictures') initializePictureCategories();
        else if (targetId === 'quiz') resetQuiz();
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            if (!targetId) return;
            showSection(targetId);
            history.pushState(null, '', `#${targetId}`);
        });
    });

    const initialHash = window.location.hash.replace('#', '');
    if (initialHash) showSection(initialHash);

    // ===== Allow normal navigation for dropdown links =====
    const dropdownLinks = document.querySelectorAll('.dropdown-content a');
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function() {
            dropdown.classList.remove('show'); // close dropdown
            // normal navigation happens automatically
        });
    });

    // ===== Pictures =====
    function initializePictureCategories() {
        const categoryLinks = document.querySelectorAll('[data-category]');
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const category = this.getAttribute('data-category');
                loadPictureCategory(category);
            });
        });
        loadPictureCategory('cute');
    }

    function loadPictureCategory(category) {
        console.log(`Loading ${category} pictures`);
        // Add your gallery logic here
    }

    // ===== Quiz =====
    const quizQuestions = [
        { question: "Where did we first meet?", answers: ["At church", "In school", "Through mutual friends", "Social Media"], correctAnswer: 0 },
        { question: "What's my favorite thing about you?", answers: ["Your smile", "Your kindness", "Your sense of humor", "All of the above"], correctAnswer: 3 },
        { question: "Where was our first date?", answers: ["Coffee shop", "Movie night", "Church event", "Dinner date"], correctAnswer: 3 },
        { question: "What's my first gift for you?", answers: ["Necklace", "Ballpen", "R5-15", "Bracelet"], correctAnswer: 3 },
        { question: "What's my favorite food?", answers: ["Stir fry baguio beans", "Adobong baboy", "Pares", "Sinigang"], correctAnswer: 0 },
        { question: "What's my favorite", answers: ["Games", "Regina", "Food", "All of the above"], correctAnswer: 1 }
    ];

    let currentQuestion = 0;
    let score = 0;

    const quizSection = document.getElementById('quiz');
    if (quizSection) document.getElementById('start-quiz').addEventListener('click', startQuiz);

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

    window.selectAnswer = function(answerIndex) {
        if (answerIndex === quizQuestions[currentQuestion].correctAnswer) score++;
        currentQuestion++;
        if (currentQuestion < quizQuestions.length) showQuestion();
        else showResults();
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
        if (percentage >= 90) return "Wow perfect mahal na mahal talaga naten ang isa't isa <3";
        else if (percentage >= 50) return "Bakit may Mali, Hindi mo bako mahal?";
        else return "Siguro May Iba Kana :<";
    }

    window.resetQuiz = function() {
        document.getElementById('quiz-results').classList.add('hidden');
        document.querySelector('.quiz-intro').classList.remove('hidden');
    };
});
