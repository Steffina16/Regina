document.addEventListener('DOMContentLoaded', function() {

// ===== Homepage Slideshow =====
const slideshowContainer = document.querySelector('#home .gallery-container');
if (slideshowContainer) {
    const slideshowText = document.getElementById('slideshow-text');

    // Generate array of image paths dynamically (228 images)
    const totalImages = 228;
    const imagePaths = [];
    for (let i = 1; i <= totalImages; i++) {
        const numStr = String(i).padStart(3, '0'); // pad 001, 002, etc.
        imagePaths.push(`albums/picture/picture ni chin-${numStr}.jpg`);
    }

    // Shuffle images randomly
    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }
    shuffle(imagePaths);

    // Create img element
    const imgElement = document.createElement('img');
    imgElement.style.width = '100%';
    imgElement.style.height = '500px';
    imgElement.style.objectFit = 'cover';
    imgElement.style.transition = 'opacity 1s ease-in-out';
    imgElement.style.position = 'absolute';
    imgElement.style.top = '0';
    imgElement.style.left = '0';
    imgElement.style.opacity = '0';
    imgElement.style.zIndex = '1';
    slideshowContainer.appendChild(imgElement);

    let currentIndex = 0;

    function showNextImage() {
        imgElement.style.opacity = '0';
        setTimeout(() => {
            imgElement.src = imagePaths[currentIndex];
            imgElement.style.opacity = '1';
            currentIndex = (currentIndex + 1) % imagePaths.length;
        }, 500);
    }

    showNextImage();
    setInterval(showNextImage, 5000); // change every 5 seconds
}

    // ===== Dropdown toggle =====
const dropBtn = document.querySelector(".dropbtn");
const dropdown = document.querySelector(".dropdown");

if (dropBtn) {
    dropBtn.addEventListener("click", function(e) {
        e.stopPropagation();
        dropdown.classList.toggle("show"); // toggle parent .dropdown
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

    // Load section if hash exists
    const initialHash = window.location.hash.replace('#', '');
    if (initialHash) showSection(initialHash);

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
        { question: "What's my favorite food?", answers: ["Stir fry baguio beans", "Adobong baboy", "Pares", "Sinigang"], correctAnswer: 0 }
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
