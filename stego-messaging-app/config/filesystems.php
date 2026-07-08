<?php

// 1. Grab raw string environment data
$rawCredentials = env('FIREBASE_CREDENTIALS');
$firebaseAuthArray = [];

if (!empty($rawCredentials)) {
    // Try decoding directly first (in case the environment keeps it as clean JSON)
    $firebaseAuthArray = json_decode($rawCredentials, true);

    // If direct decoding fails, it means it contains literal escape characters from the environment
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Strip accidental outer quotes if Render or your setup preserved them
        $cleanCredentials = trim($rawCredentials, '"\'');
        
        // ONLY clean up the escaped quotes. Leave \n alone so json_decode can parse them natively!
        $cleanCredentials = str_replace('\"', '"', $cleanCredentials);
        
        // Decode the cleaned string
        $firebaseAuthArray = json_decode($cleanCredentials, true) ?? [];
    }
}

// 2. Fallback to local file if the environment variable was missing or failed completely
if (empty($firebaseAuthArray) && file_exists(storage_path('app/firebase-auth.json'))) {
    $firebaseAuthArray = json_decode(file_get_contents(storage_path('app/firebase-auth.json')), true) ?? [];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],
    
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],
    
        'firebase' => [
            'driver' => 'gcs',
            'key_file' => $firebaseAuthArray,
            'bucket' => env('FIREBASE_STORAGE_BUCKET'),
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'bucket' => env('FIREBASE_STORAGE_BUCKET'),
            'key_file' => $firebaseAuthArray,
            'metadata' => [
                'acl' => [], 
                'predefinedAcl' => null, 
            ],
            'throw' => true, 
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];