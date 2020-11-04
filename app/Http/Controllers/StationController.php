<?php

namespace App\Http\Controllers;

use App\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StationController extends Controller
{
    /**
     * Array of timezones, organised by continents and then cities.
     */
    private $timezones;

    public function __construct()
    {
        $this->timezones = [
            'Africa' => [
                '(GMT) Monrovia' => 'Africa/Abidjan',
                '(GMT+01:00) West Central Africa' => 'Africa/Algiers',
                '(GMT+01:00) Windhoek' => 'Africa/Windhoek',
                '(GMT+02:00) Cairo' => 'Africa/Cairo',
                '(GMT+02:00) Harare, Pretoria' => 'Africa/Blantyre',
                '(GMT+03:00) Nairobi' => 'Africa/Addis_Ababa',
            ],
            'America' => [
                '(GMT-10:00) Hawaii-Aleutian' => 'America/Adak',
                '(GMT-10:00) Hawaii' => 'Etc/GMT+10',
                '(GMT-09:00) Alaska' => 'America/Anchorage',
                '(GMT-08:00) Tijuana, Baja California' => 'America/Ensenada',
                '(GMT-08:00) Pacific Time (US & Canada), Los Angeles' => 'America/Los_Angeles',
                '(GMT-07:00) Mountain Time (US & Canada), Denver' => 'America/Denver',
                '(GMT-07:00) Chihuahua, La Paz, Mazatlan' => 'America/Chihuahua',
                '(GMT-07:00) Arizona' => 'America/Dawson_Creek',
                '(GMT-06:00) Saskatchewan, Central America' => 'America/Belize',
                '(GMT-06:00) Guadalajara, Mexico City, Monterrey' => 'America/Cancun',
                '(GMT-06:00) Central Time (US & Canada), Chicago' => 'America/Chicago',
                '(GMT-05:00) Eastern Time (US & Canada), New York' => 'America/New_York',
                '(GMT-05:00) Cuba' => 'America/Havana',
                '(GMT-05:00) Bogota, Lima, Quito, Rio Branco' => 'America/Bogota',
                '(GMT-04:30) Caracas' => 'America/Caracas',
                '(GMT-04:00) Santiago' => 'America/Santiago',
                '(GMT-04:00) La Paz' => 'America/La_Paz',
                '(GMT-04:00) Brazil' => 'America/Campo_Grande',
                '(GMT-04:00) Atlantic Time (Goose Bay)' => 'America/Goose_Bay',
                '(GMT-04:00) Atlantic Time (Canada)' => 'America/Glace_Bay',
                '(GMT-03:30) Newfoundland' => 'America/St_Johns',
                '(GMT-03:00) Araguaína' => 'America/Araguaina',
                '(GMT-03:00) Montevideo' => 'America/Montevideo',
                '(GMT-03:00) Miquelon, St. Pierre' => 'America/Miquelon',
                '(GMT-03:00) Greenland' => 'America/Godthab',
                '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
                '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
                '(GMT-02:00) Mid-Atlantic Time, Noronha' => 'America/Noronha'
            ],
            'Asia' => [
                '(GMT+02:00) Beirut' => 'Asia/Beirut',
                '(GMT+02:00) Gaza' => 'Asia/Gaza',
                '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
                '(GMT+02:00) Syria' => 'Asia/Damascus',
                '(GMT+03:30) Tehran' => 'Asia/Tehran',
                '(GMT+04:00) Abu Dhabi, Muscat' => 'Asia/Dubai',
                '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
                '(GMT+04:30) Kabul' => 'Asia/Kabul',
                '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
                '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
                '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi' => 'Asia/Kolkata',
                '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
                '(GMT+06:00) Astana, Dhaka' => 'Asia/Dhaka',
                '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
                '(GMT+06:30) Yangon (Rangoon)' => 'Asia/Rangoon',
                '(GMT+07:00) Bangkok, Hanoi, Jakarta' => 'Asia/Bangkok',
                '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
                '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi' => 'Asia/Hong_Kong',
                '(GMT+08:00) Irkutsk, Ulaan Bataar' => 'Asia/Irkutsk',
                '(GMT+09:00) Osaka, Sapporo, Tokyo' => 'Asia/Tokyo',
                '(GMT+09:00) Seoul' => 'Asia/Seoul',
                '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
                '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
                '(GMT+11:00) Magadan' => 'Asia/Magadan',
                '(GMT+12:00) Anadyr' => 'Asia/Anadyr'
            ],
            'Atlantic Ocean' => [
                '(GMT-04:00) Faukland Islands' => 'Atlantic/Stanley',
                '(GMT-01:00) Cape Verde Island' => 'Atlantic/Cape_Verde',
                '(GMT-01:00) Azores' => 'Atlantic/Azores'
            ],
            'Australia' => [
                '(GMT+08:00) Perth' => 'Australia/Perth',
                '(GMT+08:45) Eucla' => 'Australia/Eucla',
                '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
                '(GMT+09:30) Darwin' => 'Australia/Darwin',
                '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
                '(GMT+10:00) Hobart' => 'Australia/Hobart',
                '(GMT+10:30) Lord Howe Island' => 'Australia/Lord_Howe'
            ],
            'Europe' => [
                '(GMT) Reykjavik' => 'Etc/GMT',
                '(GMT) Belfast' => 'Europe/Belfast',
                '(GMT) Dublin' => 'Europe/Dublin',
                '(GMT) Lisbon' => 'Europe/Lisbon',
                '(GMT) London' => 'Europe/London',
                '(GMT+01:00) Amsterdam, Bern' => 'Europe/Amsterdam',
                '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
                '(GMT+01:00) Berlin' => 'Europe/Berlin',
                '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
                '(GMT+01:00) Brussels' => 'Europe/Brussels',
                '(GMT+01:00) Madrid' => 'Europe/Madrid',
                '(GMT+01:00) Budapest' => 'Europe/Budapest',
                '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
                '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
                '(GMT+01:00) Paris' => 'Europe/Paris',
                '(GMT+01:00) Prague' => 'Europe/Prague',
                '(GMT+01:00) Rome' => 'Europe/Rome',
                '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
                '(GMT+01:00) Vienna' => 'Europe/Vienna',
                '(GMT+02:00) Minsk' => 'Europe/Minsk',
                '(GMT+03:00) Moscow, St. Petersburg' => 'Europe/Moscow',
                '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
                '(GMT+12:00) Kamchatka' => 'Etc/GMT-12',
            ],
            'Pacific Ocean' => [
                '(GMT-11:00) Midway Island, Samoa' => 'Pacific/Midway',
                '(GMT-09:30) Marquesas Islands' => 'Pacific/Marquesas',
                '(GMT-09:00) Gambier Islands' => 'Pacific/Gambier',
                '(GMT-08:00) Pitcairn Islands' => 'Etc/GMT+8',
                '(GMT-06:00) Easter Island' => 'Chile/EasterIsland',
                '(GMT+11:00) Solomon Islands, New Caledonia' => 'Etc/GMT-11',
                '(GMT+11:30) Norfolk Island' => 'Pacific/Norfolk',
                '(GMT+12:00) Auckland, Wellington' => 'Pacific/Auckland',
                '(GMT+12:00) Fiji, Marshall Islands' => 'Etc/GMT-12',
                '(GMT+12:45) Chatham Islands' => 'Pacific/Chatham',
                '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
                '(GMT+14:00) Kiritimati' => 'Pacific/Kiritimati'
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response|View
     */
    public function index()
    {
        $stations = Station::all();

        return view('stations.index', [
            'stations' => $stations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response|View
     */
    public function create()
    {
        return view('stations.create', [
            'timezones' => $this->timezones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response|View|RedirectResponse
     */
    public function store(Request $request)
    {
        $station = new Station();

        $validated = $request->validate([
            'code' => 'required|unique:stations|string',
            'city' => 'required|string',
            'name' => 'required|string',
            'chartColor' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timezone' => 'required|string',
            'enabled' => 'boolean',
        ]);

        $station->code = $validated['code'];
        $station->city = $validated['city'];
        $station->name = $validated['name'];
        $station->chart_color = $validated['chartColor'];
        $station->latitude = $validated['latitude'];
        $station->longitude = $validated['longitude'];
        $station->timezone = $validated['timezone'];
        $station->enabled = $validated['enabled'];

        $station->save();

        return redirect(route('stations.show', $station->code))
            ->with('success', __('Station has been successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param Station $station
     * @return Response|View
     */
    public function show(Station $station)
    {
        return view('stations.show', [
            'station' => $station
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Station $station
     * @return Response|View
     */
    public function edit(Station $station)
    {


        return view('stations.edit', [
            'station' => $station,
            'timezones' => $this->timezones
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Station $station
     * @return Response|View|RedirectResponse
     */
    public function update(Request $request, Station $station)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'city' => 'required|string',
            'name' => 'required|string',
            'chartColor' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timezone' => 'required|string',
            'enabled' => 'boolean',
        ]);

        $station->code = $validated['code'];
        $station->city = $validated['city'];
        $station->name = $validated['name'];
        $station->chart_color = $validated['chartColor'];
        $station->latitude = $validated['latitude'];
        $station->longitude = $validated['longitude'];
        $station->timezone = $validated['timezone'];
        $station->enabled = $validated['enabled'];

        $station->save();

        return redirect(route('stations.show', $station->code))
            ->with('success', __('Station has been successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Station $station
     * @return Response|View|RedirectResponse
     */
    public function destroy(Station $station)
    {
        $station->delete();

        return redirect(route('stations.index'))->with(
            'success', __('Station :name is successfully deleted.', [
                'name' => $station->name
            ])
        );
    }
}
