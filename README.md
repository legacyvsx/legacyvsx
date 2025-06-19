<B>LegacyVSX</b><p>
This is a php app that tracks and compares how news stories are presented in legacy media vs X. You can check it out in action at https://legacynewsvsx.news<br/><br/>
This project is completely open source and free. It requires only PHP, MySQL, and API keys for xAI and NewsAPI. It is webserver agnostic, so you can use Apache, nginx, or whatever else will run PHP. The code that automatically posts the data to X also requires an API key from X and the free software packages wkhtmltopdf/wkhtmltoimage and ImageMagick (if you're running Ubuntu, sudo apt install wkhtmltopdf imagemagick is quick and easy).

Here is a quick rundown on the files:

config.php - set your API keys here
database.php - database functions to connect to your MySQL server, uses the MySQLi PHP extension
get_news.php - gets today's global news headlines from legacy source via NewsAPI
index.php - this file, which acts as the primary frontend
main.php - responsible for invoking the functions defined in get_news.php, xai_article.php, and xai_x_posts.php. This is the file you want to execute in your crontab
table.php - handles the latest data table, index.php calls this file as an include
xai_article.php - passes a legacy news article to the xAI API to perform sentiment and emotion analysis
xai_x_posts.php - searches X (via xAI API) for posts describing a particular news story. Finds the average sentiment and dominant emotion among these posts
x_post.php - responsible for automatically posting the data to X. This should also be executed via cron slightly after main.php. Personally, I run them at 10:00 pm and 10:05 pm. Note that this requires an X API key in config.php as well as the TwitterOAuth package (easy install via composer)
