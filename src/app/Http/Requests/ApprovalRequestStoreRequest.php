<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequestStoreRequest extends FormRequest
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
            'request_start_at'  => ['required','date_format:H:i','before:request_finish_at'],
            'request_finish_at' => ['required','date_format:H:i','after:request_start_at'],
            'reason'              => ['required'],

        // ーーー 休憩データの配列バリデーション ーーー
            'rests.*.request_rest_start_at'         => [
                'nullable',
                'required_with:rests.*.request_rest_finish_at',
                'date_format:H:i',
                'after:request_start_at',
                'before:request_finish_at'],

            'rests.*.request_rest_finish_at'        => [
                'nullable',
                'required_with:rests.*.request_rest_start_at',
                'date_format:H:i',
                'after:rests.*.request_rest_start_at',
                'before:request_finish_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'request_start_at.required'   => '出勤時間は必須項目です。',
            'request_start_at.date_format'=> '出勤時間は「時:分」の形式で入力してください。',
            'request_start_at.before'     => '出勤時間もしくは退勤時間が不適切な値です',
            
            'request_finish_at.required'  => '退勤時間は必須項目です。',
            'request_finish_at.date_format'=> '出勤時間は「時:分」の形式で入力してください。',
            'request_finish_at.after'     => '出勤時間もしくは退勤時間が不適切な値です',
            
            'reason.required'               => '備考を記入してください。',

            'rests.*.request_rest_start_at.required_with'    => '休憩開始時間を入力してください。',
            'rests.*.request_rest_start_at.date_format'    => '休憩時間は「時:分」の形式で入力してください。',
            'rests.*.request_rest_start_at.after'    => '休憩時間が不適切な値です',
            'rests.*.request_rest_start_at.before'    =>'休憩時間が不適切な値です',

            
            'rests.*.request_rest_finish_at.required_with'    => '休憩終了時間を入力してください。',
            'rests.*.request_rest_finish_at.date_format'    => '休憩時間は「時:分」の形式で入力してください。',
            'rests.*.request_rest_finish_at.before'    => '休憩時間もしくは退勤時間が不適切な値です',
        ];
    }
    
}
