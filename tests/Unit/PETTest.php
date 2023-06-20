<?php

namespace Tests\Unit;

use App\Services\FileReaderService;
use App\Services\PETService;
use App\Models\Station;
use DateTime;
use DateTimeZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PETTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var PETService
     */
    private $PETService;

    /**
     * @var FileReaderService
     */
    private $fileReaderService;

    public function setUp(): void {

        parent::setUp();

        $this->PETService = $this->app->make('App\Services\PETService');
        $this->fileReaderService = $this->app->make('App\Services\FileReaderService');
    }

    public function testCanOpenTestFiles() {

        // Check if files exist
        $inputFileExists = Storage::disk('test')->exists('Binnenstad.csv');
        $outputFileExists = Storage::disk('test')->exists('Binnenstad_TCPET.csv');
        $notExistingFileExists = Storage::disk('test')->exists('notExisting.csv');

        // Assert whether the files exist
        $this->assertTrue(  $inputFileExists,
            'Binnenstad.csv is not present in the storage/app/test directory, but it should.');
        $this->assertTrue(  $outputFileExists,
            'Binnenstad_TCPET.csv is not present in the storage/app/test directory, but it should.');
        $this->assertFalse( $notExistingFileExists,
            'Binnenstad.csv is present in the storage/app/test directory, but it shouldn\'t.');

        // Try reading the files
        $inputFile = $this->fileReaderService->readCsv( 'Binnenstad.csv',
            'test',
            ',',
            true,
            null,
            1);
        $outputFile = $this->fileReaderService->readCsv('Binnenstad_TCPET.csv',
            'test',
            ',',
            true,
            null,
            1);

        // Assert that the files don't contain unreadable rows
        $this->assertFalse($inputFile->contains(false));
        $this->assertFalse($outputFile->contains(false));
    }

    public function testInputShouldBeEqualToOutput() {

        /*
         * This file will be the input to test the PET calculation
         * It has been created from the values in Binnenstad.csv
         * For reference, the file has the following columns:
         * Year (The last two digits of the year of the measurement),
         * Month (The month of the measurement),
         * Day (The day of the measurement),
         * DOY (The Day of the Year of the measurement),
         * Hour (The hour of the measurement),
         * Minute (The minute of the measurement),
         * Decimal Time [H] (Combined time of the day in hours as a decimal number, ranges from 0 to below 24),
         * Decimal Time Year[D] (Combined time of the year in days as a decimal number, ranges from 0 to below 365 or 366, depending on if the year is a leap year or not),
         * Air Pressure [hPa] (Air pressure in hectoPascal),
         * Precipitation [mm] (Amount of precipitation in millimeters),
         * Air Temperature [°C] (Air temperature in Celsius),
         * Relative Humidity [%] (Relative air Humidity in percentages),
         * Dew Point [°C] (Dew point in Celsius),
         * Wind Speed [m/s] (Wind speed in meters per second),
         * Unscreened Solar Radiation [W/m2] (Unscreened solar radiation in Watt per m²),
         * Screened Solar Radiation [W/m2] (Screened solar radiation in Watt per m²),
         * Fraction of Direct Solar Radiation (Fraction of direct solar radiation),
         * Fraction of Diffuse Solar Radiation (Fraction of diffuse solar radiation),
         * Wet Bulb Temperature [°C] (Wet bulb temperature in Celsius),
         * Globe Temperature [°C] (Globe temperature in Celsius),
         * Mean Radiant Temperature [°C] (Mean radiant temperature in Celsius),
         * Wet Bulb Globe Temperature [°C] (Wet bulb globe temperature in Celsius),
         * Cosine Of Zenith Angle (Cosine of the Zenith Angle),
         * Core Temperature [°C] (Core temperature of the average person in Celsius),
         * Skin Temperature [°C] (Skin temperature of the average person in Celsius),
         * Clothes Temperature [°C] (Clothes temperature of the average person in Celsius),
         * Evaporation of Sweat (Evaporation of sweat of the average person),
         * Physiologically Equivalent Temperature [°C] (Physiologically Equivalent Temperature in Celsius)
        */
        $rowDelimiter = "\n";
        $valueDelimiter = ",";
        $stations = Station::all();
        $availableStations = collect([]);
        foreach ($stations as $station) {
            if (Storage::disk('test')->exists($station->name . '.csv')) {
                $availableStations->add($station);
            }
        }
        if (sizeof($availableStations) === 0) {
            $this->assertTrue(true);
        }

        foreach ($availableStations as $station) {

            // Define standard values
            $outputAsString = '';
            $calculatedValues = collect();

            $testFile = $this->fileReaderService->readCsv(  $station->name . '_TCPET.csv',
                'test',
                ',',
                true,
                null,
                1);
            foreach ($testFile as $row) {

                // Replace any '#N/A' values with null value
                foreach ($row as $key => $value) {
                    if ($value === '#N/A') {
                        $row[$key] = null;
                    }
                }

                // Assign the values to local variables for readability
                // The values are currently all strings, so we need to get the right value,
                // because null will be interpreted as 0, check if value is null first
                $year                               = $row['Year'] === null                                         ? null : intval($row['Year']);
                $month                              = $row['Month'] === null                                        ? null : intval($row['Month']);
                $day                                = $row['Day'] === null                                          ? null : intval($row['Day']);
                $DOY                                = $row['DOY'] === null                                          ? null : intval($row['DOY']);
                $hour                               = $row['Hour'] === null                                         ? null : intval($row['Hour']);
                $minute                             = $row['Minute'] === null                                       ? null : intval($row['Minute']);
                $decimalTime                        = $row['Decimal Time [H]'] === null                             ? null : floatval($row['Decimal Time [H]']);
                $decimalYear                        = $row['Decimal Time Year [D]'] === null                        ? null : floatval($row['Decimal Time Year [D]']);
                $airPressure                        = $row['Air Pressure [hPa]'] === null                           ? null : floatval($row['Air Pressure [hPa]']);
                $precipitation                      = $row['Precipitation [mm]'] === null                           ? null : floatval($row['Precipitation [mm]']);
                $airTemperature                     = $row['Air Temperature [°C]'] === null                         ? null : floatval($row['Air Temperature [°C]']);
                $humidity                           = $row['Relative Humidity [%]'] === null                        ? null : floatval($row['Relative Humidity [%]']);
                $dewPoint                           = $row['Dew Point [°C]'] === null                               ? null : floatval($row['Dew Point [°C]']);
                $windSpeed                          = $row['Wind Speed [m/s]'] === null                             ? null : floatval($row['Wind Speed [m/s]']);
                $unscreenedSolarRadiation           = $row['Unscreened Solar Radiation [W/m2]'] === null            ? null : floatval($row['Unscreened Solar Radiation [W/m2]']);
                $screenedSolarRadiation             = $row['Screened Solar Radiation [W/m2]'] === null              ? null : floatval($row['Screened Solar Radiation [W/m2]']);
                $fractionOfDirectSolarRadiation     = $row['Fraction of Direct Solar Radiation'] === null           ? null : floatval($row['Fraction of Direct Solar Radiation']);
                $fractionOfDiffuseSolarRadiation    = $row['Fraction of Diffuse Solar Radiation'] === null          ? null : floatval($row['Fraction of Diffuse Solar Radiation']);
                $wetBulbTemperature                 = $row['Wet Bulb Temperature [°C]'] === null                    ? null : floatval($row['Wet Bulb Temperature [°C]']);
                $globeTemperature                   = $row['Globe Temperature [°C]'] === null                       ? null : floatval($row['Globe Temperature [°C]']);
                $meanRadiantTemperature             = $row['Mean Radiant Temperature [°C]'] === null                ? null : floatval($row['Mean Radiant Temperature [°C]']);
                $wetBulbGlobeTemperature            = $row['Wet Bulb Globe Temperature [°C]'] === null              ? null : floatval($row['Wet Bulb Globe Temperature [°C]']);
                $cosineOfZenithAngle                = $row['Cosine Of Zenith Angle'] === null                       ? null : floatval($row['Cosine Of Zenith Angle']);
                $coreTemperature                    = $row['Core Temperature [°C]'] === null                        ? null : floatval($row['Core Temperature [°C]']);
                $temperatureOfSkin                  = $row['Skin Temperature [°C]'] === null                        ? null : floatval($row['Skin Temperature [°C]']);
                $temperatureOfClothes               = $row['Clothes Temperature [°C]'] === null                     ? null : floatval($row['Clothes Temperature [°C]']);
                $evaporationOfSweat                 = $row['Evaporation Of Sweat'] === null                         ? null : floatval($row['Evaporation Of Sweat']);
                $PET                                = $row['Physiologically Equivalent Temperature [°C]'] === null  ? null : floatval($row['Physiologically Equivalent Temperature [°C]']);

                // Add useful values to the calculated values array
                // Even though these are not calculated values we want to include them in the output file
                $calculatedValues['Year'] = $year;
                $calculatedValues['Month'] = $month;
                $calculatedValues['Day'] = $day;
                $calculatedValues['Hour'] = $hour;
                $calculatedValues['Minute'] = $minute;
                $calculatedValues['Precipitation [mm]'] = $precipitation;
                $calculatedValues['Air Temperature [°C]'] = $airTemperature;
                $calculatedValues['Relative Humidity [%]'] = $humidity;
                $calculatedValues['Wind Speed [m/s]'] = $windSpeed;
                $calculatedValues['Screened Solar Radiation [W/m2]'] = $screenedSolarRadiation;

                // Construct datetime from values
                if ($day !== null and
                    $month !== null and
                    $year !== null and
                    $hour !== null and
                    $minute !== null) {

                    $createdDateTimeString = sprintf('%u-%u-20%u %u:%u',
                        $day, $month, $year, $hour, $minute);
                    $createdDateTime = new DateTime($createdDateTimeString);
                    $createdDateTime->setTimezone(new DateTimeZone('UTC'));

                    // Check DOY and Decimal time
                    // Add one to DOY because date starts at 0, and DOY at 1
                    $calculatedDOY = date('z', $createdDateTime->getTimestamp()) + 1;
                    $this->assertTrue(  $calculatedDOY === $DOY,
                        sprintf('The day of year calculation is off by %u days.',
                            abs($calculatedDOY - $DOY)));
                    $calculatedDecimalTime =    floatval($createdDateTime->format('H')) +
                        (floatval($createdDateTime->format('i')) / 60) +
                        (floatval($createdDateTime->format('s')) / 3600);
                    $this->assertTrue(  $calculatedDecimalTime === $decimalTime,
                        sprintf('The decimal time calculation is off by %u days.',
                            abs($calculatedDecimalTime - $decimalTime)));
                }
                else {
                    $createdDateTimeString = null;
                    $calculatedDOY = null;
                    $calculatedDecimalTime = null;
                }

                // Add calculated datetime values to the calculated values
                $calculatedValues['DOY'] = $calculatedDOY;
                $calculatedValues['Decimal Time [H]'] = $calculatedDecimalTime;

                // Calculate and check intermediate values if they are within acceptable limits
                // Currently, the general accepted limit is: 0,001
                // The accepted limit for the system output is: 0,15
                // The accepted limit for the evaporation of sweat is: 3.5
                // The accepted limit for the PET value is: 0,01
                $acceptedLimit = 0.001;
                $systemAcceptedLimit = 5.1;
                $evaporationOfSweatLimit = 47;
                $PETAcceptedLimit = 1.2;

                // Diffuse and Direct solar radiation
                if ($screenedSolarRadiation !== null and
                    $DOY !== null and
                    $decimalTime !== null and
                    $fractionOfDiffuseSolarRadiation !== null and
                    $fractionOfDirectSolarRadiation !== null) {

                    $calculatedFractionOfDiffuseSolarRadiation = $this->PETService->fr_diffuse( $screenedSolarRadiation,
                        $station->latitude,
                        $station->longitude,
                        $calculatedDOY,
                        $calculatedDecimalTime
                    );
                    $calculatedFractionOfDirectSolarRadiation = 1. - $calculatedFractionOfDiffuseSolarRadiation;
                    $diffuseDifference = abs($calculatedFractionOfDiffuseSolarRadiation - $fractionOfDiffuseSolarRadiation);
                    $this->assertTrue(  $diffuseDifference < $acceptedLimit,
                        sprintf('The fraction of diffuse solar radiation is off by %f Watts per m².',
                            $diffuseDifference - $acceptedLimit));
                    $directDifference = abs($calculatedFractionOfDirectSolarRadiation - $fractionOfDirectSolarRadiation);
                    $this->assertTrue(  $directDifference < $acceptedLimit,
                        sprintf('The fraction of diffuse solar radiation is off by %f Watts per m².',
                            $directDifference - $acceptedLimit));
                }
                else {
                    $calculatedFractionOfDiffuseSolarRadiation = null;
                    $calculatedFractionOfDirectSolarRadiation = null;
                }

                // Add the fractions of solar radiation to the calculated values
                $calculatedValues['Fraction of Diffuse Solar Radiation'] = $calculatedFractionOfDiffuseSolarRadiation;
                $calculatedValues['Fraction of Direct Solar Radiation'] = $calculatedFractionOfDirectSolarRadiation;

                // Cosine of zenith angle
                if ($DOY !== null and
                    $decimalTime !== null and
                    $cosineOfZenithAngle !== null) {

                    $calculatedCosineOfZenithAngle = $this->PETService->sin_solar_elev( $station->latitude,
                        $station->longitude,
                        $DOY,
                        $decimalTime);
                    $czaDifference = abs($calculatedCosineOfZenithAngle - $cosineOfZenithAngle);
                    $this->assertTrue(  $czaDifference < $acceptedLimit,
                        sprintf('The cosine of the zenith angle is off by %f.',
                            $czaDifference - $acceptedLimit));
                }
                else {
                    $calculatedCosineOfZenithAngle = null;
                }

                // Add the cosine of the zenith angle to the calculated values
                $calculatedValues['Cosine Of Zenith Angle'] = $calculatedCosineOfZenithAngle;

                // Globe temperature
                // Currently no urban correction applied
                if ($airTemperature !== null and
                    $humidity !== null and
                    $windSpeed !== null and
                    $screenedSolarRadiation !== null and
                    $calculatedFractionOfDirectSolarRadiation !== null and
                    $calculatedCosineOfZenithAngle !== null and
                    $globeTemperature !== null) {

                    $urbanFactor = 1.;
                    $calculatedGlobeTemperature = $this->PETService->calc_Tglobe(   $airTemperature,
                        $humidity,
                        $urbanFactor * $windSpeed,
                        $screenedSolarRadiation,
                        $calculatedFractionOfDirectSolarRadiation,
                        $calculatedCosineOfZenithAngle);
                    // If calc_Tglobe returns NAN, then set it to null
                    if (is_nan($calculatedGlobeTemperature)) {
                        $calculatedGlobeTemperature = null;
                    }
                    else {
                        $globeTemperatureDifference = abs($calculatedGlobeTemperature - $globeTemperature);
                        $this->assertTrue(  $globeTemperatureDifference < $acceptedLimit,
                            sprintf('The globe temperature is off by %f degrees.',
                                $globeTemperatureDifference - $acceptedLimit));
                    }
                }
                else {
                    $calculatedGlobeTemperature = null;
                }

                // Add the globe temperature to the calculated values
                $calculatedValues['Globe Temperature [°C]'] = $calculatedGlobeTemperature;

                // Median radiant temperature
                if ($calculatedGlobeTemperature !== null and
                    $airTemperature !== null and
                    $windSpeed !== null and
                    $meanRadiantTemperature !== null) {

                    $calculatedMeanRadiantTemperature = $this->PETService->Tmrt($calculatedGlobeTemperature,
                        $airTemperature,
                        $windSpeed);
                    $meanRadiantTemperatureDifference = abs($calculatedMeanRadiantTemperature - $meanRadiantTemperature);
                    $this->assertTrue(  $meanRadiantTemperatureDifference < $acceptedLimit,
                        sprintf('The mean radiant temperature is off by %f degrees.',
                            $meanRadiantTemperatureDifference - $acceptedLimit));
                }
                else {
                    $calculatedMeanRadiantTemperature = null;
                }

                // Add the mean radiant temperature to the calculated values
                $calculatedValues['Mean Radiant Temperature [°C]'] = $calculatedMeanRadiantTemperature;

                // System temperatures
                if ($airTemperature !== null and
                    $calculatedMeanRadiantTemperature !== null and
                    $humidity !== null and
                    $windSpeed !== null and
                    $coreTemperature !== null and
                    $temperatureOfSkin !== null and
                    $temperatureOfClothes !== null and
                    $evaporationOfSweat !== null) {

                    $systemOutput = $this->PETService->system(  $airTemperature,
                        $calculatedMeanRadiantTemperature,
                        $humidity,
                        $windSpeed);
                    $calculatedCoreTemperature = $systemOutput[0];
                    $calculatedTemperatureOfSkin = $systemOutput[1];
                    $calculatedTemperatureOfClothes = $systemOutput[2];
                    $calculatedEvaporationOfSweat = $systemOutput[3];
                    $coreTemperatureDifference = abs($calculatedCoreTemperature - $coreTemperature);
                    $this->assertTrue(  $coreTemperatureDifference < $systemAcceptedLimit,
                        sprintf('The core temperature is off by %f degrees.',
                            $coreTemperatureDifference - $systemAcceptedLimit));
                    $skinTemperatureDifference = abs($calculatedTemperatureOfSkin - $temperatureOfSkin);
                    $this->assertTrue(  $skinTemperatureDifference < $systemAcceptedLimit,
                        sprintf('The skin temperature is off by %f degrees.',
                            $skinTemperatureDifference - $systemAcceptedLimit));
                    $clothesTemperatureDifference = abs($calculatedTemperatureOfClothes - $temperatureOfClothes);
                    $this->assertTrue(  $clothesTemperatureDifference < $systemAcceptedLimit,
                        sprintf('The clothes temperature is off by %f degrees.',
                            $clothesTemperatureDifference - $systemAcceptedLimit));
                    $evaporationOfSweatDifference = abs($calculatedEvaporationOfSweat - $evaporationOfSweat);
                    $this->assertTrue(  $evaporationOfSweatDifference < $evaporationOfSweatLimit,
                        sprintf('The evaporation of sweat is off by %f.',
                            $evaporationOfSweatDifference - $evaporationOfSweatLimit));
                }
                else {
                    $calculatedCoreTemperature = null;
                    $calculatedTemperatureOfSkin = null;
                    $calculatedTemperatureOfClothes = null;
                    $calculatedEvaporationOfSweat = null;
                }

                // Add the system temperatures to the calculated values
                $calculatedValues['Core Temperature [°C]'] = $calculatedCoreTemperature;
                $calculatedValues['Skin Temperature [°C]'] = $calculatedTemperatureOfSkin;
                $calculatedValues['Clothes Temperature [°C]'] = $calculatedTemperatureOfClothes;
                $calculatedValues['Evaporation Of Sweat'] = $calculatedEvaporationOfSweat;

                // Calculate PET value with pet method
                if ($calculatedCoreTemperature !== null and
                    $calculatedTemperatureOfSkin !== null and
                    $calculatedTemperatureOfClothes !== null and
                    $airTemperature !== null and
                    $calculatedEvaporationOfSweat !== null and
                    $PET !== null) {

                    $calculatedPET = $this->PETService->pet($calculatedCoreTemperature,
                        $calculatedTemperatureOfSkin,
                        $calculatedTemperatureOfClothes,
                        $airTemperature,
                        $calculatedEvaporationOfSweat);

                    // Check if calculated PET value is within limits
                    $calculatedPETDifference = abs($calculatedPET - $PET);
                    $this->assertTrue(  $calculatedPETDifference < $PETAcceptedLimit,
                        sprintf('The calculated physiologically equivalent temperature is off by %f degrees.',
                            $calculatedPETDifference - $PETAcceptedLimit));
                }
                else {
                    $calculatedPET = null;
                }

                // Calculate PET value with compute method
                if ($createdDateTimeString !== null and
                    $airTemperature !== null and
                    $screenedSolarRadiation !== null and
                    $humidity !== null and
                    $windSpeed !== null and
                    $PET !== null) {

                    $computedPET = $this->PETService->computePETFromMeasurement($createdDateTimeString,
                        $airTemperature,
                        $screenedSolarRadiation,
                        $humidity,
                        $windSpeed,
                        $station->latitude,
                        $station->longitude);

                    // Check if the computed PET value is within acceptable limits
                    $computedPETDifference = abs($computedPET - $PET);
                    $this->assertTrue(  $computedPETDifference < $PETAcceptedLimit,
                        sprintf('The computed physiologically equivalent temperature is off by %f degrees.',
                            $computedPETDifference - $PETAcceptedLimit));
                }
                else {
                    $computedPET = null;
                }

                // Add PET values to the calculated values
                $calculatedValues['Physiologically Equivalent Temperature using PET method [°C]'] = $calculatedPET;
                $calculatedValues['Physiologically Equivalent Temperature using Compute method [°C]'] = $computedPET;

                // Replace any null values with '#N/A' value
                foreach ($calculatedValues as $key => $value) {
                    if ($value === null) {
                        $calculatedValues[$key] = '#N/A';
                    }
                }

                // First add the headers to the output
                if ($testFile->first() === $row) {
                    foreach ($calculatedValues as $key => $value) {
                        $keyWithDelimiter = array_key_last($calculatedValues->toArray()) === $key ? $key . $rowDelimiter : $key . $valueDelimiter;
                        $outputAsString .= $keyWithDelimiter;
                    }
                }

                // Add row values to output
                foreach ($calculatedValues as $key => $value) {
                    $valueWithDelimiter = array_key_last($calculatedValues->toArray()) === $key ? $value . $rowDelimiter : $value . $valueDelimiter;
                    $outputAsString .= $valueWithDelimiter;
                }
            }

            // Write output to csv file
            Storage::disk('output')->put($station->name . '_output.csv', $outputAsString);

            // Check if output file has been successfully created
            $this->assertTrue(  Storage::disk('output')->exists($station['name'] . '_output.csv'),
                sprintf('The output of %s could not be written to the output file',
                    $station->name));
        }
    }
}
