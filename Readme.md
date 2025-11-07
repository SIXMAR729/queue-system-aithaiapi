# Queue System

This project is a simple queue management system designed to handle different categories of queues. It provides a web interface for users to get a ticket and a separate interface for administrators to manage the queues.

## Features

- **Multiple Queue Categories:** Supports multiple queue categories (e.g., red, pink, gray, green, orange, blue).
- **Ticket Generation:** Users can request a ticket for a specific category.
- **Queue Display:** A public display shows the current and next ticket numbers for each category.
- **Admin Interface:** Administrators can call the next ticket and reset the queue.
- **Speech Synthesis:** The system can announce the called ticket number using a text-to-speech API.

## Project Structure

The project is divided into two main directories:

- `public/`: Contains the public-facing files, including the main user interface, admin login, and the queue display.
- `src/`: Contains the core application logic, including database interactions, authentication, and queue management.

## Setup

1.  **Database:**
    - Create a database for the queue system.
    - Import the `database.sql` file (not included in the project) to create the necessary tables. The tables required are `tickets` and `staff`.
    - The `tickets` table should have the following columns: `id` (INT, AUTO_INCREMENT, PRIMARY KEY), `category` (VARCHAR), `ticket_number` (INT), `status` (VARCHAR, e.g., 'waiting', 'called').
    - The `staff` table should have the following columns: `id` (INT, AUTO_INCREMENT, PRIMARY KEY), `username` (VARCHAR), `password` (VARCHAR).

2.  **Configuration:**
    - Create a `config.php` file in the `src/` directory.
    - Add the following lines to `src/config.php`:
      ```php
      <?php
      // Database credentials
      define('DB_HOST', 'your_database_host');
      define('DB_USER', 'your_database_username');
      define('DB_PASS', 'your_database_password');
      define('DB_NAME', 'your_database_name');

      // API Key for text-to-speech
      define('API_KEY', 'Your-API-From-TH-AI');
      ?>
      ```
    - Create a `db_connect.php` file in the `src/` directory with the following content:
        ```php
        <?php
        require_once __DIR__ . '/config.php';

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        ?>
        ```

3.  **Web Server:**
    - Host the project on a web server with PHP and MySQL support.
    - The web root should point to the `public/` directory.

## Usage

- **User:**
    - Access the main page (`index.html`) to get a ticket.
    - Select a category and click the button to receive a ticket number.

- **Administrator:**
    - Log in through the `login.html` page.
    - After logging in, the administrator can:
        - Call the next ticket for a specific category.
        - Reset the entire queue.

- **Display:**
    - The `display.php` page shows the current status of all queues. This page is designed to be displayed on a public screen.