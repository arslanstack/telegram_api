<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     // Prepare the photo using InputFile::create()
//     $photo = InputFile::create('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9hxIGIRPVvvpnSQjDGNI0undzKEHbVYvWe-7bvt9W4A&s');

//     // Send the photo to the chat.
//     Telegram::sendPhoto([
//         'chat_id' => '6491925544', // Replace with your chat ID
//         'photo' => $photo,
//     ]);

//     // Get the bot updates.
//     $response = Telegram::getUpdates();

//     return $response;
// });
Route::post('/upload', [App\Http\Controllers\UploadController::class, 'store']);
Route::get('/download-file/{id}', [App\Http\Controllers\UploadController::class, 'downloadFile']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
