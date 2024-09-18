<?php

namespace PhpDevCommunity\FileSystem;

use InvalidArgumentException;
use RuntimeException;

final class TempFile
{
    /**
     * Create a temporary file from base64 data.
     *
     * @param string $base64Data The base64 encoded data
     * @return FileInfo The FileInfo object representing the temporary file
     * @throws RuntimeException If unable to decode base64 data or create the temporary file
     */
    public static function fromBase64(string $base64Data): FileInfo
    {
        if (strncmp($base64Data, 'data:', 5) === 0) {
            $decodedData = file_get_contents($base64Data);
        } else {
            $decodedData = base64_decode($base64Data, true);
        }

        if ($decodedData === false) {
            throw new RuntimeException('Unable to decode base64 data.');
        }

        return self::createTempFileWithContent($decodedData);
    }

    /**
     * Create a temporary file from binary data.
     *
     * @param string $data The binary data
     * @return FileInfo The FileInfo object representing the temporary file
     * @throws RuntimeException If unable to create the temporary file
     */
    public static function fromBinary(string $data): FileInfo
    {
        return self::createTempFileWithContent($data);
    }

    /**
     * Create a temporary file from a resource.
     *
     * @param resource $resource The resource to create the temporary file from
     * @return FileInfo The FileInfo object representing the temporary file
     * @throws InvalidArgumentException If $resource is not a valid resource
     * @throws RuntimeException If unable to write data to the temporary file
     */
    public static function fromResource($resource): FileInfo
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s must be a resource, %s given.', __METHOD__, gettype($resource)));
        }

        $file = self::createTempFile();
        $tmpFile = fopen($file->getRealPath(), 'w');
        if (stream_copy_to_stream($resource, $tmpFile) === false) {
            throw new RuntimeException('Unable to write data to temporary file.');
        }

        fflush($tmpFile);
        fclose($tmpFile);

        return $file;
    }


    /**
     * Create a temporary file with the given data.
     *
     * @return FileInfo The FileInfo object representing the temporary file
     */
    private static function createTempFile(): FileInfo
    {
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'tmp_');
        if ($tmpFilePath === false) {
            throw new RuntimeException('Unable to create temporary file.');
        }
        register_shutdown_function(function () use ($tmpFilePath) {
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }
        });

        return new FileInfo($tmpFilePath);
    }

    private static function createTempFileWithContent(string $content): FileInfo
    {
        $file = self::createTempFile();
        if (file_put_contents($file->getRealPath(), $content) === false) {
            throw new RuntimeException('Unable to write data to temporary file.');
        }
        return $file;
    }
}
