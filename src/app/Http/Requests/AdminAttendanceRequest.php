<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_at'  => ['required','date_format:H:i','before:finish_at'],
            'finish_at' => ['required','date_format:H:i','after:start_at'],
            'reason'              => ['required','string'],

        // ーーー 休憩データの配列バリデーション ーーー
            //'rests.*.rest_start_at'         => ['nullable','required_with:rests.*.rest_finish_at','date_format:H:i','after:start_at','before:finish_at'],
            'rests.*.rest_finish_at'        => ['nullable','required_with:rests.*.rest_start_at','date_format:H:i','before:finish_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_at.required'   => '出勤時間は必須項目です。',
            'start_at.date_format'=> '出勤時間は「時:分」の形式で入力してください。',
            'start_at.before'     => '出勤時間もしくは退勤時間が不適切な値です',
            
            'finish_at.required'  => '退勤時間は必須項目です。',
            'finish_at.date_format'=> '出勤時間は「時:分」の形式で入力してください。',
            'finish_at.after'     => '出勤時間もしくは退勤時間が不適切な値です',
            
            'reason.required'               => '備考を記入してください。',

            'rests.*.rest_start_at.required_with'    => '休憩開始時間を入力してください。',
            'rests.*.rest_start_at.date_format'    => '休憩時間は「時:分」の形式で入力してください。',
            'rests.*.rest_start_at.after'    => '休憩時間が不適切な値です',
            'rests.*.rest_start_at.before'    =>'休憩時間が不適切な値です',

            
            'rests.*.rest_finish_at.required_with'    => '休憩終了時間を入力してください。',
            'rests.*.rest_finish_at.date_format'    => '休憩時間は「時:分」の形式で入力してください。',
            'rests.*.rest_finish_at.before'    => '休憩時間もしくは退勤時間が不適切な値です',
        ];
    }
}
