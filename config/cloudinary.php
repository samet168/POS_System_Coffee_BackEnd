<?php

// return [

//     'cloud_url' => env('CLOUDINARY_URL'),

// ];
return [
    'cloud' => env('CLOUDINARY_CLOUD_NAME'),

    'url' => env('CLOUDINARY_URL'),

    'api_key' => env('CLOUDINARY_API_KEY'),

    'api_secret' => env('CLOUDINARY_API_SECRET'),
];