document.addEventListener('DOMContentLoaded', function() {
    console.log('slideshowImages:', window.slideshowImages);

    /* ===== Slideshow ===== */
    const slideshowWrapper = document.getElementById('slideshow-wrapper');
    if (slideshowWrapper && window.slideshowImages && window.slideshowImages.length) {
        // clone image list and shuffle it randomly
        const imagePaths = [...window.slideshowImages];
        for (let i = imagePaths.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [imagePaths[i], imagePaths[j]] = [imagePaths[j], imagePaths[i]];
        }

        // clear existing slides
        slideshowWrapper.innerHTML = '';

        // display only the first 3 shuffled images
        const showCount = Math.min(3, imagePaths.length);
        for (let i = 0; i < showCount; i++) {
            const img = document.createElement('img');
            img.src = imagePaths[i];
            img.style.flex = '1';
            img.style.width = '33.33%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '10px';
            img.style.transition = 'opacity 0.5s ease-in-out';
            slideshowWrapper.appendChild(img);
        }
    } else {
        console.warn('No slideshow images found or slideshow wrapper missing.');
    }

    /* ===== Dropdowns ===== */
    const dropBtn = document.querySelector(".dropbtn");
    const dropdown = document.querySelector(".dropdown");
    if (dropBtn) {
        dropBtn.addEventListener("click", e => {
            e.stopPropagation();
            dropdown.classList.toggle("show");
        });
    }
    window.addEventListener("click", e => {
        if (!e.target.closest(".dropdown")) dropdown?.classList.remove("show");
    });

    /* ===== Profile Menu ===== */
    const profileBtn = document.getElementById("profileBtn");
    const profileMenu = document.getElementById("profileMenu");
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener("click", e => {
            e.stopPropagation();
            profileMenu.classList.toggle("show");
        });
        window.addEventListener("click", e => {
            if (!e.target.closest(".profile-dropdown")) profileMenu.classList.remove("show");
        });
    }

    /* ===== SPA Navigation ===== */
    const navLinks = document.querySelectorAll('.navbar a[data-target]');
    const contentSections = document.querySelectorAll('.content-section');

    function showSection(targetId) {
        const target = document.getElementById(targetId);
        if (!target) return;

        navLinks.forEach(link => link.classList.remove('active-link'));
        contentSections.forEach(sec => {
            sec.classList.remove('active-section');
            sec.style.display = 'none';
        });

        const matchingLink = document.querySelector(`.navbar a[data-target="${targetId}"]`);
        if (matchingLink) matchingLink.classList.add('active-link');

        target.classList.add('active-section');
        target.style.display = 'block';
    }

    navLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const targetId = link.getAttribute('data-target');
            if (!targetId) return;
            showSection(targetId);
            history.pushState(null, '', `#${targetId}`);
        });
    });

    const initialHash = window.location.hash.replace('#','');
    if (initialHash) showSection(initialHash);
    else if (contentSections.length) contentSections[0].style.display = 'block';

    /* ===== Quiz ===== */
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
    const startBtn = document.getElementById('start-quiz');
    const quizContainer = document.getElementById('quiz-questions');
    const quizResults = document.getElementById('quiz-results');

    if (startBtn) startBtn.addEventListener('click', startQuiz);

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
                ${q.answers.map((a,i)=>`<button class="answer-option">${a}</button>`).join('')}
            </div>
        `;
        quizContainer.querySelectorAll('.answer-option').forEach((btn,i) => {
            btn.addEventListener('click', () => selectAnswer(i));
        });
    }

    function selectAnswer(answerIndex) {
        if (answerIndex === quizQuestions[currentQuestion].correctAnswer) score++;
        currentQuestion++;
        if (currentQuestion < quizQuestions.length) showQuestion();
        else showResults();
    }

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

    /* ===== Delete Post Modal ===== */
    const deleteModal = document.getElementById("deleteModal");
    const cancelBtn = document.getElementById("cancelDelete");
    const confirmBtn = document.getElementById("confirmDelete");
    let selectedPostId = null;

    document.querySelectorAll('.trigger-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedPostId = btn.dataset.postId;
            deleteModal.style.display = 'flex';
        });
    });

    cancelBtn.addEventListener('click', () => {
        selectedPostId = null;
        deleteModal.style.display = 'none';
    });

    confirmBtn.addEventListener('click', () => {
        if (!selectedPostId) return;
        fetch('api/delete_post.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'post_id=' + encodeURIComponent(selectedPostId)
        }).then(() => location.reload());
    });

    window.addEventListener('click', e => {
        if (e.target === deleteModal) {
            selectedPostId = null;
            deleteModal.style.display = 'none';
        }
    });

    /* ===== Options Menu (3 dots) ===== */
    document.querySelectorAll('.options-icon').forEach(icon => {
        icon.addEventListener('click', e => {
            e.stopPropagation();
            const container = icon.closest('.options-container');
            container.classList.toggle('show');
        });
    });

    window.addEventListener('click', () => {
        document.querySelectorAll('.options-container').forEach(c => c.classList.remove('show'));
    });

});
