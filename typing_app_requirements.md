**Software Requirements Specification**  
**Project:** Personal Typing Web App  
**Version:** 1.0  
**Date:** July 19, 2025

---

## 1. Introduction

### 1.1 Purpose  
Define the functional and non-functional requirements for a zero-cost, Laravel+Blade-based web typing application leveraging Google Drive for user storage.

### 1.2 Scope  
The system will allow users to:
- Authenticate via Google and authorize Drive access
- Browse a curated library of open‑source books
- Import and store personal EPUBs in their Google Drive
- Customize and apply reading/typing themes (predefined and user‑created)
- Track and resume typing progress per book/chapter

### 1.3 Definitions  
- **User Drive**: Each user’s Google Drive account
- **Open‑Source Books**: Public domain EPUBs hosted by the system
- **Theme**: A set of UI style parameters (fonts, colors, spacing)

## 2. Overall Description

### 2.1 Product Perspective  
Stand‑alone web application built on Laravel 12 and Blade templates. No paid storage; uses SQLite for metadata and Flysystem adapter for Drive.

### 2.2 User Classes  
- **Reader/Typist**: Authenticates, reads/types books, customizes themes
- **Administrator**: Manages open‑source book repository (offline)

### 2.3 Operating Environment  
- Web server supporting PHP 8+, Laravel 12
- SQLite database file
- Google Drive API access via OAuth

### 2.4 Constraints  
- Zero external storage cost
- OAuth scope must include Drive read/write
- SQLite metadata size negligible

## 3. Functional Requirements

### 3.1 Authentication & Authorization
- FR1: Users sign in only via Google OAuth
- FR2: On first login, obtain and store Drive refresh token
- FR3: Automatically refresh access tokens when expired

### 3.2 Book Management
- FR4: Display system library of open‑source books
- FR5: Allow users to select & add open‑source books to their library (copies live on their Drive)
- FR6: Enable users to import personal EPUBs from Drive
- FR7: Store book metadata (title, author, Drive path) in SQLite

### 3.3 Theme Management
- FR8: Provide 3–5 predefined themes (e.g., light, dark, sepia)
- FR9: Allow users to create/edit custom themes (font size, line‑height, colors)
- FR10: Persist user themes in SQLite
- FR11: Apply selected theme to reading/typing UI

### 3.4 Typing & Progress Tracking
- FR12: Render one chapter or page segment at a time
- FR13: Capture keystrokes; calculate WPM and accuracy in real time
- FR14: Save current position (book, chapter, character offset) in a JSON file on Drive
- FR15: On revisiting a book, load JSON checkpoint and position user accordingly

### 3.5 UI/UX
- FR16: Responsive layout for desktop and mobile browsers
- FR17: Keyboard focus and error highlighting during typing
- FR18: Dashboard showing list of books, progress percentages, and quick‑resume links

## 4. Non-Functional Requirements

### 4.1 Performance
- NFR1: Chapter load time < 500 ms (after initial parse)
- NFR2: Token refresh transparent to user, < 1 s API latency

### 4.2 Reliability
- NFR3: Automatic retry on Drive API failures (up to 3)  
- NFR4: Local caching of parsed HTML to minimize repeated EPUB unzipping

### 4.3 Security
- NFR5: HTTPS mandatory for OAuth and APIs  
- NFR6: Sanitize all EPUB content before rendering to prevent XSS

### 4.4 Usability
- NFR7: Onboarding flow guides user through Drive connection and first book import
- NFR8: Accessible font sizes and contrast ratios per WCAG AA

### 4.5 Maintainability
- NFR9: Modular controllers (Auth, Books, Progress, Themes)  
- NFR10: Unit tests covering OAuth, parsing, progress save/load

## 5. External Interfaces

- **Google OAuth**: Scope `https://www.googleapis.com/auth/drive`  
- **Google Drive API** (via Flysystem adapter)  
- **Epub Parser Library**: e.g., `smalot/epub`

---

*Next Steps:* Validate scope, refine user stories, estimate implementation effort, and prepare sprint backlog.

