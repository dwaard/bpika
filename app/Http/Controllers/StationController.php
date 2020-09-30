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
        return view('stations.create');
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
            'station' => $station
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
