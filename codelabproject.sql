-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 10, 2026 at 03:02 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `codelabproject`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `labs`
--

CREATE TABLE `labs` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `order_index` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `instructions` text NOT NULL,
  `starter_code` text DEFAULT NULL,
  `expected_output` text DEFAULT NULL,
  `lang` enum('js','html','css','py','full') DEFAULT 'js',
  `check_mode` enum('none','exact','contains') DEFAULT 'none',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`id`, `track_id`, `order_index`, `title`, `description`, `instructions`, `starter_code`, `expected_output`, `lang`, `check_mode`, `created_at`) VALUES
(1, 1, 1, 'Variables & Types', 'Declare your first variables and explore JS data types.', '<h3>✦ Lab 1 — Variables &amp; Types</h3>\n<p>In JavaScript you declare variables with <code>let</code> or <code>const</code>.</p>\n<ul>\n  <li><code>let</code> — can be reassigned later</li>\n  <li><code>const</code> — fixed, cannot change</li>\n</ul>\n<h4>Objective</h4>\n<p>Declare a variable <code>year</code> set to <code>2025</code> and log it with <code>console.log()</code>.</p>\n<p>Your output must contain <strong>2025</strong>.</p>', '// Declare your variable here\n\n\n// Log it\nconsole.log()', '2025', 'js', 'contains', '2026-05-09 13:56:33'),
(2, 1, 2, 'Functions', 'Write reusable blocks of code with functions.', '<h3>✦ Lab 2 — Functions</h3>\n<p>Functions wrap logic you want to reuse.</p>\n<pre><code>function greet(name) {\n  return \"Hello, \" + name\n}</code></pre>\n<h4>Objective</h4>\n<p>Write a function <code>multiply(a, b)</code> that returns the product of two numbers. Log <code>multiply(6, 7)</code>.</p>\n<p>Expected output: <strong>42</strong></p>', '// Write your function here\n\n\n// Call and log it\nconsole.log(multiply(6, 7))', '42', 'js', 'contains', '2026-05-09 13:56:33'),
(3, 1, 3, 'Arrays & Loops', 'Store lists of data and iterate over them.', '<h3>✦ Lab 3 — Arrays &amp; Loops</h3>\n<p>Arrays hold ordered lists. Loops let you process each item.</p>\n<pre><code>const items = [1, 2, 3]\nfor (const item of items) {\n  console.log(item)\n}</code></pre>\n<h4>Objective</h4>\n<p>Create an array of 5 numbers. Loop through it and log the <strong>sum</strong>.</p>', '// Your array\nconst numbers = []\n\n// Loop and sum\nlet sum = 0\n\n\nconsole.log(sum)', NULL, 'js', 'none', '2026-05-09 13:56:33'),
(4, 2, 1, 'Your First HTML Page', 'Build the skeleton of a webpage from scratch.', '<h3>✦ Lab 1 — Your First HTML Page</h3>\n<p>Every webpage is built from HTML elements like headings and paragraphs.</p>\n<h4>Objective</h4>\n<p>Create a page with an <code>&lt;h1&gt;</code> that says <strong>Kuoh Academy</strong> and a <code>&lt;p&gt;</code> with any text you like.</p>', '<!-- Build your page here -->\n\n', NULL, 'html', 'none', '2026-05-09 13:56:33'),
(5, 2, 2, 'Styling with CSS', 'Add colour and personality to your HTML.', '<h3>✦ Lab 2 — Styling with CSS</h3>\n<p>CSS controls how HTML looks — colours, fonts, spacing.</p>\n<h4>Objective</h4>\n<p>Give your <code>h1</code> the colour <code>#bc8cff</code> and the body a dark background <code>#0d0f11</code>.</p>', '<style>\n  \n</style>\n\n<h1>Kuoh Academy</h1>\n<p>Your text here</p>', NULL, 'html', 'none', '2026-05-09 13:56:33'),
(6, 2, 3, 'Interactive Buttons', 'Make things happen when the user clicks.', '<h3>✦ Lab 3 — Interactive Buttons</h3>\n<p>JavaScript makes pages interactive. Use <code>onclick</code> to run code on click.</p>\n<h4>Objective</h4>\n<p>Create a button that changes a <code>&lt;div&gt;</code> text to <strong>Boosted!</strong> when clicked.</p>', '<!DOCTYPE html>\n<html>\n<body>\n  <div id=\"out\">Click the button</div>\n  <button onclick=\"\">Click me</button>\n</body>\n</html>', NULL, 'full', 'none', '2026-05-09 13:56:33'),
(7, 3, 1, 'Hello, Python', 'Your first Python program. Print to the world.', '<h3>✦ Lab 1 — Hello, Python</h3>\n<p>Python uses <code>print()</code> to output text.</p>\n<h4>Objective</h4>\n<p>Print exactly: <strong>Hello, Kuoh Academy!</strong></p>', '# Write your print statement\n', 'Hello, Kuoh Academy!', 'py', 'exact', '2026-05-09 13:56:33'),
(8, 3, 2, 'Variables & Strings', 'Store data in variables and manipulate strings.', '<h3>✦ Lab 2 — Variables &amp; Strings</h3>\n<p>Variables store values. Use f-strings to embed them in text.</p>\n<pre><code>name = \"Rias\"\nprint(f\"Hello, {name}!\")</code></pre>\n<h4>Objective</h4>\n<p>Create a variable <code>name</code> with your name and print <code>Hello, {name}!</code> using an f-string.</p>', '# Your variable\nname = \"\"\n\n# Print using f-string\nprint()', NULL, 'py', 'none', '2026-05-09 13:56:33'),
(9, 3, 3, 'Lists & Iteration', 'Store multiple values and loop through them.', '<h3>✦ Lab 3 — Lists &amp; Iteration</h3>\n<p>Lists hold multiple values. Loop through them with <code>for</code>.</p>\n<h4>Objective</h4>\n<p>Create a list of 3 club member names and print each one.</p>', '# Your list\nmembers = []\n\n# Loop and print\n', NULL, 'py', 'none', '2026-05-09 13:56:33');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tracks`
--

CREATE TABLE `tracks` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(10) NOT NULL,
  `color` varchar(20) NOT NULL,
  `difficulty` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `order_index` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tracks`
