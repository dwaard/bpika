<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileReaderService
 * @package App\Services
 */
class FileReaderService
{

    /**
     * @param string $filename which file to use
     * @param string $disk which disk the file is located on
     * @param string $delimiter which character is used to separate the values
     * @param bool $includeHeader whether the headers should be read
     * @param array|null $headers if headers aren't read, specify headers here
     * @param int $start_row which line to start reading from
     * @return Collection associative array of values by row
     * @throws FileNotFoundException
     */
    public function readCsv(
        string $filename,
        string $disk = 'public',
        string $delimiter = ',',
        bool $includeHeader = true,
        array $headers = null,
        int $start_row = 1
    ) {

        // Get file contents
        $contents = collect(explode("\n", Storage::disk($disk)->get($filename)));

        // Removes any \r
        $contents = $contents->map(function ($item) {
            return str_replace("\r", "", $item);
        });

        // Check if last line is blank
        if ($contents[count($contents) - 1] === '') {
            $contents = $contents->except(count($contents) - 1);
        }

        $current_row = 0;
        $data = collect();
        foreach ($contents as $raw) {
            $row = explode($delimiter, $raw);

            // Get keys for the output
            if ($current_row === 0 and $includeHeader) {
                $keyset = collect($row);
            } elseif ($current_row === 0 and !$includeHeader) {
                $keyset = collect($headers);
            }

            if ($current_row >= $start_row) {
                if (count($row) == $keyset->count()) {
                    // combine header and row into an Associative Array (key=>value)
                    $rowData = $keyset->combine($row)->all();

                    // Add row data to data
                    $data->add($rowData);
                } else {
                    // Add false to indicate an unreadable row
                    $data->add(false);
                }
            }
            $current_row++;
        }
        return $data;
    }
}
