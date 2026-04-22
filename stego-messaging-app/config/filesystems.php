<?php

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
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
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
    
        // Add this new Firebase/Google Cloud block
        'firebase' => [
            'driver' => 'gcs',
            'key_file' => storage_path('app/firebase-auth.json'), // Your JSON key location
            'bucket' => env('FIREBASE_STORAGE_BUCKET'),
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'visibility' => 'public',
            'throw' => true, // Set to true so you can see errors if the upload fails
        ],
        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'bucket' => env('FIREBASE_STORAGE_BUCKET'),
            // We use 'key_file' to hold the ARRAY of data, as the Google Client prefers
            'key_file' => json_decode(file_get_contents(storage_path('app/firebase-auth.json')), true),
            'metadata' => [
                'acl' => [], // Send an empty array so no ACL is created
                'predefinedAcl' => null, 
            ],
            'throw' => true, // This forces Laravel to show errors instead of returning 'false'
        ],
    
        // You can delete the 's3' block entirely if it's distracting!
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
