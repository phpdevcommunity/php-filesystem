# PHP Filesystem

A lightweight PHP library for file system operations, including temporary file creation, file manipulation, and metadata handling using SPL.

## Installation

You can install this library via [Composer](https://getcomposer.org/). Ensure your project meets the minimum PHP version requirement of 7.4.

```bash
composer require phpdevcommunity/php-filesystem
```
## Requirements

- PHP version 7.4 or higher

## Table of Contents

1. [TempFile](#TempFile)
2. [FileInfo](#FileInfo)
3. [FileSynchronizer](#filesynchronizer)
4. [FileExplorer](#fileexplorer)
5. [FileSplitter](#filesplitter)



---
# TempFile

The `TempFile` class provides methods for creating temporary files from base64 data, binary data, and resources.

### fromBase64

Create a temporary file from base64 encoded data.

```php
$base64Data = '...';
$tempFile = TempFile::fromBase64($base64Data);
```
### fromBinary

Create a temporary file from binary data.

```php
$binaryData = '...';
$tempFile = TempFile::fromBinary($binaryData);
```

### fromResource

Create a temporary file from a resource.

```php
$binaryData = '...';
$tempFile = TempFile::fromBinary($binaryData);
```
---
# FileInfo

This guide covers how to use the `FileInfo` class to manage files in PHP. The `FileInfo` class allows for working with file metadata, converting files to various formats (base64, binary, data URLs), and performing file operations like comparison and deletion.

#### 1. **Instantiating the `FileInfo` Class**

To create a new instance of `FileInfo`, pass the path of the file to its constructor.

```php
$filePath = '/path/to/your/file.txt';
$fileInfo = new FileInfo($filePath);
```

This will throw an exception if the file does not exist or if the provided path is invalid.

#### 2. **Getting Basic File Information**

Once you have an instance of `FileInfo`, you can retrieve various details about the file:

- **Get Filename**:
    ```php
    $filename = $fileInfo->getFilename();
    echo "Filename: $filename"; // Outputs: file.txt
    ```

- **Get Real Path**:
    ```php
    $realPath = $fileInfo->getRealPath();
    echo "Real Path: $realPath"; // Outputs: /absolute/path/to/file.txt
    ```

- **Get File Size**:
    ```php
    $size = $fileInfo->getSize();
    echo "File Size: $size bytes";
    ```

- **Get MIME Type**:
    ```php
    $mimeType = $fileInfo->getMimeType();
    echo "MIME Type: $mimeType"; // Example: text/plain
    ```

- **Get File Extension**:
    ```php
    $extension = $fileInfo->getExtension();
    echo "File Extension: $extension"; // Example: txt
    ```

#### 3. **Reading and Converting File Contents**

You can easily convert the file's contents into different formats for storage or transmission:

- **Convert to Base64**:
    ```php
    $base64 = $fileInfo->toBase64();
    echo "Base64 Encoded: $base64";
    ```

- **Convert to Data URL**:
    ```php
    $dataUrl = $fileInfo->toDataUrl();
    echo "Data URL: $dataUrl";
    ```

- **Get File Content as Binary**:
    ```php
    $binaryData = $fileInfo->toBinary();
    echo "Binary Data: $binaryData";
    ```

#### 4. **Working with File Metadata**

Retrieve detailed metadata about the file:

```php
$metadata = $fileInfo->getMetadata();
print_r($metadata);

/* Example Output:
[
    'path' => '/absolute/path/to/file.txt',
    'size' => 1024,  // size in bytes
    'mime_type' => 'text/plain',
    'extension' => 'txt',
    'basename' => 'file.txt',
    'last_modified' => '2024-09-18 12:34:56',
    'creation_date' => '2024-09-17 10:00:00'
]
*/
```

#### 5. **Comparing Files**

You can compare two files by content using their SHA-256 hash values:

```php
$fileInfo1 = new FileInfo('/path/to/file1.txt');
$fileInfo2 = new FileInfo('/path/to/file2.txt');

if ($fileInfo1->compareWith($fileInfo2)) {
    echo "Files are identical";
} else {
    echo "Files are different";
}
```

#### 6. **File Deletion**

To delete the file associated with the `FileInfo` object:

```php
if ($fileInfo->delete()) {
    echo "File deleted successfully";
} else {
    echo "Failed to delete the file";
}
```

Once the `delete` method is called, the file is permanently removed from the filesystem.

#### 7. **Opening Files for Reading or Writing**

You can open the file using `SplFileObject` for reading or writing operations:

```php
$fileObject = $fileInfo->openFile('r');
while (!$fileObject->eof()) {
    echo $fileObject->fgets();
}
```

#### 8. **Error Handling**

If the provided file path is invalid or the file does not exist, the constructor will throw a `RuntimeException`. Always ensure that file paths are validated before instantiating the `FileInfo` class:

```php
try {
    $fileInfo = new FileInfo('/path/to/invalid/file.txt');
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

# FileSynchronizer

The `FileSynchronizer` class provides an easy way to synchronize files between two directories, with optional recursive behavior. It copies files from the source directory to the target directory, ensuring that files are only copied if they are missing or have been updated.

#### 1. **Instantiating the `FileSynchronizer` Class**

To initialize the `FileSynchronizer`, you need to provide the source directory, target directory, and an optional logging function to track operations.

```php
$sourceDir = '/path/to/source';
$targetDir = '/path/to/target';
$synchronizer = new FileSynchronizer($sourceDir, $targetDir, function(array $info) {
    echo sprintf("Action: %s, Source: %s, Target: %s\n", $info['action'], $info['source'], $info['target']);
});
```

Both the source and target must be valid directories, otherwise, the constructor will throw an `InvalidArgumentException`.

#### 2. **Synchronizing Files**

Once initialized, you can start synchronizing files from the source to the target directory using the `sync()` method. If you want to synchronize recursively (i.e., include subdirectories), set the `recursive` parameter to `true`.

```php
$synchronizer->sync(true); // Recursive sync
$synchronizer->sync(false); // Non-recursive sync
```

During synchronization, it copies any files from the source directory that are either missing or outdated in the target directory.

#### 3. **Logging Operations**

You can pass a custom logging function to track the synchronization actions, such as copying files. The logging function receives an array of information containing the action (`copy`), the source file path, and the target file path.

```php
$log = function(array $info) {
    echo sprintf(
        "Action: %s\nSource: %s\nTarget: %s\n",
        $info['action'],
        $info['source'],
        $info['target']
    );
};

$synchronizer = new FileSynchronizer($sourceDir, $targetDir, $log);
$synchronizer->sync(true);
```

If no logging function is provided, the synchronization will proceed without logging any details.

#### 4. **Handling Directories**

- **Recursive Synchronization**: If you choose to synchronize recursively, the `FileSynchronizer` will copy entire directory structures, ensuring all subdirectories and files are copied into the target directory.
- **Non-Recursive Synchronization**: When the `recursive` flag is set to `false`, only the files in the root of the source directory will be copied to the target, ignoring subdirectories.

#### 5. **Copying Files**

The class only copies files when:
- The file does not already exist in the target directory.
- The source file has been modified after the file in the target directory.


Voici une documentation d’utilisation centrée uniquement sur les méthodes publiques de la classe `FileExplorer`, qui pourrait être utile aux développeurs souhaitant utiliser cette classe dans leurs projets.

---

# FileExplorer

The `FileExplorer` class is a utility for exploring directories, listing files, and searching files based on patterns or extensions. This guide will walk through how to use its public methods for common file operations.

#### 1. **Instantiating the `FileExplorer` Class**

To start exploring a directory, you first need to instantiate the `FileExplorer` class with a valid directory path.

```php
$directoryPath = '/path/to/directory';
$explorer = new FileExplorer($directoryPath);
```

> **Note:** The constructor will throw an `InvalidArgumentException` if the provided path is not a valid directory.

#### 2. **Listing All Files and Directories: `listAll()`**

The `listAll()` method returns all files and directories within the specified directory. You can also explore subdirectories by setting the `$recursive` flag to `true`.

```php
// Non-recursive listing
$files = $explorer->listAll(false);

// Recursive listing (including subdirectories)
$files = $explorer->listAll(true);
```

The result is an array where each item represents a file or directory. Each file or directory is provided as an associative array with these keys:
- `path`: The full path to the file or directory.
- `name`: The name of the file or directory.
- `is_directory`: Boolean indicating if it is a directory.
- `size`: The file size in bytes (null for directories).
- `modified_time`: Last modified timestamp.

Example usage:

```php
$files = $explorer->listAll(true);
foreach ($files as $file) {
    echo $file['name'] . ($file['is_directory'] ? ' (Directory)' : ' (File)') . "\n";
}
```

#### 3. **Searching Files by Pattern: `searchByPattern()`**

The `searchByPattern()` method allows you to search for files that match a specific pattern (e.g., `*.txt` for text files). You can perform the search recursively by setting the `$recursive` flag to `true`.

```php
$pattern = '*.txt'; // Example pattern to search for .txt files
$files = $explorer->searchByPattern($pattern, true); // Recursive search
```

This method returns an array of files that match the pattern. The result format is the same as in `listAll()`.

Example usage:

```php
$pattern = '*.html';
$htmlFiles = $explorer->searchByPattern($pattern, true);

foreach ($htmlFiles as $file) {
    echo $file['path'] . " - Last modified: " . $file['modified_time'] . "\n";
}
```

#### 4. **Searching Files by Extension: `searchByExtension()`**

The `searchByExtension()` method provides a simpler way to search for files by their extension. You only need to specify the extension, and it will internally use the `searchByPattern()` method.

```php
// Search for all .txt files (non-recursive)
$txtFiles = $explorer->searchByExtension('txt', false);

// Search for all .html files recursively
$htmlFiles = $explorer->searchByExtension('html', true);
```

This method is ideal when you need to quickly filter files based on their extension without crafting a pattern.

#### 5. **Practical Example**

Here’s a full example demonstrating how to use the `FileExplorer` class to list all files in a directory and then search for files with specific extensions.

```php
// Initialize FileExplorer with the desired directory
$explorer = new FileExplorer('/path/to/directory');

// List all files and directories (non-recursive)
$allFiles = $explorer->listAll(false);
foreach ($allFiles as $file) {
    echo $file['name'] . ($file['is_directory'] ? ' (Directory)' : ' (File)') . "\n";
}

// Search for all .txt files (recursive)
$txtFiles = $explorer->searchByExtension('txt', true);
foreach ($txtFiles as $file) {
    echo "Found .txt file: " . $file['path'] . "\n";
}

// Search for files that match a pattern (recursive)
$pattern = '*.log'; // Look for all .log files
$logFiles = $explorer->searchByPattern($pattern, true);
foreach ($logFiles as $file) {
    echo "Found log file: " . $file['path'] . " - Size: " . $file['size'] . " bytes\n";
}
```
Voici une documentation d'utilisation centrée sur les méthodes publiques de la classe `FileSplitter` pour les développeurs souhaitant diviser des fichiers en plusieurs morceaux.

---

# FileSplitter

The `FileSplitter` class allows developers to split large files into smaller parts, either by specifying the size in megabytes or kilobytes. This guide explains how to use its public methods to perform file splitting operations.

#### 1. **Instantiating the `FileSplitter` Class**

To start using the `FileSplitter`, you need to instantiate it with a `FileInfo` object representing the file you want to split. You can also specify a directory where the split parts will be saved, but if you don't, the parts will be saved in the same directory as the original file.

```php
$fileInfo = new FileInfo('/path/to/large/file.txt');
$splitter = new FileSplitter($fileInfo);
```

If you want to specify a different directory:

```php
$splitter = new FileSplitter($fileInfo, '/path/to/output/directory');
```

> **Note:** The `FileInfo` class is required to provide file details. Make sure the file path is valid, and the file is readable.

#### 2. **Splitting the File by Megabytes: `splitMb()`**

The `splitMb()` method allows you to split a file into smaller parts based on the size in megabytes. Each part will have the specified size unless the file size is not divisible evenly.

```php
$files = $splitter->splitMb(1); // Split into 1 MB chunks
```

The result is an array of `FileInfo` objects, each representing a part of the original file.

Example usage:

```php
$splitFiles = $splitter->splitMb(5); // Split into 5 MB chunks

foreach ($splitFiles as $filePart) {
    echo "Created part: " . $filePart->getRealPath() . "\n";
}
```

#### 3. **Splitting the File by Kilobytes: `splitKb()`**

If you prefer to specify the size in kilobytes, you can use the `splitKb()` method. This works similarly to `splitMb()` but operates in kilobytes.

```php
$files = $splitter->splitKb(512); // Split into 512 KB chunks
```

This also returns an array of `FileInfo` objects representing each part.

Example usage:

```php
$splitFiles = $splitter->splitKb(200); // Split into 200 KB chunks

foreach ($splitFiles as $filePart) {
    echo "Created part: " . $filePart->getRealPath() . "\n";
}
```

#### 4. **General File Splitting: `split()`**

The `split()` method is the core function that both `splitMb()` and `splitKb()` rely on. You can directly use this method to specify any custom size for the chunks in bytes.

```php
$chunkSizeInBytes = 1048576; // 1 MB in bytes
$files = $splitter->split($chunkSizeInBytes);
```

Like the other methods, it returns an array of `FileInfo` objects for the file parts.

#### 5. **Practical Example**

Here’s a complete example that demonstrates how to split a file into parts and delete the parts afterward.

```php
// Initialize FileInfo and FileSplitter
$fileInfo = new FileInfo('/path/to/large/file.txt');
$splitter = new FileSplitter($fileInfo);

// Split the file into 1 MB chunks
$splitFiles = $splitter->splitMb(1);

echo "File has been split into " . count($splitFiles) . " parts.\n";
```

---
## License

This library is open-source software licensed under the [MIT license](LICENSE).
