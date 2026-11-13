<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Portfolio | Home</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

   
    <header class="header">

        <nav class="navbar container">

            <!-- Logo -->
            <a href="index.html" class="logo">
                Jim<span>well</span>
            </a>

            <!-- Mobile Menu Button -->
            <button
                class="menu-toggle"
                aria-label="Open navigation"
                aria-expanded="false">
                ☰
            </button>

            <!-- Navigation Links -->
            <div class="nav-links">

                <a href="index.html">Home</a>

                <a href="about.html">About</a>

                <a href="skills.html">Skills</a>

                <a href="projects.html">Projects</a>

                <a href="resume.html">Resume</a>

                <a href="contact.html">Contact</a>

                <!-- Dark / Light Mode -->
                <button
                    class="theme-toggle"
                    aria-label="Toggle dark mode">
                    🌙
                </button>

            </div>

        </nav>

    </header>


    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main>

        <!-- HERO SECTION -->
        <section class="hero container">

            <!-- Introduction -->
            <div class="hero-text">

                <p class="eyebrow">
                    HELLO, I'M
                </p>

                <h1>
                    JIMWELL ESICAN
                </h1>

                <h2>
                    I am a
                    <span id="typing-text">
                        NORSU Student
                    </span>
                </h2>

                <p>
                    I create clean designs, simple websites,
                    and digital projects while learning more
                    about technology and creative design.
                </p>


                <!-- Buttons -->
                <div class="hero-buttons">

                    <a
                        href="projects.html"
                        class="btn primary">
                        View My Work
                    </a>

                    <a
                        href="contact.html"
                        class="btn secondary">
                        Contact Me
                    </a>

                </div>

            </div>


            <!-- PROFILE -->
            <div class="profile-card">

          <img
             src="assets/profilepic.jpg"
            alt="My Profile Picture"
            class="profile-image">

           <p>
             Creative Student
           </p>

           <span>
             HTML • CSS • JavaScript
           </span>

           </div>

            </div>

        </section>


        <section class="quick-section">

            <div class="container">

                <div class="section-heading">

                    <p class="eyebrow">
                        WELCOME
                    </p>

                    <h2>
                        A little about my portfolio
                    </h2>

                </div>


                <!-- Information Cards -->
                <div class="info-grid">


                    <!-- CARD 1 -->
                    <article class="info-card">

                        <div class="icon">
                            🎨
                        </div>

                        <h3>
                            Creative Design
                        </h3>

                        <p>
                            I enjoy making layouts, logos,
                            posters, and digital artwork.
                        </p>

                    </article>


                    <!-- CARD 2 -->
                    <article class="info-card">

                        <div class="icon">
                            💻
                        </div>

                        <h3>
                            Web Development
                        </h3>

                        <p>
                            I am learning how HTML, CSS,
                            and JavaScript work together.
                        </p>

                    </article>


                    <!-- CARD 3 -->
                    <article class="info-card">

                        <div class="icon">
                            📚
                        </div>

                        <h3>
                            Always Learning
                        </h3>

                        <p>
                            Every project helps me improve
                            my skills and understand new tools.
                        </p>

                    </article>


                </div>

            </div>

        </section>

    </main>


   

    <button
        id="scrollTop"
        class="scroll-top"
        aria-label="Scroll to top">

        ↑

    </button>




    <footer class="footer">

        <p>
            © 2026 Jimwell Esican.
            Built with HTML, CSS & JavaScript.
        </p>

    </footer>


    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script src="js/script.js"></script>

</body>
</html>