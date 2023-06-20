<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;

/**
 * Trait that enables Seeder classes to easily read CSV formatted files and seed Models with its data. The
 *
 * User: waar0003
 * Date: 11-4-2018
 * Time: 13:42
 */
trait CsvReadable
{

    /*
     * Full path of the file (normally relative to storage/app) to import
     */
    public $path = "";

    /*
     * The delimiter
     */
    public $delimiter = ',';

    /*
     * the row number of the header to read. Default = null
     *
     * If $header_row is set to null, no header data is read, and the $header attribute is used to determine the
     * columns to import. If the file contains a header row, but is not conform the column names in the model,
     * you should set the $header attribute.
     */
    public $header_row = null;

    /*
     * The first row to import. Default set to 0 (first row)
     */
    public $start_row = 0;

    /*
     * Array with the Model's column names that should be available in the csv file.
     *
     * Set this attribute if the file has no headers or the header row is not conform the column names of the
     * model.
     */
    public $header = null;

    /**
     * Reads the CSV data and calls the `$callback` function for each row.
     *
     * @param $callback
     * @return void
     */
    public function readCsvData($callback)
    {
        $contents = collect(explode("\n", Storage::get($this->path)));
        // Removes any \r
        $contents = $contents->map(function ($item) {
            // remove any remaining CR (\r) and all other non characters
            return str_replace("\r", "", preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $item));
        });

        // Set the keyset when no header row is specified
        if (!$this->header_row) {
            $keyset = collect($this->header);
        }

        $current_row = 0;
        foreach ($contents as $raw) {
            $row = str_getcsv($raw);
            if ($current_row == $this->header_row) {
                $keyset = collect($row);
            }
            if ($current_row >= $this->start_row) {
                $item_count = count($row);
                if ($item_count == $keyset->count()) {
                    // combine header and row into an Associative Array (key=>value)
                    $importData = $keyset->combine($row)->all();

                    // call the callback function
                    $callback($importData);
                } elseif ($row != [""]) {
                    $msg = implode(', ', $row);
                    $this->command->error("CANNOT IMPORT: [$msg]; Size doesn't match");
                }
            }
            $current_row++;
        }
    }
}
