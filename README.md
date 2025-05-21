.

> # Hotel Offers Management System

## Overview
The Hotel Offers Management System is a web application that allows users to manage hotel offers. Users can register, log in, create, edit, and delete their hotel offers. The application provides a user-friendly interface for both guests and registered users to view available offers.

## Features
- User authentication (registration, login, logout)
- Create, edit, and delete hotel offers
- Display hotel offers based on user authentication status
- Responsive design with a clean and modern user interface

## File Structure
```
hotel-app-main
├── index.php               # Main entry point of the application
├── assets
│   └── css
│       ├── auth.css        # Styles for authentication pages
│       └── style.css       # General styles for the application
├── auth
│   ├── login.php           # User login functionality
│   ├── logout.php          # User logout functionality
│   └── register.php        # User registration functionality
├── includes
│   ├── auth_functions.php   # Utility functions for authentication
│   ├── config.php          # Configuration settings (database, base URL)
│   ├── footer.php          # Footer section for web pages
│   └── header.php          # Header section for web pages
├── offers
│   ├── create.php          # Create new hotel offers
│   ├── delete.php          # Delete existing hotel offers
│   ├── edit.php            # Edit existing hotel offers
│   └── index.php           # Display list of hotel offers
└── README.md               # Documentation for the project
```

## Setup Instructions
1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Create a database in your MySQL server and import the necessary tables.
4. Update the `includes/config.php` file with your database connection details.
5. Start a local server (e.g., XAMPP, WAMP) and navigate to `http://localhost/hotel-offers-app` in your web browser.

## Usage
- **Registration**: New users can create an account by navigating to the registration page.
- **Login**: Registered users can log in to access their offers.
- **Manage Offers**: Users can create, edit, and delete their hotel offers from the offers section.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is open-source and available under the MIT License.
