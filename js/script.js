document.addEventListener('DOMContentLoaded', function() {

    /* ===== Slideshow ===== */
    const slideshowWrapper = document.getElementById('slideshow-wrapper');
    if (slideshowWrapper) {
        const folder1 = [];
        for (let i = 1; i <= 28; i++) folder1.push(`albums/ourpicture/Ourpicture-${String(i).padStart(3, '0')}.jpg`);

        const folder2 = [];
        for (let i = 1; i <= 228; i++) folder2.push(`albums/picture/picture-ni-chin-${String(i).padStart(3, '0')}.jpg`);

        const folder3 = [];
        for (let i = 1; i <= 6; i++) folder3.push(`albums/Funnypic/Chin-${String(i).padStart(3, '0')}.jpg`);

        let imagePaths = [...folder1, ...folder2, ...folder3].sort(() => Math.random() - 0.5);
        let currentIndex = 0;

        function showNextSlide() {
            slideshowWrapper.innerHTML = '';
            for (let i = 0; i < 3; i++) {
                const imgIndex = (currentIndex + i) % imagePaths.length;
                const img = document.createElement('img');
                img.src = imagePaths[imgIndex];
                img.style.flex = '1';
                img.style.width = '33.33%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.transition = 'opacity 0.5s ease-in-out';
                slideshowWrapper.appendChild(img);
            }
            currentIndex = (currentIndex + 3) % imagePaths.length;
        }

        showNextSlide();
        setInterval(showNextSlide, 5000);
    }

    /* ===== Regular Dropdown (Message Icon) ===== */
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
            dropdown?.classList.remove("show");
        }
    });

    /* ===== Profile Dropdown (Avatar Menu) ===== */
    const profileBtn = document.getElementById("profileBtn");
    const profileMenu = document.getElementById("profileMenu");

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            profileMenu.classList.toggle("show");
        });

        window.addEventListener("click", function (event) {
            if (!event.target.closest(".profile-dropdown")) {
                profileMenu.classList.remove("show");
            }
        });
    }

    /* ===== SPA Navigation ===== */
    const navLinks = document.querySelectorAll('.navbar a[data-target]');
    const contentSections = document.querySelectorAll('.content-section');

    function showSection(targetId) {
        const target = document.getElementById(targetId);
        if (!target) return;

        navLinks.forEach(link => link.classList.remove('active-link'));
        contentSections.forEach(section => {
            section.classList.remove('active-section');
            section.style.display = 'none';
        });

        const matching = document.querySelector(`.navbar a[data-target="${targetId}"]`);
        if (matching) matching.classList.add('active-link');

        target.classList.add('active-section');
        target.style.display = 'block';
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
    else if (contentSections.length) contentSections[0].style.display = 'block';

    /* ===== Quiz Logic ===== */
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
    const startQuizBtn = document.getElementById('start-quiz');
    const quizContainer = document.getElementById('quiz-questions');
    const quizResults = document.getElementById('quiz-results');

    if (startQuizBtn) startQuizBtn.addEventListener('click', startQuiz);

    function startQuiz() {
        currentQuestion = 0;
        score = 0;
        document.querySelector('.quiz-intro').classList.add('hidden');
        quizContainer.classList.remove('hidden');
        showQuestion();
    }

    function showQuestion() {
        const q = quizQuestions[currentQuestion];
        quizContainer.innerHTML = `
            <div class="question">
                <h2 class="question-text">${q.question}</h2>
                ${q.answers.map((a, i) => `<button class="answer-option" onclick="selectAnswer(${i})">${a}</button>`).join('')}
            </div>`;
    }

    window.selectAnswer = function(answerIndex) {
        if (answerIndex === quizQuestions[currentQuestion].correctAnswer) score++;
        currentQuestion++;
        if (currentQuestion < quizQuestions.length) showQuestion();
        else showResults();
    };

    function showResults() {
        quizContainer.classList.add('hidden');
        quizResults.classList.remove('hidden');
        const percent = Math.round((score / quizQuestions.length) * 100);
        quizResults.innerHTML = `
            <h2>Quiz Complete!</h2>
            <p>You scored ${score} out of ${quizQuestions.length} (${percent}%)</p>
            <p>${getResultMessage(percent)}</p>
            <button onclick="resetQuiz()" class="quiz-button">Try Again</button>
        `;
    }

    function getResultMessage(pct) {
        if (pct >= 90) return "Wow perfect mahal na mahal talaga naten ang isa't isa <3";
        else if (pct >= 50) return "Bakit may Mali, Hindi mo bako mahal?";
        else return "Siguro May Iba Kana :<";
    }

    window.resetQuiz = function() {
        quizResults.classList.add('hidden');
        document.querySelector('.quiz-intro').classList.remove('hidden');
    };
});
