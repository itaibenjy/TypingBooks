<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private Google_Client $client;
    private Google_Service_Drive $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->setScopes([
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        $this->service = new Google_Service_Drive($this->client);
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function getAccessToken(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new \Exception('Error getting access token: ' . $token['error']);
        }

        return $token;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->client->setAccessToken($accessToken);
    }

    public function refreshToken(string $refreshToken): array
    {
        $this->client->setRefreshToken($refreshToken);
        $token = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
        
        if (isset($token['error'])) {
            throw new \Exception('Error refreshing token: ' . $token['error']);
        }

        return $token;
    }

    public function listFiles(string $query = ''): array
    {
        $optParams = [
            'pageSize' => 100,
            'fields' => 'files(id,name,mimeType,size,modifiedTime)',
        ];

        if ($query) {
            $optParams['q'] = $query;
        }

        $results = $this->service->files->listFiles($optParams);
        return $results->getFiles();
    }

    public function getFile(string $fileId): \Google_Service_Drive_DriveFile
    {
        return $this->service->files->get($fileId);
    }

    public function downloadFile(string $fileId): string
    {
        $content = $this->service->files->get($fileId, [
            'alt' => 'media'
        ])->getBody()->getContents();

        return $content;
    }

    public function createFile(string $name, string $content, string $mimeType = 'text/plain'): \Google_Service_Drive_DriveFile
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile([
            'name' => $name,
            'mimeType' => $mimeType
        ]);

        return $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart'
        ]);
    }

    public function updateFile(string $fileId, string $content): \Google_Service_Drive_DriveFile
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile();

        return $this->service->files->update($fileId, $fileMetadata, [
            'data' => $content,
            'uploadType' => 'multipart'
        ]);
    }

    public function isTokenExpired(): bool
    {
        return $this->client->isAccessTokenExpired();
    }
} 