<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $stations = Station::orderBy('enabled', 'desc')
            ->orderBy('city')->orderBy('name')->get();

        return view('stations.index', [
            'stations' => $stations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function create(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('stations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|unique:stations|string',
            'city' => 'required|string',
            'name' => 'required|string',
            'chart_color' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timezone' => 'required|string'
        ]);

        $station = Station::create($validated);

        return redirect()->route('stations.index')
            ->with('success', __(':item is created successfully', ['item' => $station->label]));
    }

    /**
     * Display the specified resource.
     *
     * @param Station $station
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function show(Station $station): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('stations.show', compact('station'));
    }

    /**
     * Display a narrow casting page about the specified resource.
     *
     * @param Station $station
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function cast(Station $station): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $latest = $station->measurements()->latest('created_at')->first();

        return view('stations.cast', compact('station', 'latest'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Station $station
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function edit(Station $station): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('stations.edit', compact('station'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Station $station
     * @return RedirectResponse
     */
    public function update(Request $request, Station $station): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', Rule::unique('stations')->ignore($station)],
            'city' => 'required|string',
            'name' => 'required|string',
            'chart_color' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timezone' => 'required|string'
        ]);

        $station->update($validated);

        return redirect()->route('stations.show', $station)
            ->with('success', __(':item is updated successfully', ['item' => $station->label]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Station $station
     * @return RedirectResponse
     */
    public function enable(Station $station): RedirectResponse
    {
        $station->update([
            'enabled' => true
        ]);

        return redirect()->route('stations.show', $station)
            ->with('success', __(':item is updated successfully', ['item' => $station->label]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Station $station
     * @return RedirectResponse
     */
    public function destroy(Station $station): RedirectResponse
    {
        $station->update([
            'enabled' => false
        ]);

        return redirect()->route('stations.show', $station)
            ->with('success', __(':item is disabled successfully', ['item' => $station->label]));
    }
}
