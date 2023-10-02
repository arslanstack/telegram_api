<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Http;
use App\Models\File;
use Config;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            // get extension
            $extension = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getMimeType();
            $file = InputFile::create($uploadedFile->getPathname(), $mimeType, $originalName);
            $chatId = '6491925544'; // Replace with your chat ID
            $fileId = null; // Initialize file_id variable
            if (str_starts_with($mimeType, 'image')) {
                $response = Telegram::sendPhoto([
                    'chat_id' => $chatId,
                    'photo' => $file,
                    'caption' => $originalName,
                ]);
                $fileId = $response->photo[0]['file_id'];
            } elseif (str_starts_with($mimeType, 'video')) {
                $response = Telegram::sendVideo([
                    'chat_id' => $chatId,
                    'video' => $file,
                    'caption' => $originalName,
                ]);

                // Extract the file_id from the response
                $fileId = $response->video['file_id'];
            } else {
                $response = Telegram::sendDocument([
                    'chat_id' => $chatId,
                    'document' => $file,
                    'caption' => $originalName,
                ]);

                // Extract the file_id from the response
                $fileId = $response->document['file_id'];
            }
            // Save the file_id to the database
            $file = File::create([
                'file_id' => $fileId,
                'file_name' => $originalName,
            ]);
            return response()->json(['file_id' => $fileId]);
        } else {
            return 'No file uploaded.';
        }
    }


    public function downloadFile($id)
    {
        $file = File::find($id);
        if (!$file) {
            return response()->json(['error' => 'File not found.'], 404);
        }
        if ($file->file_url) {
            $file_url = $file->file_url;
            $file_name = rand(1, 100) . $file->file_name;
            $file_path = public_path('/assets/' . $file_name);
            $file_content = file_get_contents($file_url);
            file_put_contents($file_path, $file_content);
            return response()->download($file_path);
        } else {
            $fileId = $file->file_id;
            $botToken = config('telegram.bots.mybot.token'); // Get the bot token from config
            $telegramApiUrl = 'https://api.telegram.org/bot' . $botToken . '/getFile?file_id=' . $fileId;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return response()->json(['error' => 'cURL Error: ' . curl_error($ch)], 500);
            }
            curl_close($ch);
            $response = json_decode($response, true);
            if ($response['ok']) {
                $file_path = $response['result']['file_path'];
                $file_url = 'https://api.telegram.org/file/bot' . $botToken . '/' . $file_path;
                // store file in local storage with name from database record
                $file_name = rand(1, 100) . $file->file_name;
                $file_path = public_path('/assets/' . $file_name);
                $file_content = file_get_contents($file_url);
                file_put_contents($file_path, $file_content);
                $file->file_url = $file_url;
                $file->save();
                return response()->download($file_path);
            } else {
                return response()->json(['error' => 'Invallid File ID: '], 500);
            }
        }
    }
}
