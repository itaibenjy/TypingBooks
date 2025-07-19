# TypingBooks

A Laravel-based web application for improving typing skills by practicing with your favorite books. Import EPUB files from Google Drive and track your progress with real-time WPM and accuracy metrics.

## Features

- **Google OAuth Integration**: Secure authentication with Google Drive access
- **EPUB Support**: Import and parse EPUB files from your Google Drive
- **Real-time Typing Metrics**: Track WPM, accuracy, and character count
- **Progress Tracking**: Save and resume your typing progress across sessions
- **Theme Customization**: Choose from light, dark, and sepia themes
- **Chapter Navigation**: Easy navigation between book chapters
- **Responsive Design**: Works on desktop and mobile devices

## Requirements

- PHP 8.2 or higher
- Laravel 12
- SQLite database
- Google Cloud Platform account with OAuth 2.0 credentials

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd typing-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment variables**
   Copy `.env.example` to `.env` and configure:
   ```env
   APP_NAME=TypingBooks
   APP_URL=http://localhost:8000
   
   # Google OAuth Configuration
   GOOGLE_CLIENT_ID=your-google-client-id
   GOOGLE_CLIENT_SECRET=your-google-client-secret
   GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## Google OAuth Setup

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google Drive API and Google+ API
4. Go to "Credentials" and create an OAuth 2.0 Client ID
5. Set the authorized redirect URI to: `http://localhost:8000/auth/google/callback`
6. Copy the Client ID and Client Secret to your `.env` file

## Usage

1. **Sign in with Google**: Visit the application and click "Sign in with Google"
2. **Import Books**: Click "Import from Drive" to browse your Google Drive for EPUB files
3. **Start Typing**: Select a book and begin typing practice
4. **Track Progress**: Monitor your WPM, accuracy, and overall progress
5. **Customize Themes**: Choose from different reading themes for comfort

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/GoogleController.php    # Google OAuth handling
│   ├── BookController.php           # Book management
│   ├── TypingController.php         # Typing interface
│   └── ThemeController.php          # Theme management
├── Models/
│   ├── Book.php                     # Book model
│   ├── Theme.php                    # Theme model
│   ├── UserProgress.php             # Progress tracking
│   └── User.php                     # User model (extended)
├── Services/
│   ├── GoogleDriveService.php       # Google Drive API integration
│   └── EpubService.php              # EPUB parsing and processing
└── ...

resources/views/
├── auth/login.blade.php             # Login page
├── books/index.blade.php            # Books listing
├── typing/show.blade.php            # Typing interface
├── dashboard.blade.php              # User dashboard
└── layouts/app.blade.php            # Main layout
```

## Database Schema

- **users**: Extended with Google OAuth fields
- **books**: User's imported books with metadata
- **themes**: User's custom themes
- **user_progress**: Typing progress and statistics

## API Endpoints

- `GET /auth/google` - Initiate Google OAuth
- `GET /auth/google/callback` - OAuth callback
- `GET /dashboard` - User dashboard
- `GET /books` - List user's books
- `POST /books` - Import new book
- `GET /books/{book}/typing` - Start typing session
- `POST /typing/progress` - Save typing progress

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please open an issue on the GitHub repository or contact the development team.
