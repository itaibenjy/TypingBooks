<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\GoogleDriveService;
use App\Services\EpubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    private GoogleDriveService $googleDriveService;
    private EpubService $epubService;

    public function __construct(GoogleDriveService $googleDriveService, EpubService $epubService)
    {
        $this->googleDriveService = $googleDriveService;
        $this->epubService = $epubService;
    }

    public function index()
    {
        $books = Auth::user()->books()->latest()->get();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'drive_file_id' => 'required|string',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
        ]);

        $book = Book::create([
            'user_id' => Auth::id(),
            'drive_file_id' => $request->drive_file_id,
            'title' => $request->title,
            'author' => $request->author,
            'file_type' => 'epub',
        ]);

        return redirect()->route('books.index')->with('success', 'Book added successfully!');
    }

    public function show(Book $book)
    {
        $this->authorize('view', $book);
        return view('books.show', compact('book'));
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Book removed successfully!');
    }

    public function importFromDrive()
    {
        try {
            $user = Auth::user();
            
            // Set up Google Drive service with user's tokens
            $this->googleDriveService->setAccessToken($user->google_drive_token);
            
            // List EPUB files from user's Drive
            $files = $this->googleDriveService->listFiles("mimeType='application/epub+zip'");
            
            return response()->json(['files' => $files]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
