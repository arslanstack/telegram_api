# Telegram API Cloud Drive

Utilize your Telegram account as a cloud drive by leveraging the Telegram API. Enjoy the following features:

## File Upload
- Upload unlimited files, each with a size of up to 2GB.
- Utilize the API endpoint for file upload: `http://127.0.0.1:8000/api/upload`
- Use Postman for a convenient POST request with the following parameters:
  ```json
  [
    {
      "key": "file",
      "description": "",
      "type": "file",
      "enabled": true,
      "value": ["/C:/Users/...../Downloads/file.mp4"]
    }
  ]
## File Download
-Download unlimited files from the same storage, with each file size capped at 20MB.
-Access the file download endpoint: http://127.0.0.1:8000/api/download-file/{fileid}

Feel free to integrate these functionalities into your projects and make the most out of your Telegram account!