--

INSERT INTO `tracks` (`id`, `slug`, `name`, `description`, `icon`, `color`, `difficulty`, `order_index`, `created_at`) VALUES
(1, 'javascript', 'JavaScript', 'From variables to async. Master the language of the web.', '{ }', '#d29922', 'beginner', 1, '2026-05-09 13:56:33'),
(2, 'webdev', 'Web Dev', 'HTML, CSS, DOM — build things that actually render.', '</>', '#58a6ff', 'beginner', 2, '2026-05-09 13:56:33'),
(3, 'python', 'Python', 'Clean syntax, powerful ideas. Learn to think in Python.', '>>', '#4ec9b0', 'beginner', 3, '2026-05-09 13:56:33'),
(4, 'dsa', 'Data Structures', 'Arrays, trees, graphs — the hard stuff that makes you sharp.', '[ ]', '#f85149', 'advanced', 4, '2026-05-09 13:56:33');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `email`, `password`, `role`, `is_active`, `created_at`, `last_login`) VALUES
(2, 'issei', 'youkouh@gmail.com', '$2y$10$8kBCRAFVobOplseN9Th74.a8dtH8IQ5UmBq3A1XN86B7ZDoLMxTuK', 'student', 1, '2026-05-06 13:53:50', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `lab_id`, `completed`, `completed_at`, `attempts`) VALUES
(1, 2, 1, 1, '2026-05-09 14:12:54', 2);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`);

--
-- Indeksy dla tabeli `tracks`
--
ALTER TABLE `tracks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_lab` (`user_id`,`lab_id`),
  ADD KEY `lab_id` (`lab_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `labs`
--
ALTER TABLE `labs`
  ADD CONSTRAINT `labs_ibfk_1` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
