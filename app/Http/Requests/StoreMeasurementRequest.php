<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMeasurementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'station_name' => 'required|exists:stations,code',
            'th_temp' => 'sometimes',
            'th_hum' => 'sometimes',
            'th_dew' => 'sometimes',
            'th_heatindex' => 'sometimes',
            'thb_temp' => 'sometimes',
            'thb_hum' => 'sometimes',
            'thb_dew' => 'sometimes',
            'thb_press' => 'sometimes',
            'thb_seapress' => 'sometimes',
            'wind_wind' => 'sometimes',
            'wind_avgwind' => 'sometimes',
            'wind_dir' => 'sometimes',
            'wind_chill' => 'sometimes',
            'rain_rate' => 'sometimes',
            'rain_total' => 'sometimes',
            'uv_index' => 'sometimes',
            'sol_rad' => 'sometimes',
            'sol_evo' => 'sometimes',
            'sun_total' => 'sometimes',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
