<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\UserProgress;
use App\Services\GoogleDriveService;
use App\Services\EpubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypingController extends Controller
{
    private GoogleDriveService $googleDriveService;
    private EpubService $epubService;

    public function __construct(GoogleDriveService $googleDriveService, EpubService $epubService)
    {
        $this->googleDriveService = $googleDriveService;
        $this->epubService = $epubService;
    }

    public function show(Book $book)
    {
        $this->authorize('view', $book);
        
        try {
            $user = Auth::user();
            
            // Set up Google Drive service with user's tokens
            $this->googleDriveService->setAccessToken($user->google_drive_token);
            
            // Download the EPUB file from Drive
            $epubContent = $this->googleDriveService->downloadFile($book->drive_file_id);
            
            // Parse the EPUB
            $epubData = $this->epubService->parseEpub($epubContent);
            
            // Get user's progress for this book
            $progress = UserProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            
            $currentChapter = 0;
            $currentOffset = 0;
            
            if ($progress) {
                // Find the current chapter based on character offset
                $chapterInfo = $this->epubService->getChapterByCharacterOffset($epubContent, $progress->character_offset);
                if ($chapterInfo) {
                    $currentChapter = $chapterInfo['chapter_index'];
                    $currentOffset = $chapterInfo['offset_in_chapter'];
                }
            }
            
            return view('typing.show', compact('book', 'epubData', 'currentChapter', 'currentOffset', 'progress'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading book: ' . $e->getMessage());
        }
    }

    public function saveProgress(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'chapter' => 'required|integer|min:0',
            'character_offset' => 'required|integer|min:0',
            'total_characters' => 'required|integer|min:0',
            'wpm' => 'nullable|numeric|min:0',
            'accuracy' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        
        // Verify the book belongs to the user
        $book = Book::where('id', $request->book_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $progressData = [
            'wpm' => $request->wpm,
            'accuracy' => $request->accuracy,
            'timestamp' => now()->toISOString(),
        ];

        UserProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'chapter' => $request->chapter,
                'character_offset' => $request->character_offset,
                'total_characters' => $request->total_characters,
                'progress_data' => $progressData,
                'last_accessed_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
