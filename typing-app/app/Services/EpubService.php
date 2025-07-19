<?php

namespace App\Services;

use Smalot\Epub\Epub;

class EpubService
{
    public function parseEpub(string $epubContent): array
    {
        // Create a temporary file to parse the EPUB
        $tempFile = tempnam(sys_get_temp_dir(), 'epub_');
        file_put_contents($tempFile, $epubContent);

        try {
            $epub = new Epub($tempFile);
            
            $metadata = [
                'title' => $epub->getTitle(),
                'author' => $epub->getAuthor(),
                'description' => $epub->getDescription(),
                'language' => $epub->getLanguage(),
                'publisher' => $epub->getPublisher(),
                'identifier' => $epub->getIdentifier(),
            ];

            $chapters = [];
            $spine = $epub->getSpine();

            foreach ($spine as $index => $item) {
                $content = $epub->getChapter($item['href']);
                
                // Clean HTML content for typing
                $cleanContent = $this->cleanHtmlContent($content);
                
                $chapters[] = [
                    'index' => $index,
                    'title' => $item['title'] ?? "Chapter " . ($index + 1),
                    'href' => $item['href'],
                    'content' => $cleanContent,
                    'word_count' => str_word_count(strip_tags($cleanContent)),
                    'character_count' => strlen(strip_tags($cleanContent)),
                ];
            }

            return [
                'metadata' => $metadata,
                'chapters' => $chapters,
                'total_chapters' => count($chapters),
            ];

        } finally {
            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    private function cleanHtmlContent(string $html): string
    {
        // Remove script and style tags
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        
        // Convert common HTML entities
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove extra whitespace and normalize line breaks
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
        
        // Remove HTML tags but preserve line breaks
        $text = strip_tags($html);
        
        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove multiple consecutive line breaks
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);
        
        return trim($text);
    }

    public function getChapterContent(string $epubContent, int $chapterIndex): ?array
    {
        $epubData = $this->parseEpub($epubContent);
        
        if (isset($epubData['chapters'][$chapterIndex])) {
            return $epubData['chapters'][$chapterIndex];
        }
        
        return null;
    }

    public function getChapterByCharacterOffset(string $epubContent, int $characterOffset): ?array
    {
        $epubData = $this->parseEpub($epubContent);
        $currentOffset = 0;
        
        foreach ($epubData['chapters'] as $index => $chapter) {
            $chapterLength = $chapter['character_count'];
            
            if ($characterOffset >= $currentOffset && $characterOffset < $currentOffset + $chapterLength) {
                return [
                    'chapter_index' => $index,
                    'chapter' => $chapter,
                    'offset_in_chapter' => $characterOffset - $currentOffset,
                ];
            }
            
            $currentOffset += $chapterLength;
        }
        
        return null;
    }
} 