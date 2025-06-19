<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legacy VS. X - News Analysis</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Header */
        header {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            color: white;
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(45deg, #fff, #667eea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 1rem;
            flex-wrap: wrap;
        }

        nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }

        nav a:hover::before {
            left: 100%;
        }

        nav a:hover, nav a.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Desktop navigation - hide mobile text */
        @media (min-width: 769px) {
            nav a .mobile-text {
                display: none;
            }

            nav a .full-text {
                display: inline;
            }
        }

        /* Main Content */
        main {
            padding-top: 100px;
            min-height: 100vh;
        }

        section {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            margin: 2rem 0;
            padding: 3rem 2rem;
            border-radius: 24px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            opacity: 1;
            transform: translateY(0);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateY(30px) scale(0.98);
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1);
            }
        }

        h2 {
            color: #1a1a1a;
            margin-bottom: 2rem;
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        /* Hero Section - INCREASED SPACING */
        .hero {
            text-align: center;
            padding: 4rem 0 2rem 0;
            background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
            border-radius: 20px;
            margin: 2rem 0 1rem 0;
            border: 1px solid rgba(102,126,234,0.2);
        }

        .hero h2 {
            font-size: 2.2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.1rem;
            color: #666;
            max-width: 900px;
            margin: 0 auto 1rem auto;
            line-height: 1.8;
            text-align: left;
            padding: 0 2rem;
        }

        /* Comparison Features - MOVED FURTHER DOWN */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 6rem 0 3rem 0;
            clear: both;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 1rem;
            color: white;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            margin: 2rem 0;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th {
            background: linear-gradient(135deg, #1a1a1a, #333);
            color: white;
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
            font-size: 0.9rem;
        }

        /* tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
            transition: all 0.2s ease;
        } */

        .sentiment-positive { color: #28a745; font-weight: 600; }
        .sentiment-negative { color: #dc3545; font-weight: 600; }
        .sentiment-neutral { color: #6c757d; font-weight: 600; }

        .emotion-tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .emotion-joy { background: #d4edda; color: #155724; }
        .emotion-anger { background: #f8d7da; color: #721c24; }
        .emotion-fear { background: #fff3cd; color: #856404; }
        .emotion-surprise { background: #cce5ff; color: #004085; }

        /* Contact */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .contact-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 2.5rem 2rem;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }

        .contact-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .contact-item h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .contact-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-item a:hover {
            color: #764ba2;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            header {
                padding: 0.8rem 0;
            }

            .header-content {
                flex-direction: column;
                gap: 0.8rem;
            }

            .logo h1 {
                font-size: 1.4rem;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            /* Mobile navigation with shorter text */
            nav a {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }

            nav ul {
                justify-content: center;
                gap: 0.3rem;
            }

            /* Hide full text on mobile, show abbreviated */
            nav a .full-text {
                display: none;
            }

            nav a .mobile-text {
                display: inline;
            }

            main {
                padding-top: 120px;
            }

            .hero {
                padding: 2rem 1rem 6rem 1rem;
                margin-bottom: 2rem;
            }

            .hero h2 {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }

            .hero p {
                font-size: 1rem;
                padding: 0 1rem;
                margin-bottom: 4rem;
            }

            h2 {
                font-size: 2rem;
            }

            section {
                padding: 2rem 1rem;
                margin: 1rem 0;
            }

            /* Ensure proper spacing on mobile */
            .features-grid {
                margin: 4rem 0 2rem 0;
                gap: 1.5rem;
            }

            .feature-card {
                padding: 1.5rem;
            }
        }

        /* Additional mobile fixes for very small screens */
        @media (max-width: 480px) {
            .hero {
                padding: 1.5rem 0.5rem 5rem 0.5rem;
                margin-bottom: 6rem;
            }

            .hero p {
                padding: 0 0.5rem;
                margin-bottom: 3rem;
            }

            .features-grid {
                gap: 1rem;
                margin: 4rem 0 2rem 0;
            }

            .feature-card {
                padding: 1.25rem;
            }
        }

        /* Footer */
        footer {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        footer p {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">LvX</div>
                    <h1>Legacy VS. X</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="#home" class="nav-link active">
                            <span class="full-text">Home</span>
                            <span class="mobile-text">Home</span>
                        </a></li>
                        <li><a href="#latest-data" class="nav-link">
                            <span class="full-text">Latest Data</span>
                            <span class="mobile-text">Latest</span>
                        </a></li>
                        <li><a href="#archive" class="nav-link">
                            <span class="full-text">Archive</span>
                            <span class="mobile-text">Archive</span>
                        </a></li>
                        <li><a href="#source-code" class="nav-link">
                            <span class="full-text">Source Code</span>
                            <span class="mobile-text">Source</span>
                        </a></li>
                        <li><a href="#contact" class="nav-link">
                            <span class="full-text">Contact</span>
                            <span class="mobile-text">Contact</span>
                        </a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Home Section -->
        <section id="home" class="active">
            <div class="hero">
                <img src="logo.jpg" style="max-width:45%;max-height:45%"><br/><br/>
				<h2>Media Analysis Reimagined</h2>
                <p>
				In today's rapidly evolving media landscape, understanding how different platforms shape public discourse has become crucial for informed citizenship. Traditional legacy media outlets - newspapers, television networks, and established digital publications - operate under editorial standards developed over decades. Meanwhile, social media platforms like X provide real-time, unfiltered reactions from millions of users, creating a dynamic environment where news spreads instantly but with varying degrees of accuracy and emotional intensity. This AI-powered app monitors top global headlines and finds corresponding X posts, comparing how the same stories are covered, discussed, and received across these fundamentally different media environments. By examining sentiment patterns, emotional responses, and narrative framing, we can discover hidden patterns that influence how society processes information, forms opinions, and makes decisions. This comparative approach doesn't seek to determine which platform is "better," but rather to illuminate how each contributes to the complex tapestry of modern news consumption, helping readers develop media literacy skills essential for navigating our interconnected information age. Specifically, news stories are analyzed for overall sentiment (on a 0-1 scale where 0=very negative, 1=very positive) as well as the dominant emotion expressed, from both legacy sources and X posts. The data is then conveniently displayed in a table below (see the Latest Data section) for users to peruse, along with a commentary on the differences. The data automatically updates each day at 10pm CST (to capture a full day's worth of news stories), and is automatically posted here as well as on our X account, <a href="https://x.com/legacy_vs_x">@Legacy_VS_X</a>.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">Sentiment & Emotion Analysis</div>
                    <div class="feature-description">Advanced AI algorithms analyze emotional tone and sentiment across legacy media and X posts to reveal hidden biases and perspectives. The latest xAI model, called grok-3-latest, provides state-of-the-art emotion detection with extreme specifity through natural language processing. As of this writing this page, it can detect ~45 emotions (e.g. joy, sadness, anger, fear, surprise, disgust, amusement, gratitude, admiration, concern, and many more), with more constantly being added.  
					</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <div class="feature-title">Real-time Tracking</div>
                    <div class="feature-description">Compare news stories in legacy media to X right as it unfolds, capturing the evolution of public discourse.</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <div class="feature-title">Trend Analysis</div>
                    <div class="feature-description">Are there topics that differ significantly in the way they are presented in legacy media vs. social media? Analyzing the differences over a period of time can perhaps reveal unexpected patterns and truths. Once data ample data has been collected, I plan to write an article exploring this.</div>
                </div>
            </div>
        </section>

        <!-- Latest Data Section -->
        <section id="latest-data">
            <h2>Latest Data</h2>
            <p style="text-align: center; font-size: 1.1rem; color: #666; margin-bottom: 3rem;">Comparison of world news coverage between legacy media outlets and social media discourse on X. Data is automatically updated daily at 10pm to capture evolving narratives based on a full day's worth of news headlines.</p>
            <?php
		// Get the data begins here
		include("table.php");
		?>
        </section>

        <!-- Archive Section -->
        <section id="archive">
            <h2>Archive</h2>
            <p style="text-align: center; font-size: 1.1rem; color: #666; margin-bottom: 2rem;">To access data for a previous date, please refer to our account on X, which automatically posts the current day's data at 10pm CST daily.</p>
            
            
            <p style="text-align: center; margin-top: 2rem;"><a href="https://x.com/legacyvsx" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 1.1rem;">Explore Full Archive →</a></p>
        </section>

        <!-- Source Code Section -->
        <section id="source-code">
            <h2>Source Code</h2>
            <p style="text-align: center; font-size: 1.1rem; color: #666; margin-bottom: 2rem;">This project is completely open source and free. It requires only PHP, MySQL, and API keys for <a href="https://x.ai">xAI</a> and <a href="https://newsapi.org">NewsAPI</a>. It is webserver agnostic, so you can use Apache, nginx, or whatever else will run PHP. The code that automatically posts the data to X also requires an API key from X and the free software package <a href="https://wkhtmltopdf.org">wkhtmltopdf/wkhtmltoimage</a> (if you're running Ubuntu, <i>sudo apt install wkhtmltopdf</i> is quick and easy).<br/><br/>
			Here is a quick rundown on the files:
			<ul style="text-align: left; font-size: 1.1rem; color: #666;">
				<li><i>config.php</i> - set your API keys here</li>
				<li><i>database.php</i> - database functions to connect to your MySQL server, uses the MySQLi PHP extension</li>
				<li><i>get_news.php</i> - gets today's global news headlines from legacy source via NewsAPI</li>
				<li><i>index.php</i> - this file, which acts as the primary frontend</li>
				<li><i>main.php</i> - responsible for invoking the functions defined in get_news.php, xai_article.php, and xai_x_posts.php. This is the file you want to execute in your crontab</li>
				<li><i>table.php</i> - handles the latest data table, index.php calls this file as an include</li>
				<li><i>xai_article.php</i> - passes a legacy news article to the xAI API to perform sentiment and emotion analysis</li>
				<li><i>xai_x_posts.php</i> - searches X (via xAI API) for posts describing a particular news story. Finds the average sentiment and dominant emotion among these posts</li>
				<li><i>x_post.php</i> - responsible for automatically posting the data to X. This should also be executed via cron slightly after main.php. Personally, I run them at 10:00 pm and 10:05 pm. Note that this requires an X API key in config.php as well as the <a href="https://twitteroauth.com">TwitterOAuth</a> package (easy install via composer)</li>
			
			</ul>

			</p>
            
            
            
            <p style="text-align: center; margin-top: 2rem;"><a href="https://github.com/legacyvsx/legacyvsx" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 1.1rem;">View on GitHub →</a></p>
        </section>

        <!-- Contact Section -->
        <section id="contact">
            <h2>Contact</h2>
            
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">𝕏</div>
                    <h3>Follow</h3>
                    <p><a href="https://x.com/h45hb4ng">@h45hb4ng</a></p>
                    
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <h3>Email</h3>
                    <p><a href="mailto:hi[at]legacyvsx.news">hi[at]legacyvsx.news</a></p>
                    
                </div>
                
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 LegacyVSX.News - Analyzing news across media landscapes with transparency and insight</p>
        </div>
    </footer>

    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('section');

            // Handle navigation clicks for smooth scrolling
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all nav links
                    navLinks.forEach(nl => nl.classList.remove('active'));
                    
                    // Add active class to clicked nav link
                    this.classList.add('active');
                    
                    // Smooth scroll to section
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add intersection observer for scroll-based navigation updates
            const observerOptions = {
                root: null,
                rootMargin: '-20% 0px -60% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        
                        // Update active nav link based on visible section
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === '#' + id) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }, observerOptions);

            // Observe all sections
            sections.forEach(section => {
                observer.observe(section);
            });

            // Add smooth hover effects to cards
            const cards = document.querySelectorAll('.feature-card, .contact-item');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                    this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click animation to nav links
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Initialize animation styles - removed to prevent scroll animations
            const animatedElements = document.querySelectorAll('.feature-card');
            animatedElements.forEach(element => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            });

            // Add loading animation to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                row.style.transition = `all 0.5s ease ${index * 0.1}s`;
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, 500 + (index * 100));
            });

            // Add parallax effect to hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.hero');
                if (hero) {
                    hero.style.transform = `translateY(${scrolled * 0.3}px)`;
                }
            });
        });
    </script> -->
</body>
</html>
